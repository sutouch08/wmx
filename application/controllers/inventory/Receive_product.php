<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_product extends PS_Controller
{
  public $menu_code = 'ICREPD';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title = 'รับสินค้าเข้า';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_product';
    $this->load->model('inventory/receive_product_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->helper('warehouse');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'rp_code', ''),
      'reference' => get_filter('reference', 'rp_reference', ''),
      'from_date' => get_filter('from_date', 'rp_from_date', ''),
      'to_date' => get_filter('to_date', 'rp_to_date', ''),
      'status' => get_filter('status', 'rp_status', 'all'),
      'warehouse' => get_filter('warehouse', 'rp_warehouse', 'all'),
      'zone' => get_filter('zone', 'rp_zone', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();

      $rows = $this->receive_product_model->count_rows($filter);
      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);

      $filter['docs'] = $this->receive_product_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $this->pagination->initialize($init);
      $this->load->view('inventory/receive_product/receive_product_list', $filter);
    }
  }


  public function save_as_draft()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $doc = $this->receive_product_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'O' OR $doc->status == 'P')
        {
          $arr = array(
            'zone_code' => NULL,
            'zone_name' => NULL,
            'warehouse_code' => NULL,
            'warehouse_name' => NULL
          );

          $zone = empty($ds->zone_code) ? NULL : $this->zone_model->get($ds->zone_code);

          if( ! empty($zone))
          {
            if( ! $zone->active)
            {
              $sc = FALSE;
              $this->error = "Cannot save document : {$zone->name} is inactive";
            }

            $arr = array(
              'zone_code' => $zone->code,
              'zone_name' => $zone->name,
              'warehouse_code' => $zone->warehouse_code,
              'warehouse_name' => $zone->warehouse_name
            );
          }

          if($sc === TRUE)
          {
            if( ! $this->receive_product_model->update($ds->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update document header";
            }
          }

          if($sc === TRUE)
          {
            if( ! empty($ds->rows))
            {
              $this->db->trans_begin();

              foreach($ds->rows as $row)
              {
                if($sc === FALSE) { break; }

                if( ! $this->receive_product_model->update_detail($row->id, ['receive_qty' => $row->qty]))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update row";
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

          if($sc === TRUE)
          {
            $logs = array(
              'code' => $doc->code,
              'action' => 'save darft',
              'user' => $this->_user->uname
            );

            $this->receive_product_model->add_logs($logs);
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


  public function save()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $doc = $this->receive_product_model->get($ds->code);

      if( ! empty($doc))
      {
        $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

        if($doc->status == 'O' OR $doc->status == 'P')
        {
          $zone = empty($ds->zone_code) ? NULL : $this->zone_model->get($ds->zone_code);

          if(empty($zone))
          {
            $sc = FALSE;
            $this->error = "Cannot save document : Invalid zone code";
          }

          if($sc === TRUE && ! $zone->active)
          {
            $sc = FALSE;
            $this->error = "Cannot save document : {$zone->name} is inactive";
          }

          $arr = array(
            'status' => 'C',
            'zone_code' => $zone->code,
            'zone_name' => $zone->name,
            'warehouse_code' => $zone->warehouse_code,
            'warehouse_name' => $zone->warehouse_name,
            'shipped_date' => $date_add,
            'update_user' => $this->_user->uname
          );

          $this->db->trans_begin();

          if($sc === TRUE)
          {
            if( ! $this->receive_product_model->update($ds->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update document header";
            }
          }

          if($sc === TRUE)
          {
            $this->load->model('stock/stock_model');
            $this->load->model('inventory/movement_model');

            if( ! empty($ds->rows))
            {
              foreach($ds->rows as $row)
              {
                if($sc === FALSE) { break; }

                $detail = $this->receive_product_model->get_detail($row->id);

                if( ! empty($detail))
                {
                  if($detail->qty == $row->qty)
                  {
                    $arr = array(
                      'receive_qty' => $row->qty,
                      'line_status' => 'C'
                    );

                    if( ! $this->receive_product_model->update_detail($row->id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update row";
                    }

                    if($sc === TRUE)
                    {
                      if( ! $this->stock_model->update_stock_zone($zone->code, $row->product_code, $row->qty))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to update stock at line {$row->no} : {$row->product_code}";
                      }
                    }

                    if($sc === TRUE)
                    {
                      $arr = array(
                        'reference' => $doc->code,
                        'warehouse_code' => $zone->warehouse_code,
                        'zone_code' => $zone->code,
                        'product_code' => $row->product_code,
                        'move_in' => $row->qty,
                        'move_out' => 0,
                        'date_add' => $date_add
                      );

                      if( ! $this->movement_model->add($arr))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to save stock movement";
                      }
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "Line item return qty and receive qty missmatch at Line {$row->no} : {$row->product_code}";
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Line item not found at Line {$row->no} : {$row->product_code}";
                }
              }
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();

            $logs = array(
              'code' => $doc->code,
              'action' => 'Finish',
              'user' => $this->_user->uname
            );

            $this->receive_product_model->add_logs($logs);
          }
          else
          {
            $this->db->trans_rollback();
          }

          if($sc === TRUE && is_true(getConfig('WRX_API')))
          {
            if(is_true(getConfig('WRX_GR_INTERFACE')))
            {
              $this->load->library('wrx_ib_api');

              if( ! $this->wrx_ib_api->export_receive($doc->code))
              {
                $sc = FALSE;
                $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป ERP ไม่สำเร็จ : ERP Error - ".$this->wrx_ib_api->error;

                $arr = array(
                  'is_exported' => 3,
                  'export_error' => $this->error
                );

                $this->receive_product_model->update($doc->code, $arr);
              }
              else
              {
                $arr = array(
                  'is_exported' => 1,
                  'export_error' => NULL
                );

                $this->receive_product_model->update($doc->code, $arr);
              }
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

    $this->_response($sc);
  }


  public function process($code)
  {
    $this->load->helper('discount');

    $doc = $this->receive_product_model->get($code);

    if( ! empty($doc))
    {
      $details = $this->receive_product_model->get_details($code);
      $barcode_list = array();

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $barcode = $this->products_model->get_barcode($rs->product_code);
          $barcode = empty($barcode) ? $rs->product_code : $barcode;
          $rs->barcode = md5($barcode);
          $rs->bc = $barcode;

          if( empty($barcode_list[$rs->product_code]))
          {
            $bc = (object) array(
              'barcode' => $rs->barcode,
              'product_code' => $rs->product_code
            );

            $barcode_list[$rs->product_code] = $bc;
          }
        }
      }

      $ds = array(
        'doc' => $doc,
        'details' => $details,
        'barcode_list' => $barcode_list
      );

      if($doc->status == 'P')
      {
        $arr = array(
          'status' => 'O'
        );

        if($this->receive_product_model->update($doc->code, $arr))
        {
          $doc->status = 'O';

          $logs  = array(
            'code' => $doc->code,
            'action' => 'receive',
            'user' => $this->_user->uname
          );

          $this->receive_product_model->add_logs($logs);
        }
      }

      if($doc->status == 'P' OR $doc->status == 'O')
      {
        $this->load->view('inventory/receive_product/receive_product_process', $ds);
      }
      else
      {
        $this->load->view('inventory/receive_product/receive_product_view_detail', $ds);
      }
    }
    else
    {
      $this->error_page();
    }
  }


  public function view_detail($code)
  {
    $doc = $this->receive_product_model->get($code);
    $details = $this->receive_product_model->get_details($code);
    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('inventory/receive_product/receive_product_view_detail', $ds);
  }


  public function send_to_erp()
  {
    $sc = TRUE;

    $code = $this->input->post('code');

    if( ! empty($code))
    {
      if(is_true(getConfig('WRX_API')))
      {
        if(is_true(getConfig('WRX_GR_INTERFACE')))
        {
          $doc = $this->receive_product_model->get($code);

          if( ! empty($doc))
          {
            if($doc->status == 'C')
            {
              $this->load->library('wrx_ib_api');

              if( ! $this->wrx_ib_api->export_receive($code))
              {
                $sc = FALSE;
                $this->error = "Send data failed : ERP Error - ".$this->wrx_ib_api->error;

                if($doc->is_exported != 1)
                {
                  $arr = array(
                    'is_exported' => 3,
                    'export_error' =>  $this->error
                  );

                  $this->receive_product_model->update($code, $arr);
                }
              }
              else
              {
                if($doc->is_exported != 1)
                {
                  $arr = array(
                    'is_exported' => 1,
                    'export_error' => NULL
                  );

                  $this->receive_product_model->update($code, $arr);
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
          $this->error = "Goods Receive Interface is inactive";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "WRX API is inactive";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function get_zone()
  {
    $sc = TRUE;
    $ds = [];
    $code = $this->input->post('zone_code');

    if( ! empty($code))
    {
      $zone = $this->zone_model->get_zone($code);

      if( ! empty($zone))
      {
        $ds = array(
          'code' => $zone->code,
          'name' => $zone->name,
          'warehouse_code' => $zone->warehouse_code,
          'active' => $zone->active
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid zone code or not found";
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
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


	//--- print received
  public function print_detail($code)
  {
    $this->load->library('printer');
    $doc = $this->receive_product_model->get($code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $details = $this->receive_product_model->get_details($code);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }
    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_receive', $ds);
  }


  public function cancle_receive()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $reason = trim($this->input->post('reason'));

    if($this->pm->can_delete)
    {
			$doc = $this->receive_product_model->get($code);

			if( ! empty($doc))
			{
				if($doc->status != 'D')
				{
          $this->load->model('stock/stock_model');
          $this->load->model('inventory/movement_model');

          $details = $this->receive_product_model->get_details($code);

          if( ! empty($details))
          {
            if($doc->status == 'C')
            {
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                $stock = $this->stock_model->get_stock_zone($doc->zone_code, $rs->product_code);

                if($stock < $rs->receive_qty)
                {
                  $sc = FALSE;
                  $this->error = "สต็อกคงเหลือในโซนไม่เพียงพอ {$rs->product_code} : {$doc->zone_code} ({$rs->receive_qty} / {$stock})";
                }
              }
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            if( ! empty($details))
            {
              if($doc->status == 'C')
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE) { break; }

                  if( ! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, ($rs->receive_qty * -1)))
                  {
                    $sc = FALSE;
                    $this->error = "ตัดสต็อกออกจากโซนไม่สำเร็จ : {$rs->product_code}";
                  }
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->movement_model->drop_movement($doc->code))
                {
                  $sc = FALSE;
                  $this->error = "ลบ movement ไม่สำเร็จ";
                }
              }
            }

            if($sc === TRUE)
            {
              //--- set details to cancle
              if( ! $this->receive_product_model->update_details($code, array('line_status' => 'D')))
              {
                $sc = FALSE;
                $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 'D',
                'cancel_reason' => NULL,
                'cancel_user' => $this->_user->uname,
                'cancel_date' => now()
              );
              
              if( ! $this->receive_product_model->update($doc->code, $arr))
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
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function get_item()
  {
    if($this->input->post('barcode'))
    {
      $barcode = trim($this->input->post('barcode'));
      $item = $this->products_model->get_product_by_barcode($barcode);
      if( ! empty($item))
      {
        echo json_encode($item);
      }
      else
      {
        echo 'not-found';
      }
    }
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_PRODUCT');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_PRODUCT');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_product_model->get_max_code($pre);

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


  public function clear_filter()
  {
    $filter = array(
      'rp_code',
      'rp_reference',
      'rp_from_date',
      'rp_to_date',
      'rp_status',
			'rp_warehouse',
      'rp_zone'
    );

    return clear_filter($filter);
  }
} //--- end class
?>
