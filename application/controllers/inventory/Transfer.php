<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends PS_Controller
{
  public $menu_code = 'ICTRWH';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'โอนสินค้าระหว่างคลัง';
  public $require_remark = 1;
  public $segment = 4;
  public $is_mobile = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/transfer';
    $this->load->model('inventory/transfer_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');
    $this->load->helper('warehouse');
    $this->load->helper('transfer');
    $this->is_mobile = $this->agent->is_mobile();
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'tr_code', ''),
      'to_warehouse' => get_filter('to_warehouse', 'tr_to_warehouse', 'all'),
      'user' => get_filter('user', 'tr_user', 'all'),
      'status' => get_filter('status', 'tr_status', 'all'),
      'doc_num' => get_filter('doc_num', 'tr_doc_num', ''),
      'from_date' => get_filter('fromDate', 'tr_fromDate', ''),
      'to_date' => get_filter('toDate', 'tr_toDate', ''),
      'is_export' => get_filter('is_export', 'tr_is_export', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		$rows = $this->transfer_model->count_rows($filter);
    $filter['data'] = $this->transfer_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$this->pagination->initialize($init);
    $this->load->view('transfer/transfer_list', $filter);
  }


  public function add_new()
  {
    $whsCode = getConfig('DEFAULT_WAREHOUSE');
    $whs = $this->warehouse_model->get($whsCode);
    $ds = array(
      'whsCode' => ! empty($whs) ? $whs->code : NULL,
      'whsName' => ! empty($whs) ? $whs->name : NULL
    );

    $this->load->view('transfer/transfer_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $code = NULL;

    if($this->pm->can_add)
    {
      if( ! empty($ds))
      {
        $date_add = db_date($ds->date_add, TRUE);
        $shipped_date = empty($ds->shipped_date) ? NULL : db_date($ds->shipped_date, TRUE);
        $code = $this->get_new_code($date_add);

        $arr = array(
          'date_add' => $date_add,
          'shipped_date' => $shipped_date,
          'code' => $code,
          'from_warehouse' => $ds->from_warehouse,
          'to_warehouse' => $ds->to_warehouse,
          'remark' => get_null($ds->remark),
          'user' => $this->_user->uname
        );

        if( ! $this->transfer_model->add($arr))
        {
          $sc = FALSE;
          set_error('insert');
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $code
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $doc = $this->transfer_model->get($code);

    if( ! empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);

    if( ! empty($details))
    {
      $zoneName = [];

      foreach($details as $rs)
      {
        if(empty($zoneName[$rs->from_zone]))
        {
          $zoneName[$rs->from_zone] = $this->zone_model->get_name($rs->from_zone);
        }

        $rs->from_zone_name =  $zoneName[$rs->from_zone];
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('transfer/transfer_edit', $ds);
  }


  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      if( ! empty($ds) && ! empty($ds->code))
      {
        $doc = $this->transfer_model->get($ds->code);

        if( ! empty($doc))
        {
          if($doc->status == 'P')
          {
            $date_add = db_date($ds->date_add, TRUE);
            $shipped_date = empty($ds->shipped_date) ? NULL : db_date($ds->shipped_date, TRUE);

            $arr = array(
              'date_add' => $date_add,
              'shipped_date' => $shipped_date,
              'to_warehouse' => $ds->to_warehouse,
              'remark' => get_null($ds->remark),
              'update_user' => $this->_user->uname
            );

            if( ! $this->transfer_model->update($ds->code, $arr))
            {
              $sc = FALSE;
              set_error('update');
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
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function view_detail($code)
  {
    $ds = array(
      'doc' => $this->transfer_model->get($code),
      'details' => $this->transfer_model->get_details($code)
    );

    $this->load->view('transfer/transfer_view_detail', $ds);
  }


  public function get_transfer_zone($warehouse = NULL)
  {
    $txt = $_REQUEST['term'];
    $ds = [];

    $this->db->select('code, name');

    if( ! empty($warehouse))
    {
      $this->db->where('warehouse_code', $warehouse);
    }

    if($txt != '*')
    {
      $this->db
      ->group_start()
      ->like('code', $txt)
      ->or_like('name', $txt)
      ->group_end();
    }

    $rs = $this->db->order_by('code', 'ASC')->limit(100)->get('zone');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $ds[] = $rd->code.' | '.$rd->name;
      }
    }
    else
    {
      $ds = 'ไม่พบโซน';
    }

    echo json_encode($ds);
  }


  public function get_product_in_zone()
  {
    $sc = TRUE;
    $res = [];

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->zone_code) && ! empty($ds->item_code))
    {
      $item_code = $ds->item_code == '*' ? NULL : $ds->item_code;
      $stock = $this->stock_model->get_all_stock_in_zone($ds->zone_code, $item_code);

      if( ! empty($stock))
      {
        $no = 1;

        foreach($stock as $rs)
        {
          if($rs->qty > 0)
          {
            $res[] = array(
              'no' => $no,
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'qty' => round($rs->qty, 2),
              'qtyLabel' => number($rs->qty, 2)
            );

            $no++;
          }
        }
      }
      else
      {
        $res[] = ['nodata' => 'nodata'];
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
      'data' => $res
    );

    echo json_encode($arr);
  }


  public function add_to_transfer()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->zone_code) && ! empty($ds->items))
    {
      $doc = $this->transfer_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          if( ! empty($ds->items))
          {
            $this->db->trans_begin();

            foreach($ds->items as $rs)
            {
              if($sc === FALSE) { break; }

              $arr = array(
                'transfer_code' => $doc->code,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'from_zone' => $ds->zone_code,
                'warehouse_code' => $doc->from_warehouse,
                'qty' => $rs->qty
              );

              $row = $this->transfer_model->get_detail_row($doc->code, $rs->product_code, $ds->zone_code);

              if( ! empty($row))
              {
                if( ! $this->transfer_model->update_detail($row->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update item row {$rs->product_code} : {$ds->zone_code}";
                }
              }
              else
              {
                if( ! $this->transfer_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert item rows {$rs->product_code} : {$ds->zone_code}";
                }
              }
            } // end foreach

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

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_transfer_table($code)
  {
    $sc = TRUE;
    $ds = [];
    $details = $this->transfer_model->get_details($code);

    if( ! empty($details))
    {
      $no = 1;
      $totalQty = 0;

      foreach($details as $rs)
      {
        $ds[] = array(
          'id' => $rs->id,
          'no' => $no,
          'product_code' => $rs->product_code,
          'product_name' => $rs->product_name,
          'from_zone' => $rs->from_zone,
          'qty' => $rs->qty,
          'qtyLabel' => number($rs->qty)
        );

        $no++;
        $totalQty += $rs->qty;
      } //--- end foreach

      $ds[] = array('totalQty' => number($totalQty));
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function delete_detail()
  {
    $sc = TRUE;

    $code = $this->input->post('code');
    $ids = json_decode($this->input->post('ids'));

    if( ! empty($ids))
    {
      $doc = $this->transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          if( ! $this->transfer_model->delete_rows($ids))
          {
            $sc = FALSE;
            $this->error = "Failed to delete items";
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
        $this->error = "Invalid document number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $this->_response($sc);
  }


  public function save()
  {
    $sc = TRUE;
    $ex = 1;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->transfer_model->get($code);

      if( ! empty($doc))
      {
        $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

        if($doc->status == 'P')
        {
          $details = $this->transfer_model->get_details($code);

          if( ! empty($details))
          {
            $this->load->model('inventory/movement_model');

            $this->db->trans_begin();

            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              $stock_qty = $this->stock_model->get_stock_zone($rs->from_zone, $rs->product_code);

              if($rs->qty > $stock_qty)
              {
                $sc = FALSE;
                $this->error = "Insufficient stock for {$rs->product_code} in {$rs->from_zone} ($rs->qty / $stock_qty)";
              }

              if($sc === TRUE)
              {
                if( ! $this->stock_model->update_stock_zone($rs->from_zone, $rs->product_code, ($rs->qty * -1)))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update item stock for {$rs->product_code} in {$rs->from_zone}";
                }
              }

              if($sc === TRUE)
              {
                $movement = array(
                  'reference' => $doc->code,
                  'warehouse_code' => $rs->warehouse_code,
                  'zone_code' => $rs->from_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => 0,
                  'move_out' => $rs->qty,
                  'date_add' => $date_add
                );

                if( ! $this->movement_model->add($movement))
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert stock movement for {$rs->product_code} in {$rs->from_zone}";
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->transfer_model->update_detail($rs->id, ['line_status' => 'C']))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update line status";
                }
              }
            } // end foreach

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 'C',
                'update_user' => $this->_user->uname,
                'shipped_date' => empty($doc->shipped_date) ? now() : $doc->shipped_date
              );

              if( ! $this->transfer_model->update($doc->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
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

            if($sc === TRUE)
            {
              if(is_true(getConfig('WRX_API')) && is_true(getConfig('WRX_TR_INTERFACE')))
              {
                $this->load->library('wrx_transfer_api');

                if( ! $this->wrx_transfer_api->export_transfer($doc->code))
                {
                  $this->error = $this->wrx_transfer_api->error;
                  $ex = 0;
                }
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการโอนย้าย";
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
      'message' => $this->error,
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function cancel()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $reason = trim($this->input->post('reason'));

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->transfer_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 'P' OR ($this->_SuperAdmin && $doc->status != 'D'))
          {
            $this->load->model('inventory/movement_model');

            $details = $this->transfer_model->get_details($code);

            $this->db->trans_begin();

            if( ! empty($details))
            {
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                if($rs->line_status == 'C')
                {
                  if( ! $this->stock_model->update_stock_zone($rs->from_zone, $rs->product_code, $rs->qty))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to rollback stock qty : {$rs->product_code} to {$rs->from_zone}";
                  }
                }
              }
            }

            if($sc === TRUE)
            {
              $arr = array('line_status' => 'D');

              if( ! $this->transfer_model->update_details($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document line status";
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 'D',
                'cancel_reason' => get_null($reason),
                'cancel_user' => $this->_user->uname
              );

              if( ! $this->transfer_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }
            }


            if($sc === TRUE)
            {
              if( ! $this->movement_model->drop_movement($code))
              {
                $sc = FALSE;
                $this->error = "Failed delete stock movement";
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
          else
          {
            if($doc->status == 'C')
            {
              $sc = FALSE;
              set_error('status');
            }
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
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function send_to_erp()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'C')
        {
          if(is_true(getConfig('WRX_API')) && is_true(getConfig('WRX_TR_INTERFACE')))
          {
            $this->load->library('wrx_transfer_api');

            if( ! $this->wrx_transfer_api->export_transfer($doc->code))
            {
              $sc = FALSE;
              $this->error = "Export failed : ".$this->wrx_transfer_api->error;
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Service unavailable please check API configuration";
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

    $this->_response($sc);
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TRANSFER');
    $run_digit = getConfig('RUN_DIGIT_TRANSFER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->transfer_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function print_transfer($code)
  {
    $this->load->library('printer');

    $ds = array(
      'doc' => $this->transfer_model->get($code),
      'details' => $this->transfer_model->get_details($code)
    );

    $this->load->view('print/print_transfer', $ds);
  }


  public function clear_filter()
  {
    $filter = array(
      'tr_code',
      'tr_user',
      'tr_to_warehouse',
      'tr_fromDate',
      'tr_toDate',
      'tr_status',
      'tr_is_export',
      'tr_doc_num'
    );

    clear_filter($filter);

    echo 'done';
  }


} //--- end class
?>
