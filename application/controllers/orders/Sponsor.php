<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsor extends PS_Controller
{
  public $menu_code = 'SOODSP';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'สปอนเซอร์';
  public $filter;
  public $error;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/sponsor';
    $this->load->model('orders/order_sponsor_model');
    $this->load->model('orders/order_sponsor_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/reserv_stock_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/sponsors_model');
    $this->load->model('masters/sponsor_budget_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/product_model_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/warehouse_model');

    $this->load->helper('order');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('warehouse');
    $this->load->helper('sponsors');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'sp_code', ''),
      'customer' => get_filter('customer', 'sp_customer', ''),
      'user' => get_filter('user', 'sp_user', 'all'),
      'from_date' => get_filter('fromDate', 'sp_fromDate', ''),
      'to_date' => get_filter('toDate', 'sp_toDate', ''),
      'is_approved' => get_filter('is_approved', 'sp_is_approved', 'all'),
			'warehouse' => get_filter('warehouse', 'sp_warehouse', 'all'),
      'status' => get_filter('status', 'sp_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows = $this->order_sponsor_model->count_rows($filter);
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
    $filter['orders'] = $this->order_sponsor_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

		$this->pagination->initialize($init);
    $this->load->view('sponsor/sponsor_list', $filter);
  }


  public function get_budget()
  {
    $arr = array(
      'budget_id' => NULL,
      'budget_code' => NULL,
      'amount_label' => 0.00,
      'amount' => 0.00
    );

    $sp = $this->sponsors_model->get_by_customer_code($this->input->get('code'));

    if( ! empty($sp))
    {
      if( ! empty($sp->budget_id))
      {
        $bd = $this->sponsor_budget_model->get_valid_budget($sp->budget_id);

        if( ! empty($bd))
        {
          $balance = $bd->balance;
          $commit = $this->sponsor_budget_model->get_commit_amount($bd->id);
          $amount = $balance - $commit;

          $arr['budget_id'] = $bd->id;
          $arr['budget_code'] = $bd->code;
          $arr['amount'] = $amount > 0 ? $amount : 0;
          $arr['amount_label'] = $amount > 0 ? number($amount, 2) : 0;
        }
      }
    }

    echo json_encode($arr);
  }


  public function add_new()
  {
    $this->load->view('sponsor/sponsor_add');
  }


  public function add()
  {
    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $date_add = db_date($data->date_add);
      $code = $this->get_new_code($date_add);

      $wh = $this->warehouse_model->get($data->warehouse_code);
      $customer = $this->customers_model->get($data->customer_code);

      if( ! empty($customer))
      {
        if( ! empty($wh))
        {
          $ds = array(
            'date_add' => $date_add,
            'code' => $code,
            'customer_code' => $customer->code,
            'customer_name' => $customer->name,
            'user' => $this->_user->uname,
            'remark' => get_null($data->remark),
            'customer_ref' => $data->customer_ref,
            'warehouse_code' => $wh->code,
            'budget_id' => $data->budget_id,
            'budget_code' => $data->budget_code
          );

          if( ! $this->order_sponsor_model->add($ds))
          {
            $sc = FALSE;
            set_error('insert');
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid Warehouse code";
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid customer code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
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
    $this->load->model('approve_logs_model');
		$this->load->model('address/customer_address_model');
		$this->load->helper('sender');
    $ds = array();
    $order = $this->order_sponsor_model->get($code);

    if( ! empty($order))
    {
      $details = $this->order_sponsor_model->get_details($code);

      $totalQty = 0;
      $totalAmount = 0;

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $totalQty += $rs->qty;
          $totalAmount += $rs->total_amount;
        }
      }

      $order->total_qty = $totalQty;
      $order->total_amount = $totalAmount;

			$ship_to = $this->customer_address_model->get_customer_address_list($order->customer_code, 'S');

      $ds['approve_logs'] = $this->approve_logs_model->get($code);
      $ds['order'] = $order;
      $ds['details'] = $details;
			$ds['addr']  = $ship_to;
      $this->load->view('sponsor/sponsor_edit', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }
  }


  public function get_detail_table($code)
  {
    $sc = TRUE;
    $ds = array();
    $order = $this->order_sponsor_model->get($code);

    if( ! empty($order))
    {
      $details = $this->order_sponsor_model->get_details($code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
          if($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R')
          {
            if($this->pm->can_add OR $this->pm->can_edit)
            {
              $rs->can_edit = 'Y';
            }
          }

          $rs->no = $no;
          $rs->totalLabel = number($rs->total_amount, 2);
          $no++;

          $ds[] = $rs;
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('notfound');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function update_order()
  {
    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
			$this->load->model('masters/warehouse_model');

      $order = $this->order_sponsor_model->get($data->code);

      if( ! empty($order))
      {
        if($order->state > 1)
        {
          $ds = array(
            'remark' => get_null($data->remark)
          );
        }
        else
        {
					$wh = $this->warehouse_model->get($data->warehouse_code);

          if( ! empty($wh))
          {
            $customer = $this->customers_model->get($data->customer_code);

            if( ! empty($customer))
            {
              $ds = array(
                'customer_code' => $customer->code,
                'customer_name' => $customer->name,
                'date_add' => db_date($data->date_add),
                'user_ref' => $data->empName,
                'warehouse_code' => $wh->code,
                'budget_id' => $data->budget_id,
                'remark' => get_null($data->remark),
                'status' => 0,
    						'id_sender' => NULL
              );
            }
            else
            {
              $sc = FALSE;
              $this->error = "Invalid customer code";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invaild warehouse_code";
          }
        }

        if(! $this->order_sponsor_model->update($data->code, $ds))
        {
          $sc = FALSE;
          $this->error = "ปรับปรุงเอกสารไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง : {$code}";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function add_details()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $order = $this->order_sponsor_model->get($ds->code);

      if( ! empty($order))
      {
        if($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R')
        {
          if( ! empty($ds->items))
          {
            $auz = is_true(getConfig('ALLOW_UNDER_ZERO'));

            $this->db->trans_begin();

            foreach($ds->items as $rs)
            {
              if($sc === FALSE) { break; }

              $item = $this->products_model->get($rs->sku);

              if( ! empty($item) && $rs->qty > 0)
              {
                $qty = intval($rs->qty);
                $stock = $this->get_sell_stock($item->code, $order->warehouse_code);

                if($stock >= $qty OR $item->count_stock == 0 OR $auz)
                {
                  $detail = $this->order_sponsor_model->get_exists_detail($order->code, $item->code);

                  if(empty($detail))
                  {
                    $arr = array(
                      'order_code' => $order->code,
                      'product_code' => $item->code,
                      'product_name' => $item->name,
                      'model_code' => $item->model_code,
                      'unit_code' => $item->unit_code,
                      'cost' => $item->cost,
                      'price' => $item->price,
                      'qty' => $qty,
                      'total_amount' => $qty * $item->price,
                      'is_count' => $item->count_stock
                    );

                    if( ! $this->order_sponsor_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = "Error : Failed to insert item row {$item->code}";
                    }
                  }
                  else
                  {
                    $Qty = $qty + $detail->qty;
                    $total_amount = $Qty * $detail->price;

                    $arr = array(
                      'qty' => $Qty,
                      'total_amount' => $total_amount
                    );

                    if( ! $this->order_sponsor_model->update_detail($detail->id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Error : Update failed for {$item->code}";
                    }
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Error : สินค้าไม่เพียงพอ : {$item->code}";
                }
              }
            }

            if($sc === TRUE)
            {
              if($order->status != 'P')
              {
                $arr = array(
                  'status' => 'P',
                  'update_user' => $this->_user->uname
                );

                $this->order_sponsor_model->update($order->code, $arr);
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_commit();

              $this->order_sponsor_model->recal_total($order->code);
            }
            else
            {
              $this->db->trans_rollback();
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


  //--- update item qty
  public function update_item()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $order = $this->order_sponsor_model->get($ds->code);

      if( ! empty($order))
      {
        if($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R')
        {
          $detail = $this->order_sponsor_model->get_detail($ds->id);

          if( ! empty($detail))
          {
            if($ds->qty > 0)
            {
              $auz = is_true(getConfig('ALLOW_UNDER_ZERO'));
              $Qty = intval($ds->qty);
              $stock = $this->get_sell_stock($detail->product_code, $order->warehouse_code);

              if($stock >= $Qty OR $detail->is_count == 0 OR $auz)
              {
                $total_amount = $Qty * $detail->price;

                $arr = array(
                  'qty' => $Qty,
                  'total_amount' => $total_amount,
                  'update_user' => $this->_user->uname
                );

                if( ! $this->order_sponsor_model->update_detail($detail->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Error : Update failed for {$detail->product_code}";
                }
                else
                {
                  $this->order_sponsor_model->recal_total($order->code);
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "Error : สินค้าไม่เพียงพอ : {$detail->product_code}";
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


  public function update_item_price()
  {
    $sc = TRUE;
    $code = $this->input->post('order_code');
    $price = $this->input->post('price');
    $id = $this->input->post('id');

    $order = $this->order_sponsor_model->get($code);

    if( ! empty($order))
    {
      if($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R')
      {
        $detail = $this->order_sponsor_model->get_detail($id);

        //--- ถ้ารายการนี้มีอยู่
  			if( ! empty($detail))
  			{
					$total_amount = ( $detail->qty * $price );

					$arr = array(
						'price' => $price,
						'total_amount' => $total_amount,
						'update_user' => $this->_user->uname
					);

					if( ! $this->order_sponsor_model->update_detail($id, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update item price";
          }
          else
          {
            $this->order_sponsor_model->recal_total($code);
          }
  			}
        else
        {
          $sc = FALSE;
          $this->error = "Item not found in order or has removed from order";
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
      $this->error = "Invalid order number";
    }

    $this->_response($sc);
  }


  public function remove_detail()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $id = $this->input->post('id');

    $order = $this->order_sponsor_model->get($code);

    if( ! empty($order))
    {
      if($order->status == 'P' OR $order->status == 'O' OR $order->status == 'R')
      {
        if( ! $this->order_sponsor_model->delete_detail($id))
        {
          $sc = FALSE;
          $this->error = "Failed to delete item";
        }
        else
        {
          $this->order_sponsor_model->recal_total($code);
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

    $this->_response($sc);
  }


  public function save($code)
  {
    $sc = TRUE;
    $order = $this->order_sponsor_model->get($code);

    //---- check credit balance
    $amount = $this->order_sponsor_model->get_order_total_amount($code);

    $bd = $this->sponsor_budget_model->get_valid_budget($order->budget_id);

    if( ! empty($bd))
    {
      $commit = $this->sponsor_budget_model->get_commit_amount($order->budget_id, $order->code);

      $available = $bd->balance - $commit;

      if($available >= $amount)
      {
        $arr = array(
          'status' => 1,
          'is_approved' => 0
        );

        if( ! $this->order_sponsor_model->update($order->code, $arr))
        {
          $sc = FALSE;
          $this->error = "บันทึกออเดอร์ไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "งบคงเหลือไม่เพียงพอ <br/>Balance : ".number($bd->balance, 2)."<br/>Commited : ".number($commit, 2)."<br/>Available : ".number($available, 2);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบงบประมาณที่ใช้ได้";
    }

		if(empty($order->id_sender))
		{
			$this->load->model('masters/sender_model');
			$id_sender = NULL;

			$sender = $this->sender_model->get_customer_sender_list($order->customer_code);

			if( ! empty($sender))
			{
				if( ! empty($sender->main_sender))
				{
					$id_sender = $sender->main_sender;
				}
			}

			if( ! empty($id_sender))
			{
				$arr = array(
					'id_sender' => $id_sender
				);

				$this->order_sponsor_model->update($order->code, $arr);
			}
		}

    $this->_response($sc);
  }


  public function get_sell_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    //---- sell stock = stock in zone + buffer + cancel
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse, $zone);
    $ordered = $this->orders_model->get_reserv_stock($item_code, $warehouse, $zone);
    $reserv_stock = $this->reserv_stock_model->get_reserv_stock($item_code, $warehouse);
    $availableStock = $sell_stock - $ordered - $reserv_stock;
		return $availableStock < 0 ? 0 : $availableStock;
  }


  public function set_sender()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $id_sender = $this->input->post('id_sender');

    if( ! empty($code) && ! empty($id_sender))
    {
      if( ! $this->order_sponsor_model->update($code, ['id_sender' => $id_sender]))
      {
        $sc = FALSE;
        $this->error = "Failed to update data";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function set_address()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $id_address = $this->input->post('id_address');

    if( ! empty($code) && ! empty($id_address))
    {
      if( ! $this->order_sponsor_model->update($code, ['id_address' => $id_address]))
      {
        $sc = FALSE;
        set_error('update');
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
    $prefix = getConfig('PREFIX_SPONSOR');
    $run_digit = getConfig('RUN_DIGIT_SPONSOR');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_sponsor_model->get_max_code($pre);
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
      'sp_code',
      'sp_customer',
      'sp_user',
      'sp_warehouse',
      'sp_is_approved',
      'sp_fromDate',
      'sp_toDate',
      'sp_status'
    );

    return clear_filter($filter);
  }
}
?>
