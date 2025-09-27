<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/***  Mobile ***/

class Pick_list extends PS_Controller
{
  public $menu_code = 'ICODPL';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'Pick List';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'mobile/pick_list';
    $this->load->model('inventory/pick_list_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/channels_model');
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/prepare_model');

    $this->load->helper('channels');
    $this->load->helper('warehouse');
    $this->load->helper('zone');
    $this->load->helper('pick_list');
    $this->load->library('user_agent');

    $this->is_mobile = $this->agent->is_mobile();
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pl_code', ''),
      'warehouse' => get_filter('warehouse', 'pl_warehouse', 'all'),
      'zone' => get_filter('zone', 'pl_zone', 'all'),
      'channels' => get_filter('channels', 'pl_channels', 'all'),
      'user' => get_filter('user', 'pl_user', 'all'),
      'status' => get_filter('status', 'pl_status', 'all'),
      'is_exported' => get_filter('is_exported', 'pl_is_exported', 'all'),
      'from_date' => get_filter('from_date', 'pl_from_date', ''),
      'to_date' => get_filter('to_date', 'pl_to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
  		$rows = $this->pick_list_model->count_rows($filter, $this->is_mobile);
      $filter['data'] = $this->pick_list_model->get_list($filter, $perpage, $this->uri->segment($this->segment), $this->is_mobile);
  		$init = mobile_pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);

