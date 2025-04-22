<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Return_lend extends PS_Controller
{
  public $menu_code = 'ICRTLD';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'คืนสินค้าจากการยืม';
  public $filter;
  public $error;
  public $required_remark = 1;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_lend';
    $this->load->model('inventory/return_lend_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/employee_model');
    $this->load->model('masters/products_model');

    $this->load->helper('employee');
  }


  public function index()
  {
		$this->load->helper('warehouse');

    $filter = array(
      'code' => get_filter('code', 'rl_code', ''),
      'lend_code' => get_filter('lend_code', 'lend_code', ''),
      'employee' => get_filter('employee', 'rl_employee', 'all'),
      'from_date' => get_filter('from_date', 'rl_from_date', ''),
      'to_date' => get_filter('to_date', 'rl_to_date', ''),
      'zone' => get_filter('zone', 'rl_zone', ''),
      'status' => get_filter('status', 'rl_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment  = 4; //-- url segment
		$rows     = $this->return_lend_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->return_lend_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_lend_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_lend_model->get_sum_amount($rs->code);
      }
    }

    $filter['docs'] = $document;
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_lend/return_lend_list', $filter);
  }



  public function add_new()
  {
    $ds['new_code'] = $this->get_new_code();
    $this->load->view('inventory/return_lend/return_lend_add', $ds);
  }


  public function add()
  {
    $sc = TRUE;
    $ex = 1;
    if($this->input->post('header') && $this->input->post('details'))
    {
      $this->load->model('inventory/lend_model');
      $this->load->model('inventory/movement_model');
			$this->load->model('masters/warehouse_model');
      $this->load->model('stock/stock_model');

      //--- retrive data form
      $header = json_decode($this->input->post('header'));
      $details = json_decode($this->input->post('details'));
      $date_add = db_date($header->date_add, TRUE);

      if(empty($header) OR empty($details))
      {
        $sc = FALSE;
        set_error('required');
      }
      else
      {
        $lend = $this->lend_model->get($header->lendCode);
        $zone = $this->zone_model->get($header->zone_code); //--- โซนปลายทาง

        if(empty($lend))
        {
          $sc = FALSE;
          $this->error = "เลขที่เอกสารยืมสินค้าไม่ถูกต้อง";
        }

        if(empty($zone))
        {
          $sc = FALSE;
          $this->error = "โซนรับสินค้าไม่ถูกต้อง";
        }
      }

      if( $sc === TRUE)
      {
        if( ! empty($details))
        {
          //--- check stock ขาออก
          foreach($details as $rs)
          {
            if($sc === FALSE) { break; }

            $stock = $this->stock_model->get_stock_zone($lend->zone_code, $rs->product_code);

            if($stock < $rs->qty)
            {
              $sc = FALSE;
              $this->error = "สต็อกคงเหลือในโซนไมเพียงพอ <br/>Zone : {$lend->zone_code} <br/>SKU : {$rs->product_code} <br/>Qty : {$rs->qty}/{$stock}";
            }
          }
        }

        if($sc === TRUE)
        {
          $from_warehouse = $this->zone_model->get_warehouse_code($lend->zone_code);
          $wh = $this->warehouse_model->get($zone->warehouse_code); //--- คลังปลายทาง
          $code = $this->get_new_code($date_add);

          $arr = array(
            'code' => $code,
            'lend_code' => $header->lendCode,
            'empID' => $header->empID,
            'empName' => $header->empName,
            'from_warehouse' => $from_warehouse, //--- warehouse ต้นทาง ดึงจากเอกสารยืม
            'from_zone' => $lend->zone_code, //--- zone ต้นทาง ดึงจากเอกสารยืม
            'to_warehouse' => $zone->warehouse_code,
            'to_zone' => $zone->code,
            'date_add' => $date_add,
            'shipped_date' => now(),
            'user' => $this->_user->uname,
            'remark' => $header->remark,
            'status' => 1
          );

          //--- start transection;
          $this->db->trans_begin();

          if($this->return_lend_model->add($arr))
          {
            foreach($details as $row)
            {
              if($sc === FALSE) { break; }

              if($row->qty > 0)
              {
                $item = $this->products_model->get($row->product_code);

                if( ! empty($item))
                {
                  $amount = $row->qty * $item->price;

                  $ds = array(
                    'return_code' => $code,
                    'lend_code' => $header->lendCode,
                    'product_code' => $item->code,
                    'product_name' => $item->name,
                    'qty' => $row->qty,
                    'receive_qty' => $row->qty,
                    'price' => $item->price,
                    'amount' => $amount,
                    'vat_amount' => get_vat_amount($amount)
                  );

                  if( ! $this->return_lend_model->add_detail($ds))
                  {
                    $sc = FALSE;
                    $this->error = "เพิ่มรายการไม่สำเร็จ : {$item->code}";
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->stock_model->update_stock_zone($lend->zone_code, $row->product_code, ($row->qty * -1)))
                    {
                      $sc = FALSE;
                      $this->error = "ตัดสต็อกออกจากโซนไม่สำเร็จ <br/>Zone : {$lend->zone_code} <br/>SKU : {$item->code} <br/>Qty : {$row->qty}";
                    }
                  }

                  if($sc === TRUE)
                  {
                    //--- insert Movement out
                    $arr = array(
                      'reference' => $code,
                      'warehouse_code' => $lend->warehouse_code,
                      'zone_code' => $lend->zone_code,
                      'product_code' => $item->code,
                      'move_in' => 0,
                      'move_out' => $row->qty,
                      'date_add' => db_date($this->input->post('date_add'), TRUE)
                    );

                    if( ! $this->movement_model->add($arr))
                    {
                      $sc = FALSE;
                      $this->error = "บันทึก movement ขาออกไม่สำเร็จ";
                    }
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->stock_model->update_stock_zone($zone->code, $item->code, $row->qty))
                    {
                      $sc = FALSE;
                      $this->error = "เพิ่มสต็อกเข้าโซนไม่สำเร็จ <br/>Zone : {$zone->code} <br/>SKU : {$item->code} <br/>Qty : {$row->qty}";
                    }
                  }

                  if($sc === TRUE)
                  {
                    //--- insert Movement in
                    $arr = array(
                      'reference' => $code,
                      'warehouse_code' => $zone->warehouse_code,
                      'zone_code' => $zone->code,
                      'product_code' => $item->code,
                      'move_in' => $row->qty,
                      'move_out' => 0,
                      'date_add' => db_date($this->input->post('date_add'), TRUE)
                    );

                    if( ! $this->movement_model->add($arr))
                    {
                      $sc = FALSE;
                      $this->error = "บันทึก movement ขาเข้าไม่สำเร็จ";
                    }
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->return_lend_model->update_receive($header->lendCode, $item->code, $row->qty))
                    {
                      $sc = FALSE;
                      $this->error = "Update ยอดรับไม่สำเร็จ {$item->code}";
                    }
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Invalid Item Code : {$row->product_code}";
                }
              } //-- if qty > 0
            } //--- end foreach
          }
          else
          {
            $sc = FALSE;
            $this->error = "เพิ่มเอกสารไม่สำเร็จ";
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        } //--- if $sc = TRUE
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
      "code" => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $doc = $this->return_lend_model->get($code);

    if( ! empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->to_zone);
      $doc->empName = $this->employee_model->get_name($doc->empID);
    }

    $details = $this->return_lend_model->get_details($code);


    $ds['doc'] = $doc;
    $ds['details'] = $details;

    $this->load->view('inventory/return_lend/return_lend_edit', $ds);
  }


  public function cancle_return()
  {
    $sc = TRUE;

    $this->load->model('stock/stock_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/lend_model');

    $code = $this->input->post('return_code');
    $reason = $this->input->post('reason');
    $force_cancel = $this->input->post('force_cancel') == 1 ? TRUE : FALSE;

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->return_lend_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 2)
          {
            $lend = $this->lend_model->get($doc->lend_code);

            if($doc->status == 1)
            {
              $details = $this->return_lend_model->get_details($code);

              if( ! empty($details))
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE) { break; }

                  $stock = $this->stock_model->get_stock_zone($doc->to_zone, $rs->product_code);

                  if($stock < $rs->receive_qty)
                  {
                    $sc = FALSE;
                    $this->error = "สต็อกคงเหลือในโซนไม่พอยกเลิก <br/>Zone : {$doc->to_zone}<br/>SKU : {$rs->product_code}<br/>Qty : {$rs->receive_qty}/{$stock}";
                  }
                }
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              if($doc->status == 1)
              {
                if( ! empty($details))
                {
                  foreach($details as $rs)
                  {
                    if($sc === FALSE) { break; }

                    if( ! $this->stock_model->update_stock_zone($doc->to_zone, $rs->product_code, ($rs->receive_qty * -1)))
                    {
                      $sc = FALSE;
                      $this->error = "ตัดสต็อกออกจากโซนไม่สำเร็จ <br/>Zone : {$doc->to_zone}<br/>SKU : {$rs->product_code}";
                    }

                    if($sc === TRUE)
                    {
                      if( ! $this->stock_model->update_stock_zone($doc->from_zone, $rs->product_code, $rs->receive_qty))
                      {
                        $sc = FALSE;
                        $this->error = "เพิ่มสต็อกเข้าโซนไม่สำเร็จ <br/>Zone : {$doc->from_zone}<br/>SKU : {$rs->product_code}";
                      }
                    }

                    $qty = $rs->receive_qty * -1;  //--- convert to negative for add in function

                    if( ! $this->return_lend_model->update_receive($rs->lend_code, $rs->product_code, ($rs->receive_qty * -1)))
                    {
                      $sc = FALSE;
                      $this->error = "ปรับปรุง ยอดรับ {$rs->product_code} ไม่สำเร็จ";
                    }
                  } //-- end foreach
                }

                if($sc === TRUE)
                {
                  if( ! $this->movement_model->drop_movement($code) )
                  {
                    $sc = FALSE;
                    $this->error = "ลบ movement ไม่สำเร็จ";
                  }
                }
              } //-- if doc->status == 1

              //--- 3. change lend_details status to 2 (cancle)
              if($sc === TRUE)
              {
                if( ! $this->return_lend_model->change_details_status($code, 2))
                {
                  $sc = FALSE;
                  $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
                }
              }

              //--- 4. change return_lend document to 2
              if($sc === TRUE)
              {
                $arr = array(
                  'inv_code' => NULL,
                  'status' => 2,
                  'cancle_reason' => $reason,
                  'cancle_user' => $this->_user->uname
                );

                if( ! $this->return_lend_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
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
            } //--- $sc = TRUE
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบเลขที่เอกสาร";
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
    $this->load->model('inventory/lend_model');
    $doc = $this->return_lend_model->get($code);

    $details = $this->lend_model->get_backlogs_list($doc->lend_code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $Qtys =  $this->return_lend_model->get_return_qty($doc->code, $rs->product_code);
        $rs->return_qty = empty($Qtys) ? 0 : $Qtys->qty;
        $rs->receive_qty = empty($Qtys) ? 0 : $Qtys->receive_qty;
      }
    }

    $data['doc'] = $doc;
    $data['details'] = $details;
    $this->load->view('inventory/return_lend/return_lend_view_detail', $data);
  }


  public function get_lend_details($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/lend_model');
    $doc = $this->lend_model->get($code);

    if(!empty($doc))
    {
      $ds = array(
        'empID' => $doc->empID,
        'empName' => $doc->empName
      );

      $details = $this->return_lend_model->get_backlogs($code);

      $rows = array();

      if(!empty($details))
      {
        $no = 1;
        $totalLend = 0;
        $totalReceived = 0;
        $totalBacklogs = 0;

        foreach($details as $rs)
        {
          $barcode = $this->products_model->get_barcode($rs->product_code);
          $backlogs = $rs->qty - $rs->receive;

          if($backlogs > 0)
          {
            $arr = array(
              'no' => $no,
              'itemCode' => $rs->product_code,
              'itemName' => $rs->product_name,
              'barcode' => (!empty($barcode) ? $barcode : $rs->product_code), //--- หากไม่มีบาร์โค้ดให้ใช้รหัสสินค้าแทน
              'lendQty' => $rs->qty,
              'lendQtyLabel' => number($rs->qty, 2),
              'received' => $rs->receive,
              'receivedLabel' => number($rs->receive, 2),
              'backlogs' => $backlogs,
              'backlogsLabel' => number($backlogs, 2)
            );

            array_push($rows, $arr);
            $no++;
            $totalLend += $rs->qty;
            $totalReceived += $rs->receive;
            $totalBacklogs += $backlogs;
          }
        }

        $arr = array(
          'totalLend' => $totalLend,
          'totalReceived' => $totalReceived,
          'totalBacklogs' => $totalBacklogs
        );

        array_push($rows, $arr);
      }
      else
      {
        array_push($rows, array('nodata' => 'nodata'));
      }

      $ds['details'] = $rows;
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่ใบยืมสินค้า";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function print_return($code)
  {
    $this->load->model('inventory/lend_model');
    $this->load->library('printer');
    $doc = $this->return_lend_model->get($code);
    $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
    $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    $doc->from_zone_name = $this->zone_model->get_name($doc->from_zone);
    $doc->to_zone_name = $this->zone_model->get_name($doc->to_zone);

    $details = $this->lend_model->get_backlogs_list($doc->lend_code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $Qtys =  $this->return_lend_model->get_return_qty($doc->code, $rs->product_code);
        $rs->return_qty = empty($Qtys) ? 0 : $Qtys->qty;
        $rs->receive_qty = empty($Qtys) ? 0 : $Qtys->receive_qty;
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_return_lend', $ds);
  }


  public function get_new_code($date = '')
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RETURN_LEND');
    $run_digit = getConfig('RUN_DIGIT_RETURN_LEND');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_lend_model->get_max_code($pre);
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


  public function is_exists($code, $old_code = NULL)
  {
    $exists = $this->return_lend_model->is_exists($code, $old_code);
    if($exists)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }


  public function clear_filter()
  {
    $filter = array(
      'rl_code',
      'lend_code',
      'rl_employee',
      'rl_from_date',
      'rl_to_date',
      'rl_status',
      'rl_zone'
    );

    clear_filter($filter);
  }


} //--- end class
?>
