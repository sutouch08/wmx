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
    $this->load->helper('warehouse');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'sm_code', ''),
      'reference' => get_filter('reference', 'sm_reference', ''),
      'order_code' => get_filter('order_code', 'sm_order_code', ''),
      'customer_code' => get_filter('customer_code', 'sm_customer_code', ''),
      'from_date' => get_filter('from_date', 'sm_from_date', ''),
      'to_date' => get_filter('to_date', 'sm_to_date', ''),
      'status' => get_filter('status', 'sm_status', 'all'),
      'warehouse' => get_filter('warehouse', 'sm_warehouse', 'all'),
      'zone' => get_filter('zone', 'sm_zone', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();

      $rows = $this->return_order_model->count_rows($filter);
      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);

      $filter['docs'] = $this->return_order_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $this->pagination->initialize($init);
      $this->load->view('inventory/return_order/return_order_list', $filter);
    }
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
    $doc = $this->return_order_model->get($code);
    $details = $this->return_order_model->get_details($code);
    $ds = array(
      'doc' => $doc,
      'details' => $details
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
