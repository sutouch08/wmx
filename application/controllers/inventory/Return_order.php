<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_order extends PS_Controller
{
  public $menu_code = 'ICRTOR';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'คืนสินค้า(ลดหนี้ขาย)';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_order';
    $this->load->model('inventory/return_order_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/products_model');
  }


  public function index()
  {
    $this->load->helper('warehouse');
    $this->load->helper('print');

    $filter = array(
      'code' => get_filter('code', 'sm_code', ''),
      'invoice' => get_filter('invoice', 'sm_invoice', ''),
      'customer_code' => get_filter('customer_code', 'sm_customer_code', ''),
      'from_date' => get_filter('from_date', 'sm_from_date', ''),
      'to_date' => get_filter('to_date', 'sm_to_date', ''),
      'status' => get_filter('status', 'sm_status', 'all'),
      'approve' => get_filter('approve', 'sm_approve', 'all'),
      'zone' => get_filter('zone', 'sm_zone', ''),
      'is_pos_api' => get_filter('is_pos_api', 'sm_pos_api', 'all')
    );

    //--- แสดงผลกี่รายการต่อหน้า
    $perpage = get_rows();

    $rows = $this->return_order_model->count_rows($filter);
    $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
    $document = $this->return_order_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

    if( ! empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->return_order_model->get_sum_qty($rs->code);
        $rs->amount = $this->return_order_model->get_sum_amount($rs->code);
      }
    }

    $filter['docs'] = $document;
    $filter['allow_import_return'] = is_true(getConfig('ALLOW_IMPORT_RETURN'));
    $this->pagination->initialize($init);
    $this->load->view('inventory/return_order/return_order_list', $filter);
  }


  public function import_excel_file()
	{
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');

    $this->load->library('excel');

    $sc = TRUE;
    $import = 0;
    $uid = genUid();
    $Ymd = date('Ymd');
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
  	$path = $this->config->item('upload_path').'return/';
    $file	= 'uploadFile';
    $config = array(
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "SM-import-{$Ymd}-{$uid}",
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      $sc = FALSE;
      $this->error = $this->upload->display_errors();
    }

    if($sc === TRUE)
    {
      //---- checking data
      $info = $this->upload->data();
      /// read file
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      //get only the Cell Collection
      $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

      $i = 1;
      $count = count($collection);
      $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;

      if($count <= $limit)
      {
        $ds = array();
        $zn = array(); //-- ไว้เก็บโซน object
        $bookcode = getConfig('BOOK_CODE_RETURN_ORDER');

        /*
        Loop เพื่อ จัดข้อมูลในรูปแบบเอกสารมี order เป็น key หลัก ในมิติที่ 1 ไว้ใช้สร้างเอกสาร
        รายการสินค้า จะถูกเพิ่มเข้าใน invoice เป็น array มิติที่ 2
        $ds[order_code] = array(
          [0] => line data object,
          [1] => line data object
        );
        */

        foreach($collection as $cs)
        {
          if($sc === FALSE)
          {
            break;
          }

          if($i === 1)
          {
            $i++;

            $headCol = array(
              'A' => 'Date',
              'B' => 'Order Code',
              'C' => 'Warehouse Code',
              'D' => 'Zone Code',
              'E' => 'Item Code',
              'F' => 'Return Qty',
              'G' => 'Interface',
              'H' => 'WMS',
              'I' => 'Remark'
            );

            foreach($headCol as $col => $field)
            {
              if($cs[$col] !== $field)
              {
                $sc = FALSE;
                $this->error = 'Column '.$col.' Should be '.$field;
                break;
              }
            }
          }
          else
          {
            if( ! empty($cs['A']))
            {
              if(empty(trim($cs['B'])))
              {
                $sc = FALSE;
                $this->error = "Missing Order Code at Line{$i}";
              }

              if(empty(trim($cs['C'])))
              {
                $sc = FALSE;
                $this->error = "Missing Warhouse Code at Line{$i}";
              }

              if(empty(trim($cs['D'])))
              {
                $sc = FALSE;
                $this->error = "Missing Zone Code at Line{$i}";
              }

              if(empty(trim($cs['E'])))
              {
                $sc = FALSE;
                $this->error = "Missing Item Code at Line{$i}";
              }

              if(empty(trim($cs['F'])) OR intval($cs['F']) <= 0)
              {
                $sc = FALSE;
                $this->error = "Invalid Reqturn Qty at Line{$i}";
              }

              if($sc === TRUE)
              {
                $date = db_date(trim($cs['A']));
                $order_code = trim($cs['B']); //--- order code use to be 1st dimention array
                $zone_code = trim($cs['D']);
                $item_code = trim($cs['E']);
                $return_qty = intval(trim($cs['F']));
                $remark = empty($cs['I']) ? NULL : get_null(trim($cs['I']));


                //--- ถ้ายังไม่มี order_code ให้สร้างใหม่
                if( ! isset($ds[$order_code]))
                {
                  //--- check date format only check not convert
                  if( ! is_valid_date($date))
                  {
                    $sc = FALSE;
                    $this->error = "Invalid Date format at Line{$i}";
                  }

                  //--- check warehouse and zone
                  if( empty($zn[$zone_code]))
                  {
                    $zone = $this->zone_model->get($zone_code);

                    if( ! empty($zone))
                    {
                      $zn[$zone_code] = $zone;
                    }
                    else
                    {
                      $sc = FALSE;
                      $this->error = "Invalid Zone Code at Line{$i}";
                    }
                  }
                  else
                  {
                    $zone = $zn[$zone_code];
                  }


                  //---- ไว้สร้างเอกสารใหม่
                  if($sc == TRUE)
                  {
                    $invoice = $this->return_order_model->get_invoice_detail_by_order_item($order_code, $item_code);

                    if(empty($invoice))
                    {
                      $sc = FALSE;
                      $this->error = "Invoice not exists for {$order_code} : {$item_code} at Line {$i}";
                    }

                    if($sc === TRUE)
                    {
                      if($invoice->qty < $return_qty)
                      {
                        $sc = FALSE;
                        $this->error = "Return quantity ({$return_qty}) exceed invoice quantity ({intval($invoice)}) at Line {$i}";
                      }
                    }

                    if($sc === TRUE)
                    {
                      $invoice->price = round(add_vat($invoice->price), 2);
                      $amount = round((get_price_after_discount($invoice->price, $invoice->discount) * $return_qty), 2);
                      $vat_amount = round(get_vat_amount($amount), 2);

                      $ds[$order_code] = (object) array(
                        'date_add' => $date,
                        'invoice' => $invoice->code,
                        'customer_code' => $invoice->customer_code,
                        'customer_name' => $invoice->customer_name,
                        'order_code' => $order_code,
                        'warehouse_code' => $zone->warehouse_code,
                        'zone_code' => $zone->code,
                        'must_accept' => empty($zone->user_id) ? 0 : 1,
                        'remark' => $remark,
                        'details' => array((object)array(
                          'invoice_code' => $invoice->code,
                          'order_code' => $order_code,
                          'product_code' => $invoice->product_code,
                          'product_name' => $invoice->product_name,
                          'sold_qty' => round($invoice->qty, 2),
                          'return_qty' => $return_qty,
                          'price' => $invoice->price,
                          'discount_percent' => round($invoice->discount, 2),
                          'amount' => $amount,
                          'vat_amount' => $vat_amount
                        ))
                      );
                    }
                  }
                }
                else
                {
                  $invoice = $this->return_order_model->get_invoice_detail_by_order_item($order_code, $item_code);
                  $invoice->price = round(add_vat($invoice->price), 2);
                  $amount = round((get_price_after_discount($invoice->price, $invoice->discount) * $return_qty), 2);
                  $vat_amount = round(get_vat_amount($amount), 2);

                  $ds[$order_code]->details[] = (object)array(
                    'invoice_code' => $invoice->code,
                    'order_code' => $order_code,
                    'product_code' => $invoice->product_code,
                    'product_name' => $invoice->product_name,
                    'sold_qty' => round($invoice->qty, 2),
                    'return_qty' => $return_qty,
                    'price' => $invoice->price,
                    'discount_percent' => round($invoice->discount, 2),
                    'amount' => $amount,
                    'vat_amount' => $vat_amount
                  );
                }
              } //--- endif $sc === TRUE

              $i++;
            }
          }  //--- end if $i === 1
        } //--- foreach collection

        //--- เก็บข้อมูลครบแล้ว
        if($sc === TRUE && ! empty($ds))
        {
          $this->db->trans_begin();

          foreach($ds as $sm)
          {
            if($sc === FALSE)
            {
              break;
            }

            $code = $this->get_new_code($sm->date_add);

            if(empty($code))
            {
              $sc = FALSE;
              $this->error = "Failed to generate document number for {$sm->order_code}";
            }

            if($sc === TRUE)
            {
              $arr = array(
                'code' => $code,
                'bookcode' => $bookcode,
                'invoice' => $sm->invoice,
                'customer_code' => $sm->customer_code,
                'warehouse_code' => $sm->warehouse_code,
                'zone_code' => $sm->zone_code,
                'user' => $this->_user->uname,
                'date_add' => $sm->date_add,
                'remark' => $sm->remark,
                'status' => 1,
                'must_accept' => $sm->must_accept,
                'is_import' => 1,
                'import_id' => $uid
              );

              if( ! $this->return_order_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
              }

              if($sc === TRUE)
              {
                if( ! empty($sm->details))
                {
                  foreach($sm->details as $rs)
                  {
                    $arr = array(
                      'return_code' => $code,
                      'invoice_code' => $rs->invoice_code,
                      'order_code' => get_null($rs->order_code),
                      'product_code' => $rs->product_code,
                      'product_name' => $rs->product_name,
                      'sold_qty' => $rs->sold_qty,
                      'qty' => $rs->return_qty,
                      'receive_qty' => $rs->return_qty,
                      'price' => $rs->price,
                      'discount_percent' => $rs->discount_percent,
                      'amount' => $rs->amount,
                      'vat_amount' => $rs->vat_amount
                    );

                    if( ! $this->return_order_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = "บันทึกรายการไม่สำเร็จ @ {$rs->product_code} : {$rs->order_code}";
                    }
                  } //-- end foreach
                } // end if
              } // endif
            }
          } //--- end foreach

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        } //-- endif
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไฟล์มีรายการเกิน {$limit} บรรทัด";
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
	}


  public function get_template_file()
  {
    $path = $this->config->item('upload_path').'return/';
    $file_name = $path."import_return_template.xlsx";

    if(file_exists($file_name))
    {
      header('Content-Description: File Transfer');
      header('Content-Type:Application/octet-stream');
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: 0');
      header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
      header('Content-Length: '.filesize($file_name));
      header('Pragma: public');

      flush();
      readfile($file_name);
      die();
    }
    else
    {
      echo "File Not Found";
    }
  }


  public function add_details($code, $save_type = 1)
  {
    $sc = TRUE;
    $save_type = $save_type == 1 ? 1 : 3;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $doc = $this->return_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          $vat = getConfig('SALE_VAT_RATE'); //--- 0.07

          //--- start transection
          $this->db->trans_begin();

          if($this->return_order_model->drop_details($code))
          {
            foreach($data as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              if($rs->qty > 0)
              {
                $price = round($rs->price, 2);
                $discount = $rs->discount_percent;
                $disc_amount = $discount == 0 ? 0 : $rs->qty * ($price * ($discount * 0.01));
                $amount = ($rs->qty * $price) - $disc_amount;

                $receive_qty = $save_type == 1 ? $rs->qty : 0;

                $arr = array(
                  'return_code' => $code,
                  'invoice_code' => $doc->invoice,
                  'order_code' => get_null($rs->order_code),
                  'product_code' => $rs->product_code,
                  'product_name' => $rs->product_name,
                  'sold_qty' => $rs->sold_qty,
                  'qty' => $rs->qty,
                  'receive_qty' => $receive_qty,
                  'price' => $price,
                  'discount_percent' => $discount,
                  'amount' => $amount,
                  'vat_amount' => get_vat_amount($amount)
                );

                if( ! $this->return_order_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "บันทึกรายการไม่สำเร็จ @ {$rs->product_code} : {$rs->order_code}";
                }
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Failed to delete previous details";
          }


          if($sc === TRUE)
          {
            if( ! $this->return_order_model->set_status($code, $save_type))
            {
              $sc = FALSE;
              $this->error = "Failed to change document status";
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
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        //--- empty document
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function save_as_draft()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->rows))
    {
      $doc = $this->return_order_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 3)
        {
          $this->db->trans_begin();

          if( ! empty($ds->rows))
          {
            foreach($ds->rows as $rs)
            {
              if($sc === FALSE) { break; }

              $row = $this->return_order_model->get_detail($rs->id);

              if( ! empty($row))
              {
                $price = $row->price;
                $disc = $row->discount_percent;
                $receive_qty = $rs->receive_qty;

                $disc_amount = $disc == 0 ? 0 : $receive_qty * ($price * ($disc * 0.01));
                $amount = ($receive_qty * $price) - $disc_amount;

                $arr = array(
                  'receive_qty' => $receive_qty,
                  'amount' => $amount,
                  'vat_amount' => get_vat_amount($amount)
                );

                if( ! $this->return_order_model->update_detail($rs->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update receive qty at {$row->product_code}";
                }
              }
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


  public function save_return()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->rows))
    {
      $doc = $this->return_order_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 3)
        {
          $this->load->model('stock/stock_model');
          $this->load->model('inventory/movement_model');

          $shipped_date = getConfig('ORDER_SOLD_DATE') === 'D' ? $doc->date_add : now();

          $this->db->trans_begin();

          if( ! empty($ds->rows))
          {
            foreach($ds->rows as $rs)
            {
              if($sc === FALSE) { break; }

              $row = $this->return_order_model->get_detail($rs->id);

              if( ! empty($row))
              {
                $price = $row->price;
                $disc = $row->discount_percent;
                $receive_qty = $rs->receive_qty;

                $disc_amount = $disc == 0 ? 0 : $receive_qty * ($price * ($disc * 0.01));
                $amount = ($receive_qty * $price) - $disc_amount;

                $arr = array(
                  'receive_qty' => $receive_qty,
                  'amount' => $amount,
                  'vat_amount' => get_vat_amount($amount),
                  'valid' => 1
                );

                if( ! $this->return_order_model->update_detail($rs->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update receive qty at {$row->product_code}";
                }

                if($sc === TRUE)
                {
                  if( ! $this->stock_model->update_stock_zone($doc->zone_code, $row->product_code, $receive_qty))
                  {
                    $sc = FALSE;
                    $this->error = "เพิ่มสต็อกเข้าโซนไม่สำเร็จ";
                  }
                }

                if($sc === TRUE)
                {
                  $arr = array(
                    'reference' => $doc->code,
                    'warehouse_code' => $doc->warehouse_code,
                    'zone_code' => $doc->zone_code,
                    'product_code' => $row->product_code,
                    'move_in' => $receive_qty,
                    'date_add' => $shipped_date
                  );

                  if( ! $this->movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "บันทึก movement ไม่สำเร็จ";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการตั้งต้น : {$rs->product_code} จำนวน {$rs->receive_qty}";
              }
            }
          }

          if($sc === TRUE)
          {
            $arr = array(
              'status' => 1,
              'shipped_date' => $shipped_date,
              'is_complete' => 1,
              'update_user' => $this->_user->uname
            );

            if( ! $this->return_order_model->update($doc->code, $arr))
            {
              $sc = FALSE;
              $this->error = "เปลียนสถานะเอกสารไม่สำเร็จ";
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


  public function delete_detail($id)
  {
    $rs = $this->return_order_model->delete_detail($id);
    echo $rs === TRUE ? 'success' : 'ลบรายการไม่สำเร็จ';
  }


  public function unsave($code)
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $doc = $this->return_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 1)
        {
          if($doc->is_approve == 1)
          {
            $details = $this->return_order_model->get_details($code);

            if( ! empty($details))
            {
              $this->load->model('stock/stock_model');
              $this->load->model('inventory/movement_model');

              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                $stock = $this->stock_model->get_stock_zone($doc->zone_code, $rs->product_code);

                if($stock < $rs->receive_qty)
                {
                  $sc = FALSE;
                  $this->error = "สต็อกคงเหลือไม่พอให้ย้อนสถานะ";
                }
              }
            }
          } //--- if approved

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            if($doc->is_approve == 1 && ! empty($details))
            {
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                if( ! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, ($rs->receive_qty * -1)))
                {
                  $sc = FALSE;
                  $this->error = "ตัดสต็อกออกจากโซนไม่สำเร็จ";
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->movement_model->drop_movement($code))
                {
                  $sc = FALSE;
                  $this->error = "ลบ movement ไม่สำเร็จ";
                }
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 0,
                'is_approve' => 0,
                'approver' => NULL
              );

              if( ! $this->return_order_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = 'ยกเลิกการบันทึกไม่สำเร็จ';
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
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function approve($code)
  {
		$sc = TRUE;

    if($this->pm->can_approve)
    {
      $this->load->model('stock/stock_model');
      $this->load->model('inventory/movement_model');
      $this->load->model('approve_logs_model');

			$doc = $this->return_order_model->get($code);

			if( ! empty($doc))
			{
				if(($doc->status == 1 OR $doc->status == 3) && $doc->is_approve == 0) //--- status บันทึกแล้วเท่านั้น หรือ รอรับเท่านั้น
				{
          $this->db->trans_begin();

          $shipped_date = getConfig('ORDER_SOLD_DATE') === 'D' ? $doc->date_add : now();

          if($doc->status == 1)
          {
            $arr = array(
              'is_approve' => 1,
              'approver' => $this->_user->uname,
              'shipped_date' => $shipped_date,
              'is_complete' => 1
            );
          }

          if($doc->status == 3)
          {
            $arr = array(
              'is_approve' => 1,
              'approver' => $this->_user->uname
            );
          }

          if( ! $this->return_order_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Approve Faiiled";
          }

					if($sc === TRUE && $doc->status == 1)
					{
            $details = $this->return_order_model->get_details($doc->code);

            if( ! empty($details))
            {
              //---- add movement
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                if( ! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, $rs->receive_qty))
                {
                  $sc = FALSE;
                  $this->error = "เพิ่มสต็อกเข้าโซนไม่สำเร็จ";
                }

                if($sc === TRUE)
                {
                  $arr = array(
                    'reference' => $doc->code,
                    'warehouse_code' => $doc->warehouse_code,
                    'zone_code' => $doc->zone_code,
                    'product_code' => $rs->product_code,
                    'move_in' => $rs->receive_qty,
                    'date_add' => db_date($doc->date_add, TRUE)
                  );

                  if( ! $this->movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = 'บันทึก movement ไม่สำเร็จ';
                  }
                }
              }
            }
					}

          if($sc === TRUE)
          {
            $this->approve_logs_model->add($code, 1, $this->_user->uname);
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
					$sc = FALSE;
					$this->error = "Invalid status";
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
			set_error('permission');
    }

		$this->_response($sc);
  }


  public function unapprove($code)
  {
		$sc = TRUE;

    if($this->pm->can_approve)
    {
      $this->load->model('stock/stock_model');
      $this->load->model('inventory/movement_model');
      $this->load->model('approve_logs_model');

      $doc = $this->return_order_model->get($code);

      if( ! empty($doc))
      {
        if(($doc->status == 1 OR $doc->status == 3) && $doc->is_approve == 1)
        {
          if($doc->status == 1)
          {
            $details = $this->return_order_model->get_details($code);

            if( ! empty($details))
            {
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                $stock = $this->stock_model->get_stock_zone($doc->zone_code, $rs->product_code);

                if($stock < $rs->receive_qty)
                {
                  $sc = FALSE;
                  $this->error = "สต็อกคงเหลือในโซนไม่เพียงพอ";
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

                  if( ! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, ($rs->receive_qty * -1)))
                  {
                    $sc = FALSE;
                    $this->error = "ตัดสต็อกออกจากโซนไม่สำเร็จ";
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
            }

            if($sc === TRUE)
            {
              $arr = array(
                'is_approve' => 0,
                'approver' => NULL,
                'is_complete' => 0,
                'update_user' => $this->_user->uname
              );

              if( ! $this->return_order_model->update($doc->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }
            }

            if($sc === TRUE)
            {
              $this->approve_logs_model->add($code, 0, $this->_user->uname);
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
      set_error('permission');
    }

		$this->_response($sc);
  }


  public function add_new()
  {
    $this->load->view('inventory/return_order/return_order_add');
  }


  public function add()
  {
    $sc = TRUE;
    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $date_add = db_date($data->date_add, TRUE);
			$zone = $this->zone_model->get($data->zone_code);

      if(empty($zone))
      {
        $sc = FALSE;
        $this->error = "รหัสโซนไม่ถูกต้อง";
      }

      if($sc === TRUE)
      {
        $code = $this->get_new_code($date_add);

        $arr = array(
          'code' => $code,
          'invoice' => $data->invoice,
          'customer_code' => $data->customer_code,
          'warehouse_code' => $zone->warehouse_code,
          'zone_code' => $zone->code,
          'user' => $this->_user->uname,
          'date_add' => $date_add,
          'remark' => get_null(trim($data->remark))
        );

        if( ! $this->return_order_model->add($arr))
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error("required");
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $this->load->helper('discount');

    $doc = $this->return_order_model->get($code);

    if( ! empty($doc))
    {
      $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
      $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
      $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
      $details = $this->return_order_model->get_details($code);

      $detail = array();
        //--- ถ้าไม่มีรายละเอียดให้ไปดึงจากใบกำกับมา
      if(empty($details))
      {
        $details = $this->return_order_model->get_invoice_details($doc->invoice);

        if( ! empty($details))
        {
          //--- ถ้าได้รายการ ให้ทำการเปลี่ยนรหัสลูกค้าให้ตรงกับเอกสาร
          $cust = $this->return_order_model->get_customer_invoice($doc->invoice);

          if( ! empty($cust))
          {
            $this->return_order_model->update($doc->code, array('customer_code' => $cust->customer_code));
          }
          //--- เปลี่ยนข้อมูลที่จะแสดงให้ตรงกันด้วย
          $doc->customer_code = $cust->customer_code;
          $doc->customer_name = $cust->customer_name;

          foreach($details as $rs)
          {
            if($rs->qty > 0)
            {
              $returned_qty = $this->return_order_model->get_returned_qty($doc->invoice, $rs->product_code);
              $qty = $rs->qty - $returned_qty;

              if($qty > 0)
              {
                $rs->id = "";
                $rs->discount_percent = round(discountAmountToPercent($rs->discount_amount, $rs->qty, $rs->price), 2);
                $rs->qty = round($qty, 2);
                $rs->price = round($rs->price, 2);
                $rs->sell_price = round($rs->sell_price, 2);
                $rs->amount = $rs->sell_price * $rs->qty;
                $detail[] = $rs;
              }
            }
          }
        }
      }
      else
      {
        foreach($details as $rs)
        {
          $returned_qty = $this->return_order_model->get_returned_qty($doc->invoice, $rs->product_code);
          $qty = $rs->sold_qty - ($returned_qty - $rs->qty);
          $sell_price = $rs->price * (1 - ($rs->discount_percent * 0.01));

          $rs->qty = $qty;
          $rs->sell_price = $sell_price;
          $rs->amount = $rs->sell_price * $qty;
  				$detail[] = $rs;
        }
      }


      $ds = array(
        'doc' => $doc,
        'details' => $detail
      );

      if($doc->status == 0)
      {
        $this->load->view('inventory/return_order/return_order_edit', $ds);
      }
      else
      {
        $this->load->view('inventory/return_order/return_order_view_detail', $ds);
      }
    }
    else
    {
      $this->error_page();
    }
  }


  public function process($code)
  {
    $this->load->helper('discount');

    $doc = $this->return_order_model->get($code);

    if( ! empty($doc))
    {
      $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
      $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
      $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
      $details = $this->return_order_model->get_details($code);
      $barcode_list = array();

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $barcode = $this->products_model->get_barcode($rs->product_code);
          $barcode = empty($barcode) ? $rs->product_code : $barcode;
          $rs->barcode = md5($barcode);

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

      if($doc->status == 3)
      {
        $this->load->view('inventory/return_order/return_order_process', $ds);
      }
      else
      {
        $this->load->view('inventory/return_order/return_order_view_detail', $ds);
      }
    }
    else
    {
      $this->error_page();
    }
  }


  public function update()
  {
    $sc = TRUE;
    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $code = $data->code;
      $date_add = db_date($data->date_add, TRUE);
      $shipped_date = empty($data->shipped_date) ? NULL : db_date($data->shipped_date, TRUE);

      if($sc === TRUE)
      {
        $zone = $this->zone_model->get($data->zone_code);

        if(empty($zone))
        {
          $sc = FALSE;
          $this->error = "รหัสโซนไม่ถูกต้อง";
        }
      }

      if($sc === TRUE)
      {

        $arr = array(
          'date_add' => $date_add,
          'invoice' => $data->invoice,
          'customer_code' => $data->customer_code,
          'warehouse_code' => $zone->warehouse_code,
          'zone_code' => $zone->code,
          'remark' => get_null(trim($data->remark)),
          'update_user' => $this->_user->uname
        );

        if( ! $this->return_order_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = 'ปรับปรุงข้อมูลไม่สำเร็จ';
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function update_shipped_date()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $shipped_date = $this->input->post('shipped_date');

    if( ! empty($code) && ! empty($shipped_date))
    {
      $doc = $this->return_order_model->get($code);

      if( ! empty($doc))
      {
        $arr = array(
          'shipped_date' => empty($shipped_date) ? NULL : db_date($shipped_date, TRUE)
        );

        if( ! $this->return_order_model->update($code, $arr))
        {
          $sc = FALSE;
          set_error('update');
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


  public function view_detail($code)
  {
    $this->load->model('approve_logs_model');
    $doc = $this->return_order_model->get($code);
    $details = $this->return_order_model->get_details($code);
    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'approve_list' => $this->approve_logs_model->get($code)
    );

    $this->load->view('inventory/return_order/return_order_view_detail', $ds);
  }


  public function get_invoice($invoice)
  {
    $sc = TRUE;
    $details = $this->return_order_model->get_invoice_details($invoice);
    $ds = array();
    if(empty($details))
    {
      $sc = FALSE;
      $message = 'ไม่พบข้อมูล';
    }

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $returned_qty = $this->return_order_model->get_returned_qty($invoice, $rs->product_code);
        $qty = $rs->qty - $returned_qty;
        $row = new stdClass();
        if($qty > 0)
        {
          $row->barcode = $this->products_model->get_barcode($rs->product_code);
          $row->invoice = $invoice;
					$row->order_code = $rs->order_code;
          $row->code = $rs->product_code;
          $row->name = $rs->product_name;
          $row->price = round($rs->price, 2);
          $row->discount = round($rs->discount, 2);
          $row->qty = round($qty, 2);
          $row->amount = 0;
          $ds[] = $row;
        }
      }
    }

    echo $sc === TRUE ? json_encode($ds) : $message;
  }


  //--- auto complete
  public function get_invoice_code($customer_code = NULL)
	{
		$txt = $_REQUEST['term'];
		$ds = array();

		$this->db
		->select('code, customer_code, customer_name')
		->where('state', 8)
		->where_in('role', array('S','P', 'U'));

    if( ! empty($customer_code))
    {
      $this->db->where('customer_code', $customer_code);
    }

    if($txt != '*')
    {
      $this->db->like('code', $txt);
    }

    $qs = $this->db
    ->order_by('date_add', 'DESC')
    ->order_by('code', 'DESC')
    ->limit(50, 0)
    ->get('orders');

		if($qs->num_rows() > 0)
		{
			foreach($qs->result() as $rs)
			{
				$ds[] = $rs->code ." | ".$rs->customer_code." | ".$rs->customer_name;
			}
		}
		else
		{
			$ds[] = 'Not found';
		}

		echo json_encode($ds);
	}


	//--- print received
  public function print_detail($code)
  {
    $this->load->library('printer');
    $doc = $this->return_order_model->get($code);
    $doc->customer_name = $this->customers_model->get_name($doc->customer_code);
    $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    $details = $this->return_order_model->get_details($code);

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

    $this->load->view('print/print_return', $ds);
  }


  public function cancle_return()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $reason = trim($this->input->post('reason'));

    if($this->pm->can_delete)
    {
			$doc = $this->return_order_model->get($code);

			if( ! empty($doc))
			{
				if($doc->status != 2)
				{
          $this->load->model('stock/stock_model');
          $this->load->model('inventory/movement_model');

          $details = $this->return_order_model->get_details($code);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              $stock = $this->stock_model->get_stock_zone($doc->zone_code, $rs->product_code);

              if($stock < $rs->receive_qty)
              {
                $sc = FALSE;
                $this->error = "สต็อกคงเหลือในโซนไม่เพียงพอ";
              }
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            if( ! empty($details))
            {
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                if( ! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, ($rs->receive_qty * -1)))
                {
                  $sc = FALSE;
                  $this->error = "ตัดสต็อกออกจากโซนไม่สำเร็จ";
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
              if( ! $this->return_order_model->update_details($code, array('is_cancle' => 1)))
              {
                $sc = FALSE;
                $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 2,
                'is_approve' => 0,
                'approver' => NULL,
                'is_complete' => 0,
                'cancle_date' => now(),
                'cancle_reason' => $reason,
                'cancle_user' => $this->_user->uname
              );

              if( ! $this->return_order_model->update($doc->code, $arr))
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


  public function roll_back_expired()
  {
    $sc = TRUE;

    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->return_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->is_expire == 1)
        {
          $arr = array(
            'is_expire' => 0
          );

          if( ! $this->return_order_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "ย้อนสถานะเอกสารไม่สำเร็จ";
          }
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
      set_error('required');
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
    $prefix = getConfig('PREFIX_RETURN_ORDER');
    $run_digit = getConfig('RUN_DIGIT_RETURN_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_order_model->get_max_code($pre);
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
      'sm_code',
      'sm_invoice',
      'sm_customer_code',
      'sm_from_date',
      'sm_to_date',
      'sm_status',
      'sm_approve',
			'sm_warehouse',
      'sm_zone',
      'sm_must_accept',
      'sm_sap'
    );
    clear_filter($filter);
  }
} //--- end class
?>
