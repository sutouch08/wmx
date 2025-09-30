
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_order extends PS_Controller
{
  public $menu_code = 'ICODDO';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'รายการรอเปิดบิล';
  public $filter;
  public $error;
  public $logs;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/delivery_order';
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'ic_code', ''),
      'so_no' => get_filter('so_no', 'ic_so_no', ''),
      'reference' => get_filter('reference', 'ic_reference', ''),
      'customer' => get_filter('customer', 'ic_customer', ''),
      'user' => get_filter('user', 'ic_user', 'all'),
      'role' => get_filter('role', 'ic_role', 'all'),
      'channels' => get_filter('channels', 'ic_channels', ''),
      'from_date' => get_filter('from_date', 'ic_from_date', ''),
      'to_date' => get_filter('to_date', 'ic_to_date', ''),
      'ship_from_date' => get_filter('ship_from_date', 'ic_ship_from_date', ''),
      'ship_to_date' => get_filter('ship_to_date', 'ic_ship_to_date', ''),
      'sort_by' => get_filter('sort_by', 'ic_sort_by', ''),
      'order_by' => get_filter('order_by', 'ic_order_by', ''),
      'warehouse' => get_filter('warehouse', 'ic_warehouse', 'all'),
      'is_hold' => get_filter('is_hold', 'ic_is_hold', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $this->load->model('masters/customers_model');
      $this->load->helper('channels');
      $this->load->helper('order');
      $this->load->helper('warehouse');

      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();

      $segment  = 4; //-- url segment
      $rows     = $this->delivery_order_model->count_rows($filter, 7);
      //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
      $init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $orders   = $this->delivery_order_model->get_list($filter, $perpage, $this->uri->segment($segment), 7);

      $filter['orders'] = $orders;

      $this->pagination->initialize($init);
      $this->load->view('inventory/delivery_order/delivery_list', $filter);
    }
  }


  public function is_cancel($reference, $channels, $shop_id)
  {
    $is_cancel = FALSE;

    if($channels == getConfig('TIKTOK_CHANNELS_CODE') && is_true(getConfig('WRX_TIKTOK_API')))
    {
      $this->load->library('wrx_tiktok_api');

      $order_status = $this->wrx_tiktok_api->get_order_status($reference, $shop_id);

      if($order_status == '140')
      {
        $is_cancel = TRUE;
      }

      return $is_cancel;
    }

    if($channels == getConfig('SHOPEE_CHANNELS_CODE') && is_true(getConfig('WRX_SHOPEE_API')))
    {
      $this->load->library('wrx_shopee_api');

      $order_status = $this->wrx_shopee_api->get_order_status($reference, $shop_id);

      if($order_status == 'CANCELLED' OR $order_status == 'IN_CANCEL')
      {
        $is_cancel = TRUE;
      }

      return $is_cancel;
    }

    if($channels == getConfig('LAZADA_CHANNELS_CODE') && is_true(getConfig('WRX_LAZADA_API')))
    {
      $this->load->library('wrx_lazada_api');

      $order_status = $this->wrx_lazada_api->get_order_status($reference, $shop_id);

      if($order_status == 'canceled' OR $order_status == 'CANCELED' OR $order_status == 'Canceled')
      {
        $is_cancel = TRUE;
      }

      return $is_cancel;
    }

    return $is_cancel;
  }


  public function confirm_order()
  {
    $sc = TRUE;

    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/qc_model');
    $this->load->model('inventory/cancle_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('stock/stock_model');
    $this->load->helper('discount');

    $code = $this->input->post('order_code');
    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      $wrx_api = is_true(getConfig('WRX_API'));
      $lazada_code = getConfig('LAZADA_CHANNELS_CODE');
      $shopee_code = getConfig('SHOPEE_CHANNELS_CODE');
      $tiktok_code = getConfig('TIKTOK_CHANNELS_CODE');

      //--- check cancel request
      if($this->orders_model->is_cancel_request($order->code))
      {
        $sc = FALSE;
        $this->error = "ออเดอร์ถูกยกเลิกบน Platform แล้ว";
      }

      if($sc === TRUE)
      {
        if($wrx_api)
        {
          if( ! empty($order->reference))
          {
            if($order->channels_code == $tiktok_code OR $order->channels_code == $shopee_code OR $order->channels_code == $lazada_code)
            {
              if($this->is_cancel($order->reference, $order->channels_code, $order->shop_id))
              {
                $sc = FALSE;
                $this->error = "ออเดอร์ถูกยกเลิกบน Platform แล้ว";

                $this->orders_model->update($order->code, ['is_cancled' => 1]);
              }
            }
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Order Not Found";
    }

    if($sc === TRUE)
    {
			$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $order->date_add : now();
      $toWhsCode = NULL;

      //-- กรณี สปอนเซอร์
      if($order->role == 'P')
      {
        $this->load->model('masters/sponsor_budget_model');
      }

      if($order->state == 7)
      {
        $this->db->trans_begin();

        //--- change state
        $this->orders_model->change_state($code, 8);

        if(empty($order->shipped_date))
        {
          $this->orders_model->update($code, array('shipped_date' => now())); //--- update shipped date
        }

        //--- add state event
        $arr = array(
          'order_code' => $code,
          'state' => 8,
          'update_user' => $this->_user->uname
        );

        $this->order_state_model->add_state($arr);

        //---- รายการทีรอการเปิดบิล
        $bill = $this->delivery_order_model->get_bill_detail($code);

        $docTotal = 0;

        if( ! empty($bill))
        {
          foreach($bill as $rs)
          {
            //--- ถ้ามีรายการที่ไมสำเร็จ ออกจาก loop ทันที
            if($sc === FALSE)
            {
              break;
            }

            //--- get prepare and qc
            $rs->qc = $this->qc_model->get_sum_qty($code, $rs->product_code, $rs->id);

            if($rs->qc > 0)
            {
              //--- ถ้ายอดตรวจ น้อยกว่า หรือ เท่ากับ ยอดสั่ง ใช้ยอดตรวจในการตัด buffer
              //--- ถ้ายอดตวจ มากกว่า ยอดสั่ง ให้ใช้ยอดสั่งในการตัด buffer (บางทีอาจมีการแก้ไขออเดอร์หลังจากมีการตรวจสินค้าแล้ว)
              $sell_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;

              //--- ดึงข้อมูลสินค้าที่จัดไปแล้วตามสินค้า
              $buffers = $this->buffer_model->get_details($code, $rs->product_code, $rs->id);

              if( ! empty($buffers))
              {
                $no = 0;

                foreach($buffers as $rm)
                {
                  if($sell_qty > 0)
                  {
                    //--- ถ้ายอดใน buffer น้อยกว่าหรือเท่ากับยอดสั่งซื้อ (แยกแต่ละโซน น้อยกว่าหรือเท่ากับยอดสั่ง (ซึ่งควรเป็นแบบนี้))
                    $buffer_qty = $rm->qty <= $sell_qty ? $rm->qty : $sell_qty;

                    //--- ทำยอดให้เป็นลบเพื่อตัดยอดออก เพราะใน function  ใช้การบวก
                    $qty = $buffer_qty * (-1);

                    //--- 1. ตัดยอดออกจาก buffer
                    //--- นำจำนวนติดลบบวกกลับเข้าไปใน buffer เพื่อตัดยอดให้น้อยลง

                    if($this->buffer_model->update($rm->order_code, $rm->product_code, $rm->zone_code, $qty, $rs->id) !== TRUE)
                    {
                      $sc = FALSE;
                      $this->error = 'ปรับยอดใน buffer ไม่สำเร็จ';
                      break;
                    }

                    //--- ลดยอด sell qty ลงตามยอด buffer ทีลดลงไป
                    $sell_qty += $qty;

                    //--- 2. update movement
                    $arr = array(
                      'reference' => $order->code,
                      'warehouse_code' => $rm->warehouse_code,
                      'zone_code' => $rm->zone_code,
                      'product_code' => $rm->product_code,
                      'move_in' => 0,
                      'move_out' => $buffer_qty,
                      'date_add' => $date_add
                    );

                    if( ! $this->movement_model->add($arr))
                    {
                      $sc = FALSE;
                      $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                      break;
                    }

                    $total_amount = $rs->final_price * $buffer_qty;
                    $docTotal += $total_amount;

                    //--- ข้อมูลสำหรับบันทึกยอดขาย
                    $arr = array(
                      'role' => $order->role,
                      'reference' => $order->code,
                      'so_no' => $order->so_no,
                      'fulfillment_code' => $order->fulfillment_code,
                      'oracle_id' => $order->oracle_id,
                      'line_id' => $rs->line_id,
                      'payment_code' => $order->payment_code,
                      'channels_code' => $order->channels_code,
                      'product_code' => $rs->product_code,
                      'product_name' => $rs->product_name,
                      'product_model' => $rs->model_code,
                      'unit_code' => $rs->unit_code,
                      'cost' => $rs->cost,
                      'price' => $rs->price,
                      'sell' => $rs->final_price,
                      'qty' => $buffer_qty,
                      'discount_label' => discountLabel($rs->discount1),
                      'discount_amount' => ($rs->discount_amount * $buffer_qty),
                      'total_amount' => $total_amount,
                      'customer_code' => $order->customer_code,
                      'customer_ref' => $order->customer_ref,
                      'user' => $order->user,
                      'date_add' => $date_add,
                      'zone_code' => $rm->zone_code,
                      'warehouse_code' => $rm->warehouse_code,
                      'update_user' => $this->_user->uname,
                      'budget_id' => $order->budget_id,
                      'budget_code' => $order->budget_code,
                      'is_count' => $rs->is_count,
                      'order_detail_id' => $rs->id
                    );

                    //--- 3. บันทึกยอดขาย
                    if($this->delivery_order_model->sold($arr) !== TRUE)
                    {
                      $sc = FALSE;
                      $this->error = 'บันทึกขายไม่สำเร็จ';
                      break;
                    }
                  } //--- end if sell_qty > 0
                } //--- end foreach $buffers
              } //--- end if wmpty ($buffers)

            }
          } //--- end foreach $bill
        } //--- end if empty($bill)

        //--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
        $buffer = $this->buffer_model->get_all_details($code);
        //--- ถ้ายังมีรายการที่ค้างอยู่ใน buffer เคลียร์เข้า cancle
        if( ! empty($buffer))
        {
          foreach($buffer as $rs)
          {
            if($rs->qty != 0)
            {
              $arr = array(
                'order_code' => $rs->order_code,
                'product_code' => $rs->product_code,
                'warehouse_code' => $rs->warehouse_code,
                'zone_code' => $rs->zone_code,
                'qty' => $rs->qty,
                'user' => $this->_user->uname,
                'order_detail_id' => $rs->order_detail_id
              );

              if($this->cancle_model->add($arr) === FALSE)
              {
                $sc = FALSE;
                $this->error = 'เคลียร์ยอดค้างเข้า cancle ไม่สำเร็จ';
                break;
              }
            }

            if($this->buffer_model->delete($rs->id) === FALSE)
            {
              $sc = FALSE;
              $this->error = 'ลบ Buffer ที่ค้างอยู่ไม่สำเร็จ';
              break;
            }
          }
        }


        //--- บันทึกขายรายการที่ไม่นับสต็อก
        $bill = $this->delivery_order_model->get_non_count_bill_detail($order->code);

        if( ! empty($bill))
        {
          foreach($bill as $rs)
          {
            $total_amount = $rs->final_price * $rs->qty;
            $docTotal += $total_amount;

            //--- ข้อมูลสำหรับบันทึกยอดขาย
            $arr = array(
              'role' => $order->role,
              'reference' => $order->code,
              'so_no' => $order->so_no,
              'fulfillment_code' => $order->fulfillment_code,
              'oracle_id' => $order->oracle_id,
              'line_id' => $rs->line_id,
              'payment_code' => $order->payment_code,
              'channels_code' => $order->channels_code,
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'product_model' => $rs->model_code,
              'unit_code' => $rs->unit_code,
              'cost' => $rs->cost,
              'price' => $rs->price,
              'sell' => $rs->final_price,
              'qty' => $rs->qty,
              'discount_label' => discountLabel($rs->discount1),
              'discount_amount' => ($rs->discount_amount * $rs->qty),
              'total_amount' => $total_amount,
              'customer_code' => $order->customer_code,
              'customer_ref' => $order->customer_ref,
              'user' => $order->user,
              'date_add' => $date_add,
              'zone_code' => NULL,
              'warehouse_code' => NULL,
              'update_user' => $this->_user->uname,
              'budget_id' => $order->budget_id,
              'budget_code' => $order->budget_code,
              'is_count' => $rs->is_count,
              'order_detail_id' => $rs->id
            );

            //--- 3. บันทึกยอดขาย
            if($this->delivery_order_model->sold($arr) !== TRUE)
            {
              $sc = FALSE;
              $this->error = 'บันทึกขายไม่สำเร็จ';
              break;
            }
          }
        }

        if($sc === TRUE)
        {
          if( ! $this->orders_model->update($code, array('doc_total' => $docTotal)))
          {
            $sc = FALSE;
            $this->error = "Failed to update doc total";
          }
        }

        if($sc === TRUE && $order->role == 'P')
        {
          $bd = $this->sponsor_budget_model->get_valid_budget($order->budget_id);

          if( ! empty($bd))
          {
            if($bd->balance < $docTotal)
            {
              $sc = FALSE;
              $this->error = "งบประมาณคงเหลือไม่พอ <br/>คงเหลือ : ".number($bd->balance, 2);
            }

            if($sc === TRUE)
            {
              if( ! $this->sponsor_budget_model->update_used($bd->id, $docTotal))
              {
                $sc = FALSE;
                $this->error = "Failed to update outstanding budget";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบงบประมาณที่ใช้";
          }
        }

        if($sc === TRUE)
        {
          $this->orders_model->set_complete($code);
        }

        if($sc === TRUE)
        {
          $this->db->trans_commit();

          if(is_true(getConfig('WRX_OB_INTERFACE')))
          {
            $this->load->library('wrx_ob_api');

            if( ! $this->wrx_ob_api->update_status($code))
            {
              $sc = FALSE;
              $this->error = "บันทึกขายสำเร็จแต่ส่งข้อมูลไป ERP ไม่สำเร็จ : ERP Error - ".$this->wrx_ob_api->error;

              $arr = array(
                'is_exported' => 3
              );

              $this->orders_model->update($code, $arr);
            }
            else
            {
              $arr = array(
                'is_exported' => 1
              );

              $this->orders_model->update($code, $arr);
            }
          }
        }
        else
        {
          $this->db->trans_rollback();
        }
      } //--- end if state == 7
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order status";
      }
    }

    $this->_response($sc);
  }


  public function view_detail($code)
  {
    ini_set('max_execution_time', 600);

    $this->load->model('masters/customers_model');
    $this->load->model('inventory/qc_model');
		$this->load->model('masters/warehouse_model');
		$this->load->model('masters/channels_model');
		$this->load->model('masters/payment_methods_model');
    $this->load->helper('order');
    $this->load->helper('discount');

    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
		$order->warehouse_name = $this->warehouse_model->get_name($order->warehouse_code);
		$order->channels_name = $this->channels_model->get_name($order->channels_code);
		$order->payment_name = $this->payment_methods_model->get_name($order->payment_code);

    if($order->role == 'C' OR $order->role == 'N' OR $order->role == 'L')
    {
      $this->load->model('masters/zone_model');
      $order->zone_name = $this->zone_model->get_name($order->zone_code);
    }

    $details = $this->delivery_order_model->get_billed_detail($code);
    $box_list = $this->qc_model->get_box_list($code);
    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['box_list'] = $box_list;
    $this->load->view('inventory/delivery_order/bill_detail', $ds);
  }


	public function update_shipped_date()
	{
		$sc = TRUE;
		$code = $this->input->post('order_code');
		$date = db_date($this->input->post('shipped_date'), FALSE);

		$arr = array(
			'shipped_date' => $date,
			'update_user' => get_cookie('uname')
		);

		if( ! $this->orders_model->update($code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update Shipped data failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function get_state()
  {
    $code = $this->input->get('order_code');
    $state = $this->orders_model->get_state($code);
    echo $state;
  }

  public function clear_filter()
  {
    $filter = array(
      'ic_code',
      'ic_so_no',
      'ic_reference',
      'ic_customer',
      'ic_user',
      'ic_role',
      'ic_channels',
      'ic_from_date',
      'ic_to_date',
      'ic_ship_from_date',
      'ic_ship_to_date',
      'ic_sort_by',
      'ic_order_by',
      'ic_warehouse',
      'ic_is_hold'
    );

    return clear_filter($filter);
  }

} //--- end class
?>