      $this->load->view('mobile/pick_list/pick_list', $filter);
    }
  }


  public function process($code)
  {
    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $doc = $this->pick_list_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'R')
        {
          $arr = array(
            'status' => 'Y',
            'update_user' => $this->_user->uname
          );

          $this->pick_list_model->update($doc->code, $arr);
        }

        $incomplete = $this->pick_list_model->get_incomplete_rows($doc->code);
        $complete = $this->pick_list_model->get_complete_rows($doc->code); //--- reload later
        $totalProcess = $this->pick_list_model->get_total_process_qty($doc->code);
        $totalReleaseQty = empty($totalProcess) ? 0 : $totalProcess->release_qty; //--- sum of release qty
        $totalPickQty = empty($totalProcess) ? 0 : $totalProcess->pick_qty; //-- sum of picked qty

        if( ! empty($incomplete))
        {
          foreach($incomplete as $rs)
          {
            $rs->barcode = $this->products_model->get_barcode($rs->product_code);
            $rs->stock_in_zone = $this->get_stock_in_zone($rs->product_code, $doc->warehouse_code);
            $rs->balance = $rs->release_qty - $rs->pick_qty;
          }
        }

        $ds = array(
          'doc' => $doc,
          'incomplete' => $incomplete,
          'complete' => $complete,
          'totalReleaseQty' => $totalReleaseQty,
          'totalPickQty' => $totalPickQty,
          'transection' => $this->pick_list_model->get_pick_transections($doc->code),
          'finished' => empty($incomplete) ? TRUE : FALSE
        );

        $this->load->view('mobile/pick_list/pick_process', $ds);
      }
      else
      {
        $this->error_page();
      }
    }
    else
    {
      $this->deny_page();
    }
  }


  public function do_picking()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $status = $this->pick_list_model->get_status_by_id($ds->id);

      if($status === 'Y')
      {
        $row = $this->pick_list_model->get_row($ds->row_id);

        if( ! empty($row))
        {
          if($row->line_status == 'O')
          {
            $sum_qty = $row->pick_qty + $ds->qty;

            if($sum_qty > $row->release_qty)
            {
              $sc = FALSE;
              $this->error = "จำนวนเกินที่กำหนด";
            }

            if($sc === TRUE)
            {
              $stock = $this->stock_model->get_stock_zone($ds->zone_code, $row->product_code);

              if($ds->qty > $stock)
              {
                $sc = FALSE;
                $this->error = "สต็อกในโซนไม่เพียงพอ";
              }
            }

            if($sc === TRUE)
            {
              $trans = $this->pick_list_model->get_exists_transection($row->pick_id, $row->product_code, $ds->zone_code);

              $this->db->trans_begin();

              if( ! empty($trans))
              {
                $qty = $trans->qty + $ds->qty;

                $arr = array(
                  'qty' => $qty,
                  'user' => $this->_user->uname
                );

                if( ! $this->pick_list_model->update_transection($trans->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update pick transection";
                }
              }
              else
              {
                $arr = array(
                  'pick_id' => $row->pick_id,
                  'pick_code' => $row->pick_code,
                  'product_code' => $row->product_code,
                  'product_name' => $row->product_name,
                  'zone_code' => $ds->zone_code,
                  'qty' => $ds->qty,
                  'user' => $this->_user->uname
                );

                if( ! $this->pick_list_model->add_transection($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert pick transection";
                }
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'pick_qty' => $sum_qty,
                  'valid' => $row->release_qty == $sum_qty ? 1 : 0
                );

                if( ! $this->pick_list_model->update_row($ds->row_id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update pick row";
                }
              }

              if($sc === TRUE)
              {
                $this->db->trans_commit();
              }
              else
              {
                $this->db->trans_rollback();
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid row status";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบรายการ";
        }
      }
      else
      {
        $sc = FALSE;
        set_error('status');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'pick_qty' => $sc === TRUE ? $sum_qty : NULL
    );

    echo json_encode($arr);
  }


  public function finish_pick()
  {
    $sc = TRUE;

    $ex = 0;

    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->pick_list_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'Y')
        {
          $this->load->model('inventory/movement_model');

          $to_warehouse_code = $this->zone_model->get_warehouse_code($doc->zone_code);
          $from_warehouse_code = $doc->warehouse_code;

          if( ! empty($to_warehouse_code) && ! empty($from_warehouse_code))
          {
            $this->db->trans_begin();

            //---- close picklist
            $arr = array(
              'status' => 'C',
              'shipped_date' => now(),
              'update_user' => $this->_user->uname
            );

            if( ! $this->pick_list_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update document status";
            }

            //--- close pick rows
            if($sc === TRUE)
            {
              if( ! $this->pick_list_model->update_rows($code, ['line_status' => 'C']))
              {
                $sc = FALSE;
                $this->error = "Failed to update pick rows status";
              }
            }

            //--- close pick details
            if($sc === TRUE)
            {
              if( ! $this->pick_list_model->update_details($code, ['line_status' => 'C']))
              {
                $sc = FALSE;
                $this->error = "Failed to update pick details status";
              }
            }

            //---- insert movement
            if($sc === TRUE)
            {
              $trans = $this->pick_list_model->get_pick_transections($code);

              if( ! empty($trans))
              {
                foreach($trans as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  if( ! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, ($rs->qty * -1)))
                  {
                    $sc = FALSE;
                    $this->error = "ตัดสต็อกขาออกไม่สำเร็จ";
                  }

                  if($sc === TRUE)
                  {
                    //--- move out
                    $arr = array(
                      'reference' => $code,
                      'warehouse_code' => $from_warehouse_code,
                      'zone_code' => $rs->zone_code,
                      'product_code' => $rs->product_code,
                      'move_in' => 0,
                      'move_out' => $rs->qty,
                      'date_add' => $doc->date_add
                    );

                    if( ! $this->movement_model->add($arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to create movement";
                    }
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, $rs->qty))
                    {
                      $sc = FALSE;
                      $this->error = "เพิ่มสต็อกขาเข้าไม่สำเร็จ";
                    }
                  }

                  if($sc === TRUE)
                  {
                    //--- move in
                    $arr = array(
                      'reference' => $code,
                      'warehouse_code' => $to_warehouse_code,
                      'zone_code' => $doc->zone_code,
                      'product_code' => $rs->product_code,
                      'move_in' => $rs->qty,
                      'move_out' => 0,
                      'date_add' => $doc->date_add
                    );

                    if( ! $this->movement_model->add($arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to create movement";
                    }
                  }
                } //--- end foreach
              } //-- ! empty($trans)
            } //--- $sc = TRUE

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid warehouse code";
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }

    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function get_stock_in_zone($item_code, $warehouse = NULL)
  {
    $sc = "ไม่มีสินค้า";

    $stock = $this->stock_model->get_stock_in_zone($item_code, $warehouse);

    if( ! empty($stock))
    {
      $sc = "";
      $i = 1;
      foreach($stock as $rs)
      {
        $picked = $this->pick_list_model->get_picked_zone($rs->zone_code, $item_code);
        $qty = $rs->qty - $picked;

        if($qty > 0)
        {
          $sc .= $i == 1 ? $rs->name.' : '.($qty) : ' | '.$rs->name.' : '.$qty;
          $i++;
        }
      }
    }

    return empty($sc) ? 'ไม่พบสินค้า' : $sc;
  }


  public function get_stock_zone($zone_code, $item_code)
  {
    $stock = $this->stock_model->get_stock_zone($zone_code, $item_code); //-- stock in zone
    $picked = $this->pick_list_model->get_picked_zone($zone_code, $item_code); //-- pick list transection

    return $stock - $picked;
  }


  public function get_zone_code()
  {
    $ds = array();
    $sc = TRUE;
    $zone_code = trim($this->input->get('zone_code'));
    $warehouse_code = trim($this->input->get('warehouse_code'));

    if($zone_code)
    {
      $zone = $this->zone_model->get_zone($zone_code, $warehouse_code);

      if( ! empty($zone))
      {
        $ds = array(
          'code' => $zone->code,
          'name' => $zone->name
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบโซน";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'zone' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_pick_row()
  {
    $sc = TRUE;
    $id = $this->input->post('row_id');
    $warehouse_code = $this->input->post('warehouse_code');

    $ds = [];
    $stock_in_zone = "";
    $row = $this->pick_list_model->get_row($id);

    if( ! empty($row))
    {
      $ds = array(
        'pick_qty' => $row->pick_qty,
        'balance_qty' => $row->release_qty - $row->pick_qty,
        'stock_in_zone' => $this->get_stock_in_zone($row->product_code, $warehouse_code)
      );
    }
    else
    {
      $sc = FALSE;
      $this->error = "Pick row not found !";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_incomplete_table($code)
  {
    $sc = TRUE;
    $ds = [];

    $doc = $this->pick_list_model->get($code);

    if( ! empty($doc))
    {
      $incomplete = $this->pick_list_model->get_incomplete_rows($code);

      if( ! empty($incomplete))
      {
        $no = 1;

        foreach($incomplete as $rs)
        {
          $ds[] = (object) array(
            'no' => $no,
            'id' => $rs->id,
            'barcode' => $this->products_model->get_barcode($rs->product_code),
            'product_code' => $rs->product_code,
            'product_name' => $rs->product_name,
            'release_qty' => $rs->release_qty,
            'pick_qty' => $rs->pick_qty,
            'balance' => $rs->release_qty - $rs->pick_qty,
            'stock_in_zone' => $this->get_stock_in_zone($rs->product_code, $doc->warehouse_code)
          );

          $no++;
        }
      }
      else
      {
        $ds[] = array('nodata' => 'nodata');
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document number";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function get_complete_table($code)
  {
    $sc = TRUE;
    $ds = [];
    $complete = $this->pick_list_model->get_complete_rows($code);

    if( ! empty($complete))
    {
      $no = 1;

      foreach($complete as $rs)
      {
        $ds[] = (object) array(
          'no' => $no,
          'id' => $rs->id,
          'product_code' => $rs->product_code,
          'product_name' => $rs->product_name,
          'releaseQty' => $rs->release_qty,
          'pickQtty' => $rs->pick_qty
        );

        $no++;
      }
    }
    else
    {
      $ds[] = array('nodata' => 'nodata');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function get_transection_table($code)
  {
    $sc = TRUE;
    $ds = [];
    $trans = $this->pick_list_model->get_pick_transections($code);

    if( ! empty($trans))
    {
      $no = 1;

      foreach($trans as $rs)
      {
        $ds[] = (object) array(
          'no' => $no,
          'id' => $rs->id,
          'product_code' => $rs->product_code,
          'product_name' => $rs->product_name,
          'qty' => $rs->qty,
          'zone_code' => $rs->zone_code,
          'user' => $rs->user,
          'date_upd' => thai_date($rs->date_upd, TRUE),
          'valid' => $rs->is_complete
        );

        $no++;
      }
    }
    else
    {
      $ds[] = array('nodata' => 'nodata');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function delete_transection()
  {
    $sc = TRUE;
    $id = $this->input->get('id');

    $trans = $this->pick_list_model->get_transection($id);

    if( ! empty($trans))
    {
      $doc = $this->pick_list_model->get_by_id($trans->pick_id);

      if( ! empty($doc))
      {
        if($doc->status == 'Y' OR $doc->status == 'R')
        {
          $row = $this->pick_list_model->get_pick_row($trans->pick_code, $trans->product_code);

          if( ! empty($row))
          {
            if($row->pick_qty < $trans->qty OR $row->release_qty < $trans->qty)
            {
              $sc = FALSE;
              $this->error = "Invalid qty :release qty = {$row->release_qty} , pick qty = {$row->pick_qty}, transection qty = {$trans->qty}";
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              //-- delete transection
              if( ! $this->pick_list_model->delete_transection($trans->id))
              {
                $sc = FALSE;
                $this->error = "Failed to delete transection";
              }

              //--- update pick_row
              if($sc === TRUE)
              {
                $arr = array(
                  'pick_qty' => $row->pick_qty - $trans->qty,
                  'valid' => 0
                );

                if( ! $this->pick_list_model->update_row($row->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update pick qty";
                }
              }

              if($sc === TRUE)
              {
                $this->db->trans_commit();
              }
              else
              {
                $this->db->trans_rollback();
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Pick row not found";
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid document status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Document not found!";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Transection not found !";
    }

    $this->_response($sc);
  }


  public function clear_filter()
  {
    $filter = array(
      'pl_code',
      'pl_warehouse',
      'pl_zone',
      'pl_channels',
      'pl_user',
      'pl_status',
      'pl_is_exported',
      'pl_from_date',
      'pl_to_date',
    );

    return clear_filter($filter);
  }
} //--- end class
?>
