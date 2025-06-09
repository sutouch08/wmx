<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends PS_Controller
{
  public $menu_code = 'SOODSO';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ออเดอร์';
  public $filter;
  public $error;
	public $logs; //--- logs database;
  public $sync_api_stock = FALSE;
  public $ix_warehouse = NULL;
  public $log_delete = TRUE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/orders';
    $this->load->model('orders/orders_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/product_model_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/discount_model');
    $this->load->model('orders/reserv_stock_model');
    //--- เฉพาะกิจ
    $this->load->model('inventory/transfer_model');

    $this->load->helper('order');
    $this->load->helper('channels');
    $this->load->helper('payment_method');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('state');
    $this->load->helper('product_images');
    $this->load->helper('discount');
    $this->load->helper('warehouse');

    $this->filter = getConfig('STOCK_FILTER');
    $this->sync_api_stock = is_true(getConfig('SYNC_IX_STOCK'));
    $this->ix_warehouse = getConfig('IX_WAREHOUSE');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'order_code', ''),
      'so_no' =>  get_filter('so_no', 'so_no', ''),
      'reference' => get_filter('reference', 'order_reference', ''),
      'customer' => get_filter('customer', 'order_customer', ''),
      'user' => get_filter('user', 'order_user', 'all'),
      'ship_code' => get_filter('shipCode', 'order_shipCode', ''),
      'channels' => get_filter('channels', 'order_channels', 'all'),
      'payment' => get_filter('payment', 'order_payment', 'all'),
      'from_date' => get_filter('fromDate', 'order_fromDate', ''),
      'to_date' => get_filter('toDate', 'order_toDate', ''),
      'warehouse' => get_filter('warehouse', 'order_warehouse', 'all'),
      'notSave' => get_filter('notSave', 'notSave', NULL),
      'onlyMe' => get_filter('onlyMe', 'onlyMe', NULL),
      'isExpire' => get_filter('isExpire', 'isExpire', NULL),
			'method' => get_filter('method', 'method', 'all'),
      'order_by' => get_filter('order_by', 'order_order_by', ''),
      'sort_by' => get_filter('sort_by', 'order_sort_by', ''),
      'stated' => get_filter('stated', 'stated', ''),
      'startTime' => get_filter('startTime', 'startTime', ''),
      'endTime' => get_filter('endTime', 'endTime', ''),
      'is_pre_order' => get_filter('is_pre_order', 'is_pre_order', 'all'),
      'is_backorder' => get_filter('is_backorder', 'is_backorder', 'all')
    );

    $state = array(
      '1' => get_filter('state_1', 'state_1', 'N'),
      '2' => get_filter('state_2', 'state_2', 'N'),
      '3' => get_filter('state_3', 'state_3', 'N'),
      '4' => get_filter('state_4', 'state_4', 'N'),
      '5' => get_filter('state_5', 'state_5', 'N'),
      '6' => get_filter('state_6', 'state_6', 'N'),
      '7' => get_filter('state_7', 'state_7', 'N'),
      '8' => get_filter('state_8', 'state_8', 'N'),
      '9' => get_filter('state_9', 'state_9', 'N')
    );

    $state_list = array();

    $button = array();

    for($i =1; $i <= 9; $i++)
    {
    	if($state[$i] === 'Y')
    	{
    		$state_list[] = $i;
    	}

      $btn = 'state_'.$i;
      $button[$btn] = $state[$i] === 'Y' ? 'btn-info' : '';
    }

    $button['not_save'] = empty($filter['notSave']) ? '' : 'btn-info';
    $button['only_me'] = empty($filter['onlyMe']) ? '' : 'btn-info';
    $button['is_expire'] = empty($filter['isExpire']) ? '' : 'btn-info';


    $filter['state_list'] = empty($state_list) ? NULL : $state_list;

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();
      $segment  = 4; //-- url segment
      $startTime = now();
      $rows = $this->orders_model->count_rows($filter);
      //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
      $init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $offset = $rows < $this->uri->segment($segment) ? NULL : $this->uri->segment($segment);
      $orders = $this->orders_model->get_list($filter, $perpage, $offset);

      $endTime = now();

      $filter['orders'] = $orders; //$ds;
      $filter['state'] = $state;
      $filter['channelsList'] = $this->channels_model->get_channels_array();
      $filter['paymentList'] = $this->payment_methods_model->get_payment_array();
      $filter['btn'] = $button;
      $filter['start'] = $startTime;
      $filter['end'] = $endTime;

      $this->pagination->initialize($init);
      $this->load->view('orders/orders_list', $filter);
    }
  }


  //---- รายการรออนุมัติ
  public function get_un_approve_list()
  {
    $role = $this->input->get('role');
    $rows = $this->orders_model->count_un_approve_rows($role);
    $limit = empty($this->input->get('limit')) ? 10 : intval($this->input->get('limit'));
    $list = $this->orders_model->get_un_approve_list($role, $limit);


    $result_rows = empty($list) ? 0 :count($list);

    $ds = array();
    if( ! empty($list))
    {
      foreach($list as $rs)
      {
        $arr = array(
          'date_add' => thai_date($rs->date_add),
          'code' => $rs->code,
          'customer' => $rs->customer_name,
          'empName' => $rs->empName
        );

        array_push($ds, $arr);
      }
    }

    $data = array(
      'result_rows' => $result_rows,
      'rows' => $rows,
      'data' => $ds
    );

    echo json_encode($data);
  }


  public function add_new()
  {
    $this->load->view('orders/orders_add');
  }


  public function add()
  {
    $sc = TRUE;
    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $this->load->model('inventory/invoice_model');
			$this->load->model('masters/warehouse_model');
			$this->load->model('masters/sender_model');
      $this->load->model('address/address_model');
      $date_add = db_date($data->date_add);
      $code = $this->get_new_code($date_add);

      $customer = $this->customers_model->get($data->customer_code);
			$customer_ref = trim($data->customer_ref);
      $role = 'S'; //--- S = ขาย
      $payment = $this->payment_methods_model->get($data->payment_code);
      $has_term = empty($payment) ? FALSE : ($payment->role == 4 ? FALSE : (is_true($payment->has_term)));

      if($sc === TRUE)
      {
        $ds = array(
          'date_add' => $date_add,
          'code' => $code,
          'role' => $role,
          'reference' => get_null($data->reference),
          'customer_code' => $customer->code,
          'customer_name' => $customer->name,
          'customer_ref' => $customer_ref,
          'channels_code' => $data->channels_code,
          'payment_code' => $data->payment_code,
          'warehouse_code' => $data->warehouse_code,
          'is_term' => ($has_term === TRUE ? 1 : 0),
          'user' => $this->_user->uname,
          'remark' => get_null($data->remark),
					'id_sender' => $this->sender_model->get_main_sender($customer->code),
          'is_pre_order' => $data->is_pre_order
        );

        if( ! $this->orders_model->add($ds))
        {
          $sc = FALSE;
          $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
        }

        if($sc === TRUE)
        {
          $arr = array(
            'order_code' => $code,
            'state' => 1,
            'update_user' => $this->_user->uname
          );

          $this->order_state_model->add_state($arr);
        }
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
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function add_detail($order_code)
  {
    $sc = TRUE;
    $auz = getConfig('ALLOW_UNDER_ZERO');
		$sync_stock = array();
    $err = 0;
    $data = $this->input->post('data');
    $order = $this->orders_model->get($order_code);

    if( empty($order))
    {
      $sc = FALSE;
      $this->error = "Invalid order code";
    }
    else
    {
      if($order->state != 1)
      {
        $sc = FALSE;
        $this->error = "สถานะออเดอร์ไม่ถูกต้อง กรุณาตรวจสอบ";
      }
    }

    if($sc === TRUE)
    {
      if( ! empty($data))
      {
        foreach($data as $rs)
        {
          $code = $rs['code']; //-- รหัสสินค้า
          $qty = $rs['qty'];
          $item = $this->products_model->get($code);

          if( $qty > 0 && !empty($item))
          {
            $qty = ceil($qty);

            //---- ยอดสินค้าที่่สั่งได้
            $sumStock = $this->get_sell_stock($item->code, $order->warehouse_code);


            //--- ถ้ามีสต็อกมากว่าที่สั่ง หรือ เป็นสินค้าไม่นับสต็อก
            if( $sumStock >= $qty OR $item->count_stock == 0 OR $auz == 1)
            {
              //---- ถ้ายังไม่มีรายการในออเดอร์
              //--- อาจจะได้มากกกว่า 1 บรรทัด แต่จะเอามาแค่บรรทัดเดียว
              $detail = $this->orders_model->get_exists_detail($order_code, $item->code, $item->price);

              if(empty($detail))
              {
                //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
                $discount = array(
                  'amount' => 0,
                  'id_rule' => NULL,
                  'discLabel1' => 0,
                  'discLabel2' => 0,
                  'discLabel3' => 0
                );

                if($order->role == 'S')
                {
                  $discount = $this->discount_model->get_item_discount($item->code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add, $order->code);
                }

                if($order->role == 'C' OR $order->role == 'N')
                {
                  $gp = $order->gp;
                  //------ คำนวณส่วนลดใหม่
                  $step = explode('+', $gp);
                  $discAmount = 0;
                  $discLabel = array(0, 0, 0);
                  $price = $item->price;
                  $i = 0;
                  foreach($step as $discText)
                  {
                    if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
                    {
                      $disc = explode('%', $discText);
                      $disc[0] = floatval(trim($disc[0])); //--- ตัดช่องว่างออก
                      $amount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
                      $discLabel[$i] = count($disc) == 1 ? $disc[0] : $disc[0].'%';
                      $discAmount += $amount;
                      $price -= $amount;
                    }

                    $i++;
                  }

                  $total_discount = $qty * $discAmount; //---- ส่วนลดรวม
                  //$total_amount = ( $qty * $price ) - $total_discount; //--- ยอดรวมสุดท้าย
                  $discount['amount'] = $total_discount;
                  $discount['discLabel1'] = $discLabel[0];
                  $discount['discLabel2'] = $discLabel[1];
                  $discount['discLabel3'] = $discLabel[2];
                }

                $arr = array(
                  "order_code"	=> $order_code,
                  "model_code"		=> $item->model_code,
                  "product_code"	=> $item->code,
                  "product_name"	=> addslashes($item->name),
                  "unit_code" => $item->unit_code,
                  "cost"  => $item->cost,
                  "price"	=> $item->price,
                  "qty"		=> $qty,
                  "discount1"	=> $discount['discLabel1'],
                  "discount2" => $discount['discLabel2'],
                  "discount3" => $discount['discLabel3'],
                  "discount_amount" => $discount['amount'],
                  "total_amount"	=> ($item->price * $qty) - $discount['amount'],
                  "id_rule"	=> get_null($discount['id_rule']),
                  "is_count" => $item->count_stock
                );

                if( $this->orders_model->add_detail($arr) === FALSE )
                {
                  $sc = FALSE;
                  $this->error = "Error : Insert fail";
                  $err++;
                }
                else
                {
                  //---- update chatbot stock
                  if($item->count_stock == 1 && $item->is_api == 1 && $this->sync_api_stock && ! $order->is_pre_order)
                  {
                    if($order->warehouse_code == $this->ix_warehouse)
                    {
                      $sync_stock[] = (object) array('code' => $item->code, 'rate' => $item->api_rate);
                    }
                  }
                }
              }
              else  //--- ถ้ามีรายการในออเดอร์อยู่แล้ว
              {
                $qty = $qty + $detail->qty;

                $discount = array(
                  'amount' => 0,
                  'id_rule' => NULL,
                  'discLabel1' => 0,
                  'discLabel2' => 0,
                  'discLabel3' => 0
                );

                //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
                if($order->role == 'S')
                {
                  $discount 	= $this->discount_model->get_item_discount($item->code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add, $order->code);
                }

                $arr = array(
                  "qty"		=> $qty,
                  "discount1"	=> $discount['discLabel1'],
                  "discount2" => $discount['discLabel2'],
                  "discount3" => $discount['discLabel3'],
                  "discount_amount" => $discount['amount'],
                  "total_amount"	=> ($item->price * $qty) - $discount['amount'],
                  "id_rule"	=> get_null($discount['id_rule']),
                  "valid" => 0,
                  "valid_qc" => 0
                );

                if( $this->orders_model->update_detail($detail->id, $arr) === FALSE )
                {
                  $sc = FALSE;
                  $this->error = "Error : Update Fail";
                  $err++;
                }
                else
                {
                  //---- update chatbot stock
                  if($item->count_stock == 1 && $item->is_api == 1 && $this->sync_api_stock && ! $order->is_pre_order)
                  {
                    if($order->warehouse_code == $this->ix_warehouse)
                    {
                      $sync_stock[] = (object) array('code' => $item->code, 'rate' => $item->api_rate);
                    }
                  }
                }

              }	//--- end if isExistsDetail
            }
            else 	// if getStock
            {
              $sc = FALSE;
              $this->error = "Error : สินค้าไม่เพียงพอ : {$item->code}";
              $err++;
            } 	//--- if getStock
          }	//--- if qty > 0
        } //--- end foreach

        if($sc === TRUE)
        {
          $doc_total = $this->orders_model->get_order_total_amount($order_code);
          $arr = array(
            'doc_total' => $doc_total,
            'status' => 0
          );

          $this->orders_model->update($order_code, $arr);
        }

        if($this->sync_api_stock && !empty($sync_stock))
        {
          $this->update_api_stock($sync_stock);
        }
      }
    }


    echo $sc === TRUE ? 'success' : ( $err > 0 ? $this->error.' : '.$err.' item(s)' : $this->error);
  }


  public function add_free_detail($order_code)
  {
    $sc = TRUE;
    $auz = getConfig('ALLOW_UNDER_ZERO');
		$sync_stock = array();
    $err = 0;
    $data = $this->input->post('data');
    $order = $this->orders_model->get($order_code);

    if( empty($order))
    {
      $sc = FALSE;
      $this->error = "Invalid order code";
    }
    else
    {
      if($order->state != 1)
      {
        $sc = FALSE;
        $this->error = "สถานะออเดอร์ไม่ถูกต้อง กรุณาตรวจสอบ";
      }
    }

    if($sc === TRUE)
    {
      if( ! empty($data))
      {
        foreach($data as $rs)
        {
          $code = $rs['code']; //-- รหัสสินค้า
          $qty = $rs['qty'];
          $item = $this->products_model->get($code);

          if( $qty > 0 && !empty($item))
          {
            $qty = ceil($qty);

            //---- ยอดสินค้าที่่สั่งได้
            $sumStock = $this->get_sell_stock($item->code, $order->warehouse_code);


            //--- ถ้ามีสต็อกมากว่าที่สั่ง หรือ เป็นสินค้าไม่นับสต็อก
            if( $sumStock >= $qty OR $item->count_stock == 0 OR $auz == 1)
            {
              //---- ถ้ายังไม่มีรายการในออเดอร์
              //--- อาจจะได้มากกกว่า 1 บรรทัด แต่จะเอามาแค่บรรทัดเดียว
              $detail = $this->orders_model->get_exists_free_detail($order_code, $item->code);

              if(empty($detail))
              {
                $arr = array(
                  "order_code" => $order_code,
                  "model_code" => $item->model_code,
                  "product_code" => $item->code,
                  "product_name" => addslashes($item->name),
                  "unit_code" => $item->unit_code,
                  "cost"  => $item->cost,
                  "price"	=> $item->price,
                  "qty" => $qty,
                  "discount1"	=> '100%',
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $item->price * $qty,
                  "total_amount"	=> 0.00,
                  "id_rule"	=> NULL,
                  "is_count" => $item->count_stock
                );

                if( $this->orders_model->add_detail($arr) === FALSE )
                {
                  $sc = FALSE;
                  $this->error = "Error : Insert fail";
                  $err++;
                }
                else
                {
                  //---- update chatbot stock
                  if($item->count_stock == 1 && $item->is_api == 1 && $this->sync_api_stock && ! $order->is_pre_order)
                  {
                    if($order->warehouse_code == $this->ix_warehouse)
                    {
                      $sync_stock[] = (object) array('code' => $item->code, 'rate' => $item->api_rate);
                    }
                  }
                }
              }
              else  //--- ถ้ามีรายการในออเดอร์อยู่แล้ว
              {
                $qty = $qty + $detail->qty;

                $arr = array(
                  "qty"		=> $qty,
                  "discount_amount" => $item->price * $qty,
                  "total_amount"	=> 0.00,
                  "valid" => 0,
                  "valid_qc" => 0
                );

                if( ! $this->orders_model->update_detail($detail->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Error : Update Fail";
                  $err++;
                }
                else
                {
                  //---- update chatbot stock
                  if($item->count_stock == 1 && $item->is_api == 1 && $this->sync_api_stock && ! $order->is_pre_order)
                  {
                    if($order->warehouse_code == $this->ix_warehouse)
                    {
                      $sync_stock[] = (object) array('code' => $item->code, 'rate' => $item->api_rate);
                    }
                  }
                }

              }	//--- end if isExistsDetail
            }
            else 	// if getStock
            {
              $sc = FALSE;
              $this->error = "Error : สินค้าไม่เพียงพอ : {$item->code}";
              $err++;
            } 	//--- if getStock
          }	//--- if qty > 0
        } //--- end foreach

        if($this->sync_api_stock && !empty($sync_stock))
        {
          $this->update_api_stock($sync_stock);
        }
      }
    }


    echo $sc === TRUE ? 'success' : ( $err > 0 ? $this->error.' : '.$err.' item(s)' : $this->error);
  }

  //--- update item qty
  public function update_item()
	{
		$sc = TRUE;
    $auz = is_true(getConfig('ALLOW_UNDER_ZERO'));
    $code = $this->input->post('order_code');
		$id = $this->input->post('id');
		$qty = $this->input->post('qty');

		if( ! empty($id))
		{
      $order = $this->orders_model->get($code);

      if( ! empty($order))
      {
        if( $order->state < 2)
        {
          $detail = $this->orders_model->get_detail($id);

    			if( ! empty($detail))
    			{
            $this->db->trans_begin();

            $valid = $detail->valid == 1 && $qty > $detail->qty ? 0 : $detail->valid;
            $valid_qc = $detail->valid_qc == 1 && $qty > $detail->qty ? 0 : $detail->valid_qc;
            $discount_label = discountLabel($detail->discount1, $detail->discount2, $detail->discount3);

            if($order->is_pre_order OR $detail->is_count == 0 OR $auz)
            {
              $available = 1000000;
            }
            else
            {
              //---- สต็อกคงเหลือในคลัง
              $sell_stock = $this->stock_model->get_sell_stock($detail->product_code, $order->warehouse_code);

              //---- ยอดจองสินค้า ไม่รวมรายการที่กำหนด
              $ordered = $this->orders_model->get_reserv_stock_exclude($detail->product_code, $order->warehouse_code, $detail->id);
              $reserv_stock = $this->reserv_stock_model->get_reserv_stock($detail->product_code, $order->warehouse_code);

              $available = $sell_stock - $ordered - $reserv_stock;
            }

            if($qty <= $available OR $auz)
            {
              $discount = array(
                'amount' => 0,
                'id_rule' => NULL,
                'discLabel1' => 0,
                'discLabel2' => 0,
                'discLabel3' => 0
              );

              //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
              if($order->role == 'S' && ! $order->is_pre_order)
              {
                $discount = $this->discount_model->get_item_discount($detail->product_code, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add, $order->code);
              }

              $arr = array(
                "qty" => $qty,
                "discount1"	=> $discount['discLabel1'],
                "discount2" => $discount['discLabel2'],
                "discount3" => $discount['discLabel3'],
                "discount_amount" => $discount['amount'],
                "total_amount"	=> ($detail->price * $qty) - $discount['amount'],
                "id_rule"	=> get_null($discount['id_rule']),
                "valid" => $valid,
                "valid_qc" => $valid_qc
              );

              if( ! $this->orders_model->update_detail($id, $arr))
      				{
      					$sc = FALSE;
      					$this->error = "Update failed";
      				}
              else
              {
                $discount_label = discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']);
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "สต็อกคงเหลือไม่พอ : คงเหลือ {$available}";
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
              $doc_total = $this->orders_model->get_order_total_amount($code);
              $arr = array(
                'doc_total' => $doc_total,
                'status' => 0
              );

              $this->orders_model->update($code, $arr);

              //--- update api stock
              if($this->sync_api_stock && $detail->is_count == 1 && ! $order->is_pre_order && $order->warehouse_code == $this->ix_warehouse)
              {
                $sync_stock = array();
                $item = $this->products_model->get($detail->product_code);

                if( ! empty($item))
                {
                  if($item->is_api)
                  {
                    $sync_stock[] = (object) array('code' => $item->code, 'rate' => $item->api_rate);

                    $this->update_api_stock($sync_stock);
                  }
                }
              }
              //--- end update api stock
            }
    			}
    			else
    			{
    				$sc = FALSE;
    				$this->error = "Item Not found";
    			}
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid order state";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order code";
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
      'discLabel' => $sc === TRUE ? $discount_label : NULL
    );

		echo json_encode($arr);
	}


  public function add_pre_order_detail()
  {
    $sc = TRUE;
    $order_code = $this->input->post('order_code');
    $data = json_decode($this->input->post('data'));
    $order = $this->orders_model->get($order_code);

    if( ! empty($data))
    {
      if( ! empty($order))
      {
        if($order->state == 1)
        {
          foreach($data as $rs)
          {
            if($rs->qty > 0)
            {
              $item = $this->products_model->get($rs->code);

              if( ! empty($item))
              {
                $qty = ceil($rs->qty);

                $detail = $this->orders_model->get_exists_detail($order_code, $item->code, $item->price);

                if(empty($detail))
                {
                  $arr = array(
                    "order_code" => $order_code,
                    "model_code" => $item->model_code,
                    "product_code" => $item->code,
                    "product_name" => addslashes($item->name),
                    "unit_code" => $item->unit_code,
                    "cost"  => $item->cost,
                    "price"	=> $item->price,
                    "qty"	=> $qty,
                    "discount1"	=> 0,
                    "discount2" => 0,
                    "discount3" => 0,
                    "discount_amount" => 0,
                    "total_amount"	=> ($item->price * $qty),
                    "id_rule"	=> NULL,
                    "is_count" => $item->count_stock,
                    "pre_order_detail_id" => $rs->id
                  );

                  if( ! $this->orders_model->add_detail($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert item : {$item->code}";
                  }
                }
                else //--- if ! empty detail
                {
                  $qty = $qty + $detail->qty;

                  $arr = array(
                    "qty"	=> $qty,
                    "total_amount"	=> ($item->price * $qty),
                    "valid" => 0
                  );

                  if( ! $this->orders_model->update_detail($detail->id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update item : {$item->code}";
                  }
                } //-- end if ! empty($detail)
              } //-- end if ! empty($item)
            } //-- end if $rs->qty > 0
          } //--- end foreach

          if( $sc === TRUE)
          {
            $doc_total = $this->orders_model->get_order_total_amount($order_code);
            $arr = array(
            'doc_total' => $doc_total,
            'status' => 0
            );

            $this->orders_model->update($order_code, $arr);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะออเดอร์ไม่ถูกต้อง กรุณาตรวจสอบ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Order number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการสินค้า";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function remove_detail($id)
  {
		$sc = TRUE;
    $detail = $this->orders_model->get_detail($id);
		if( ! empty($detail))
		{
			$order = $this->orders_model->get($detail->order_code);

			if( ! empty($order))
			{
				//--- อนุญาติให้ลบได้แค่ 1 สถานะ
				if($order->state == 1)
				{
          $this->db->trans_begin();

          if(! $this->orders_model->remove_detail($id))
          {
            $sc = FALSE;
            $this->error = "Delete filed";
          }

          if($sc === TRUE)
          {
            $doc_total = $this->orders_model->get_order_total_amount($detail->order_code);
            $arr = array('doc_total' => $doc_total);

            if( ! $this->orders_model->update($detail->order_code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update doc total amount";
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
            if($this->log_delete)
            {
              $arr = array(
                'order_code' => $detail->order_code,
                'product_code' => $detail->product_code,
                'qty' => $detail->qty,
                'user' => $this->_user->uname
              );

              $this->orders_model->log_delete($arr);
            }

            if($this->sync_api_stock && $detail->is_count == 1 && ! $order->is_pre_order && $order->warehouse_code == $this->ix_warehouse)
            {
              $item = $this->products_model->get($detail->product_code);

              if(! empty($item))
              {
                if($item->is_api == 1)
                {
                  $sync_stock = array();
                  $sync_stock[] = (object) array('code' => $item->code, 'rate' => $item->api_rate);
                  $this->update_api_stock($sync_stock);
                }
              }
            }
            //--- sync api stock
          }
				}
				else
				{
					$sc = FALSE;
					$this->error = "สถานะออเดอร์ไม่ถูกต้อง กรุณาตรวจสอบ";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Order not found";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Item not found";
		}

		echo $sc === TRUE ? 'success' : $this->error;

  }


  public function edit_order($code)
  {
    $this->load->model('address/address_model');
    $this->load->model('masters/bank_model');
    $this->load->model('orders/order_payment_model');
    $this->load->helper('bank');
		$this->load->helper('sender');

    $ds = array();

    $rs = $this->orders_model->get($code);

    if( ! empty($rs))
    {
      $rs->channels_name = $this->channels_model->get_name($rs->channels_code);
      $rs->payment_name = $this->payment_methods_model->get_name($rs->payment_code);
      $rs->customer_name = empty($rs->customer_name) ? $this->customers_model->get_name($rs->customer_code) : $rs->customer_name;
      $rs->total_amount = $rs->doc_total <= 0 ? $this->orders_model->get_order_total_amount($rs->code) : $rs->doc_total;
      $rs->user = $this->user_model->get_name($rs->user);
      $rs->state_name = get_state_name($rs->state);
      $rs->has_payment = $this->order_payment_model->is_exists($code);

			$state = $this->order_state_model->get_order_state($code);

	    $ost = array();

	    if( ! empty($state))
	    {
	      foreach($state as $st)
	      {
	        $ost[] = $st;
	      }
	    }

	    $details = $this->orders_model->get_order_details($code);
	    $ship_to = $this->address_model->get_ship_to_address($code);
      $tracking = $this->orders_model->get_order_tracking($code);
      $backlogs = $rs->is_backorder == 1 ? $this->orders_model->get_backlogs_details($rs->code) : NULL;

      if(empty($ship_to))
      {
        $ship_to = (object)array(
          'id' => NULL,
          'name' => NULL,
          'address' => NULL,
          'sub_district' => NULL,
          'district' => NULL,
          'province' => NULL,
          'postcode' => NULL,
          'country' => NULL,
          'phone' => NULL,
          'email' => NULL,
          'alias' => NULL
        );
      }

	    $ds['state'] = $ost;
	    $ds['order'] = $rs;
	    $ds['details'] = $details;
	    $ds['ship_to']  = $ship_to;
      $ds['tracking'] = $tracking;
      $ds['backlogs'] = $backlogs;
			$ds['cancle_reason'] = ($rs->state == 9 ? $this->orders_model->get_cancle_reason($code) : NULL);
	    $this->load->view('orders/order_edit', $ds);
    }
		else
		{
			$err = "ไม่พบเลขที่เอกสาร : {$code}";
			$this->page_error($err);
		}
  }


  public function update_order()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $this->load->model('inventory/invoice_model');
			$this->load->model('masters/warehouse_model');
      $code = $this->input->post('order_code');
      $recal = $this->input->post('recal');
      $payment = $this->payment_methods_model->get($this->input->post('payment_code'));
      $has_term = empty($payment) ? FALSE : ($payment->role == 4 ? FALSE : (is_true($payment->has_term)));

      $customer = $this->customers_model->get($this->input->post('customer_code'));

      $order = $this->orders_model->get($code);

      if( ! empty($order))
      {
        if( $order->state == 1)
        {
          $warehouse_code = $this->input->post('warehouse_code');

          $ds = array(
            'reference' => $this->input->post('reference'),
            'customer_code' => empty($customer) ? $this->input->post('customer_code') : $customer->code,
            'customer_name' => empty($customer) ? NULL : $customer->name,
            'customer_ref' => $this->input->post('customer_ref'),
            'channels_code' => $this->input->post('channels_code'),
            'payment_code' => $this->input->post('payment_code'),
            'is_term' => $has_term,
            'date_add' => db_date($this->input->post('date_add')),
            'warehouse_code' => $warehouse_code,
            'remark' => $this->input->post('remark'),
            'status' => 0,
            'id_sender' => NULL
          );

          $rs = $this->orders_model->update($code, $ds);

          if($rs === TRUE)
          {
            if($recal == 1)
            {
              //---- Recal discount
              $details = $this->orders_model->get_order_details($code);

              if( ! empty($details))
              {
                foreach($details as $detail)
                {
                  $qty	= $detail->qty;

                  //---- คำนวณ ส่วนลดจากนโยบายส่วนลด
                  $discount 	= $this->discount_model->get_item_recal_discount($detail->order_code, $detail->product_code, $detail->price, $order->customer_code, $qty, $order->payment_code, $order->channels_code, $order->date_add);

                  $arr = array(
                    "qty"		=> $qty,
                    "discount1"	=> $discount['discLabel1'],
                    "discount2" => $discount['discLabel2'],
                    "discount3" => $discount['discLabel3'],
                    "discount_amount" => $discount['amount'],
                    "total_amount"	=> ($detail->price * $qty) - $discount['amount'],
                    "id_rule"	=> $discount['id_rule']
                  );

                  $this->orders_model->update_detail($detail->id, $arr);
                }
              }

              $doc_total = $this->orders_model->get_order_total_amount($code);
              $arr = array(
              'doc_total' => $doc_total,
              'status' => 0
              );

              $this->orders_model->update($code, $arr);
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = 'ปรับปรุงรายการไม่สำเร็จ';
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะออเดอร์ไม่ถูกต้อง กรุณาตรวจสอบ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order code";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function edit_detail($code)
  {
    $this->load->helper('product_tab');

    $ds = array();
    $rs = $this->orders_model->get($code);

    if($rs->state == 1)
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $ds['order'] = $rs;

      $details = $this->orders_model->get_order_details($code);
      $ds['details'] = $details;
      $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('orders/order_edit_detail', $ds);
    }
    else
    {
      $err = "สถานะเอกสารไม่ถูกต้อง";
      $this->page_error($err);
    }

  }


  public function save($code)
  {
    $sc = TRUE;

		$id_sender = $this->input->post('id_sender');
		$tracking = trim($this->input->post('tracking'));
    $cod_amount = $this->input->post('cod_amount');

		$arr = array();

		if(! empty($id_sender))
		{
			$arr['id_sender'] = $id_sender;
		}

		if(! empty($tracking))
		{
			$arr['shipping_code'] = $tracking;
		}

    $arr['cod_amount'] = $cod_amount < 0 ? 0 : get_zero($cod_amount);

    $order = $this->orders_model->get($code);

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
        $arr['id_sender'] = $id_sender;
			}
		}


    if(is_true(getConfig('IX_BACK_ORDER')) && $order->state <= 4 && $order->is_pre_order == 0)
    {
      $is_backorder = 0;
      $total_amount = 0;

      $this->orders_model->drop_backlogs_list($order->code);

      $details = $this->orders_model->get_order_details($order->code);

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          if($rs->is_count)
          {
            $sell_stock = $this->stock_model->get_sell_stock($rs->product_code, $order->warehouse_code);
            $ordered = $this->orders_model->get_reserv_stock_exclude($rs->product_code, $order->warehouse_code, $rs->id);
            $reserv_stock = $this->reserv_stock_model->get_reserv_stock($rs->product_code, $order->warehouse_code);

            $available = $sell_stock - $ordered - $reserv_stock;

            if($available < $rs->qty)
            {
              $is_backorder = 1;

              $backlogs = array(
                'order_code' => $rs->order_code,
                'product_code' => $rs->product_code,
                'order_qty' => $rs->qty,
                'available_qty' => $available
              );

              $this->orders_model->add_backlogs_detail($backlogs);
            }
          }

          $total_amount += $rs->total_amount;
        }
      }

      $arr['doc_total'] = $total_amount;
      $arr['is_backorder'] = $is_backorder;
    }

    if($sc === TRUE)
    {
      $arr['status'] = 1;

      if( ! $this->orders_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "บันทึกออเดอร์ไม่สำเร็จ";
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function check_available_stock()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $rs = array();

    if( ! empty($ds))
    {
      $order = $this->orders_model->get($ds->code);

      if( ! empty($order))
      {
        if( ! empty($ds->rows))
        {
          foreach($ds->rows as $row)
          {
            $item = $this->products_model->get($row->product_code);

            $item = empty($item) ? $this->products_model->get_by_old_code($row->product_code) : $item;

            if( ! empty($item))
            {
              if($item->active == 1)
              {
                //---- สต็อกคงเหลือในคลัง
                $sell_stock = $this->stock_model->get_sell_stock($item->code, $order->warehouse_code);

                //---- ยอดจองสินค้า ไม่รวมรายการที่กำหนด
                $ordered = $this->orders_model->get_reserv_stock_exclude($item->code, $order->warehouse_code, $row->id);
                $reserv_stock = $this->reserv_stock_model->get_reserv_stock($item->code, $order->warehouse_code);

                $availableStock = $sell_stock - $ordered - $reserv_stock;

                $rs[] = array(
                  'id' => $row->id,
                  'available' => $availableStock < 0 ? 0 : $availableStock,
                  'status' => $availableStock >= $row->qty ? 'OK' : 'failed'
                );
              }
              else
              {
                $rs[] = array(
                  'id' => $row->id,
                  'available' => 0,
                  'status' => 'inactive'
                );
              }
            }
            else
            {
              $rs[] = array(
                'id' => $row->id,
                'available' => 0,
                'status' => 'invalid item'
              );
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order number";
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
      'data' => $rs
    );

    echo json_encode($arr);
  }


  public function get_order_grid()
  {
    $sc = TRUE;
    $ds = array();
    //----- Attribute Grid By Clicking image
    $model = $this->product_model_model->get(trim($this->input->get('model_code')));

    if( ! empty($model))
    {
      if($model->active)
      {
        $warehouse = get_null($this->input->get('warehouse_code'));
        $zone = get_null($this->input->get('zone_code'));
        $view = $this->input->get('isView') == '0' ? FALSE : TRUE;
        $table = $this->getOrderGrid($model->code, $view, $warehouse, $zone);
        $tableWidth	= $this->products_model->countAttribute($model->code) == 1 ? 600 : $this->getOrderTableWidth($model->code);

        if($table == 'notfound') {
          $sc = FALSE;
          $this->error = "not found";
        }
        else
        {
          $tbs = '<table class="table table-bordered border-1" style="min-width:'.$tableWidth.'px;">';
          $tbe = '</table>';
          $ds = array(
            'status' => 'success',
            'message' => NULL,
            'table' => $tbs.$table.$tbe,
            'tableWidth' => $tableWidth + 20,
            'modelCode' => $model->code,
            'modelName' => $model->name
          );
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "สินค้า Inactive";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "not found";
    }


    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function get_item_grid()
  {
    $sc = "";
    $item_code = $this->input->get('itemCode');
    $warehouse_code = get_null($this->input->get('warehouse_code'));
    $filter = getConfig('MAX_SHOW_STOCK');
    $auz = getConfig('ALLOW_UNDER_ZERO') ? TRUE : FALSE;
    $item = $this->products_model->get($item_code);

    if( ! empty($item))
    {
      if(! is_array($item))
      {
        $qty = ($item->count_stock == 1 &&  ! $auz) ? ($item->active == 1 ? $this->showStock($this->get_sell_stock($item->code, $warehouse_code)) : 0) : ($item->active == 1 ? 1000000 : 0);
        $sc = "success | {$item_code} | {$qty}";
      }
      else
      {
        $this->error = "รหัสซ้ำ ";
        foreach($item as $rs)
        {
          $this->error .= " :{$rs->code}";
        }

        echo "Error : {$this->error} | {$item_code}";
      }

    }
    else
    {
      $sc = "Error | ไม่พบสินค้า | {$item_code}";
    }

    echo $sc;
  }


  public function getOrderGrid($model_code, $view = FALSE, $warehouse = NULL, $zone = NULL)
	{
		$sc = '';
    $model = $this->product_model_model->get($model_code);
    if( ! empty($model))
    {
      if($model->active)
      {
        $isVisual = FALSE;
    		$attrs = $this->getAttribute($model->code);

    		if( count($attrs) == 1  )
    		{
    			$sc .= $this->orderGridOneAttribute($model, $attrs[0], $isVisual, $view, $warehouse, $zone);
    		}
    		else if( count( $attrs ) == 2 )
    		{
    			$sc .= $this->orderGridTwoAttribute($model, $isVisual, $view, $warehouse, $zone);
    		}
      }
      else
      {
        $sc = 'Disactive';
      }

    }
    else
    {
      $sc = 'notfound';
    }

		return $sc;
	}


  public function showStock($qty)
	{
		return $this->filter == 0 ? $qty : ($this->filter < $qty ? $this->filter : $qty);
	}


  public function orderGridOneAttribute($model, $attr, $isVisual, $view, $warehouse = NULL, $zone = NULL)
	{
    $auz = getConfig('ALLOW_UNDER_ZERO');
    if($auz == 1)
    {
      $isVisual = TRUE;
    }
		$sc 		= '';
		$data 	= $attr == 'color' ? $this->getAllColors($model->code) : $this->getAllSizes($model->code);
		$items	= $this->products_model->get_model_items($model->code);
		//$sc 	 .= "<table class='table table-bordered'>";
		$i 		  = 0;

    foreach($items as $item )
    {
      $id_attr	= $item->size_code === NULL OR $item->size_code === '' ? $item->color_code : $item->size_code;
      $sc 	.= $i%2 == 0 ? '<tr>' : '';
      $active	= $item->active == 0 ? 'Disactive' : ( $item->can_sell == 0 ? 'Not for sell' : ( $item->is_deleted == 1 ? 'Deleted' : TRUE ) );
      $stock	= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->stock_model->get_stock($item->code) )  : 0 ) : 0; //---- สต็อกทั้งหมดทุกคลัง
			$qty 		= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code, $warehouse, $zone) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้
			$disabled  = $isVisual === TRUE  && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');

      if( $qty < 1 && $active === TRUE )
			{
				$txt = '<p class="pull-right red">Sold out</p>';
			}
			else if( $qty > 0 && $active === TRUE )
			{
				$txt = '<p class="pull-right green">'. $qty .'  in stock</p>';
			}
			else
			{
				$txt = $active === TRUE ? '' : '<p class="pull-right blue">'.$active.'</p>';
			}

      $limit		= $qty === FALSE ? 1000000 : $qty;
      $code = $attr == 'color' ? $item->color_code : $item->size_code;

			$sc 	.= '<td class="middle" style="border-right:0px;">';
			$sc 	.= '<strong>' .	$code.' ('.$data[$code].')' . '</strong>';
			$sc 	.= '</td>';

			$sc 	.= '<td class="middle" class="one-attribute">';
			$sc 	.= $isVisual === FALSE ? '<center><span class="font-size-10 blue">('.($stock < 0 ? 0 : $stock).')</span></center>':'';

      if( $view === FALSE )
      {
        $sc 	.= '<input type="number" class="form-control input-sm order-grid display-block"
        data-sku="'.$item->code.'" data-limit="'.$limit.'" data-countstock="'.$item->count_stock.'"
        name="qty[0]['.$item->code.']" id="qty_'.$item->code.'" '.$disabled.' />';
      }

      $sc 	.= 	'<center>';
      $sc   .= '<span class="font-size-10">';
      $sc   .= $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : $qty);
      $sc   .= '</span></center>';
			$sc 	.= '</td>';

			$i++;

			$sc 	.= $i%2 == 0 ? '</tr>' : '';

    }


		//$sc	.= "</table>";

		return $sc;
	}


  public function orderGridTwoAttribute($model, $isVisual, $view, $warehouse = NULL, $zone = NULL)
  {
    $auz = getConfig('ALLOW_UNDER_ZERO');
    if($auz == 1)
    {
      $isVisual = $view === TRUE ? $isVisual : TRUE;
    }

    $colors	= $this->getAllColors($model->code);
    $sizes 	= $this->getAllSizes($model->code);
    $sc 		= '';
    //$sc 		.= '<table class="table table-bordered">';
    $sc 		.= $this->gridHeader($colors);

    foreach( $sizes as $size_code => $size )
    {
      $bg_color = '';
      $sc 	.= '<tr style="font-size:12px; '.$bg_color.'">';
      $sc 	.= '<td class="text-center middle fix-size" scope="row"><strong>'.$size_code.'</strong></td>';

      foreach( $colors as $color_code => $color )
      {
        $item = $this->products_model->get_item_by_color_and_size($model->code, $color_code, $size_code);

        if( !empty($item) )
        {
          $active	= $item->active == 0 ? 'Disactive' : TRUE;

          $stock	= $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->stock_model->get_stock($item->code) )  : 0 ) : 0; //---- สต็อกทั้งหมดทุกคลัง
          $qty = $isVisual === FALSE ? ( $active == TRUE ? $this->showStock( $this->get_sell_stock($item->code, $warehouse, $zone) ) : 0 ) : FALSE; //--- สต็อกที่สั่งซื้อได้
          $disabled  = $isVisual === TRUE  && $active == TRUE ? '' : ( ($active !== TRUE OR $qty < 1 ) ? 'disabled' : '');

          if( $qty < 1 && $active === TRUE )
          {
            $txt = '<span class="font-size-12 red">Sold out</span>';
          }
          else
          {
            $txt = $active === TRUE ? '' : '<span class="font-size-12 blue">'.$active.'</span>';
          }

          $available = $qty === FALSE && $active === TRUE ? '' : ( ($qty < 1 || $active !== TRUE ) ? $txt : number($qty));
          $limit		= $qty === FALSE ? 1000000 : $qty;


          $sc 	.= '<td class="order-grid">';
          $sc .= $view === TRUE ? '<center><span <span class="font-size-10" style="color:#ccc;">'.$color_code.'-'.$size_code.'</span></center>' : '';
          $sc 	.= $isVisual === FALSE ? '<center><span class="font-size-10 blue">('.number($stock).')</span></center>' : '';

          if( $view === FALSE )
          {
            $sc .= '<input type="number" min="1" max="'.$limit.'" ';
            $sc .= 'class="form-control text-center order-grid" ';
            $sc .= 'name="qty['.$item->color_code.']['.$item->code.']" ';
            $sc .= 'id="qty_'.$item->code.'" ';
            $sc .= 'data-sku="'.$item->code.'" data-limit="'.$limit.'" data-countstock="'.$item->count_stock.'" ';
            $sc .= 'placeholder="'.$color_code.'-'.$size_code.'" '.$limit.'" '.$disabled.' />';
          }

          $sc 	.= $isVisual === FALSE ? '<center>'.$available.'</center>' : '';
          $sc 	.= '</td>';
        }
        else
        {
          $sc .= '<td class="order-grid middle">N/A</td>';
        }
      } //--- End foreach $colors

      $sc .= '</tr>';
    } //--- end foreach $sizes

    return $sc;
  }


  public function getAttribute($model_code)
  {
    $sc = array();
    $color = $this->products_model->count_color($model_code);
    $size  = $this->products_model->count_size($model_code);
    if( $color > 0 )
    {
      $sc[] = "color";
    }

    if( $size > 0 )
    {
      $sc[] = "size";
    }
    return $sc;
  }


  public function gridHeader(array $colors)
  {
    $sc = '<thead>';
    $sc .= '<tr class="font-size-12">';
    $sc .= '<th class="fix-width-80 fix-size fix-header" style="z-index:100">&nbsp;</th>';

    foreach( $colors as $code => $name )
    {
      $sc .= '<th class="text-center middle fix-header" style="width:80px; white-space:normal;">'.$code . '<br/>'. $name.'</th>';
    }

    $sc .= '</tr>';
    $sc .= '</thead>';

    return $sc;
  }


  public function getAllColors($model_code)
	{
		$sc = array();
    $colors = $this->products_model->get_all_colors($model_code);
    if($colors !== FALSE)
    {
      foreach($colors as $color)
      {
        $sc[$color->code] = $color->name;
      }
    }

    return $sc;
	}


  public function getAllSizes($model_code)
	{
		$sc = array();
		$sizes = $this->products_model->get_all_sizes($model_code);
		if( $sizes !== FALSE )
		{
      foreach($sizes as $size)
      {
        $sc[$size->code] = $size->name;
      }
		}
		return $sc;
	}


  public function getSizeColor($size_code)
  {
    $colors = array(
      'XS' => '#DFAAA9',
      'S' => '#DFC5A9',
      'M' => '#DEDFA9',
      'L' => '#C3DFA9',
      'XL' => '#A9DFAA',
      '2L' => '#A9DFC5',
      '3L' => '#A9DDDF',
      '5L' => '#A9C2DF',
      '7L' => '#ABA9DF'
    );

    if(isset($colors[$size_code]))
    {
      return $colors[$size_code];
    }

    return FALSE;
  }


  public function getOrderTableWidth($model_code)
  {
    $sc = 600; //--- ชั้นต่ำ
    $tdWidth = 80;  //----- แต่ละช่อง
    $padding = 80; //----- สำหรับช่องแสดงไซส์
    $color = $this->products_model->count_color($model_code);
    if($color > 0)
    {
      $sc = $color * $tdWidth + $padding;
    }

    return $sc;
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);
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


  public function print_order_sheet($code, $barcode = '')
  {
    $this->load->model('masters/products_model');

    $this->load->library('printer');
    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    $details = $this->orders_model->get_order_details($code);
    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['is_barcode'] = $barcode != '' ? TRUE : FALSE;
    $this->load->view('print/print_order_sheet', $ds);
  }


  public function get_sell_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    //---- Orignal
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse, $zone);
    $ordered = $this->orders_model->get_reserv_stock($item_code, $warehouse, $zone);
    $reserv_stock = $this->reserv_stock_model->get_reserv_stock($item_code, $warehouse);
    $availableStock = $sell_stock - $ordered - $reserv_stock;
		return $availableStock < 0 ? 0 : $availableStock;
  }


  public function get_detail_table($order_code)
  {
    $sc = "no data found";
    $order = $this->orders_model->get($order_code);
    $details = $this->orders_model->get_order_details($order_code);
    if($details != FALSE )
    {
      $no = 1;
      $total_qty = 0;
      $total_discount = 0;
      $total_amount = 0;
      $total_order = 0;
      $ds = array();
      foreach($details as $rs)
      {
        $arr = array(
          "id"		=> $rs->id,
          "no"	=> $no,
          "imageLink"	=> get_product_image($rs->product_code, 'mini'),
          "productCode"	=> $rs->product_code,
          "productName"	=> $rs->product_name,
          "cost" => number($rs->cost, 2),
          "price"	=> number($rs->price, 2),
          "priceLabel" => number($rs->price, 2),
          "qty"	=> floatval($rs->qty),
          "discount"	=> discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
          "amount"	=> number_format($rs->total_amount, 2),
          "is_count" => intval($rs->is_count)
        );

        array_push($ds, $arr);
        $total_qty += $rs->qty;
        $total_discount += $rs->discount_amount;
        $total_amount += $rs->total_amount;
        $total_order += $rs->qty * $rs->price;
        $no++;
      }

      $netAmount = ( $total_amount - $order->bDiscAmount ) + $order->shipping_fee + $order->service_fee;

      $arr = array(
            "total_qty" => number($total_qty),
            "order_amount" => number($total_order, 2),
            "total_discount" => number($total_discount, 2),
            "shipping_fee"	=> number($order->shipping_fee,2),
            "service_fee"	=> number($order->service_fee, 2),
            "total_amount" => number($total_amount, 2),
            "net_amount"	=> number($netAmount,2)
          );
      array_push($ds, $arr);
      $sc = json_encode($ds);
    }
    echo $sc;

  }


  public function get_pay_amount()
  {
    $sc = TRUE;
    $ds = array();

    if($this->input->get('order_code'))
    {
      $order = $this->orders_model->get($this->input->get('order_code'));

      if( ! empty($order))
      {
        //--- ยอดรวมหลังหักส่วนลด ตาม item
        $amount = $this->orders_model->get_order_total_amount($order->code);

        //--- ส่วนลดท้ายบิล
        $bDisc = $order->bDiscAmount; //$this->orders_model->get_bill_discount($code);

        $pay_amount = $amount - $bDisc;

        $ds = array(
          'pay_amount' => $pay_amount,
          'id_sender' => empty($order->id_sender) ? FALSE : $order->id_sender
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Order code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter : order code";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function get_account_detail($id)
  {
    $sc = 'fail';
    $this->load->model('masters/bank_model');
    $this->load->helper('bank');
    $rs = $this->bank_model->get_account_detail($id);
    if($rs !== FALSE)
    {
      $ds = bankLogoUrl($rs->bank_code).' | '.$rs->bank_name.' สาขา '.$rs->branch.'<br/>เลขที่บัญชี '.$rs->acc_no.'<br/> ชื่อบัญชี '.$rs->acc_name;
      $sc = $ds;
    }

    echo $sc;
  }


  public function confirm_payment()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $this->load->helper('bank');
      $this->load->model('orders/order_payment_model');

      $file = isset( $_FILES['image'] ) ? $_FILES['image'] : FALSE;
      $order_code = $this->input->post('order_code');
      $date = $this->input->post('payDate');
      $h = $this->input->post('payHour');
      $m = $this->input->post('payMin');
      $dhm = $date.' '.$h.':'.$m.':00';
      $pay_date = db_date($dhm, TRUE);

      $order = $this->orders_model->get($order_code);

      $arr = array(
        'order_code' => $order_code,
        'order_amount' => $this->input->post('orderAmount'),
        'pay_amount' => $this->input->post('payAmount'),
        'pay_date' => $pay_date,
        'id_account' => $this->input->post('id_account'),
        'acc_no' => $this->input->post('acc_no'),
        'user' => get_cookie('uname'),
        'is_pre_order' => $order->is_pre_order
      );

      //--- บันทึกรายการ
      if($this->order_payment_model->add($arr))
      {
        if($order->state == 1)
        {
          $rs = $this->orders_model->change_state($order_code, 2);  //--- แจ้งชำระเงิน

          if($rs)
          {
            $arr = array(
              'order_code' => $order_code,
              'state' => 2,
              'update_user' => get_cookie('uname')
            );

            $this->order_state_model->add_state($arr);
          }

          if($rs === FALSE)
          {
            $sc = FALSE;
            $message = 'เปลี่ยนสถานะออเดอร์ไม่สำเร็จ';
          }
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'บันทึกรายการไม่สำเร็จ';
      }

      if($file !== FALSE)
      {
        $rs = $this->do_upload($file, $order_code);
        if($rs !== TRUE)
        {
          $sc = FALSE;
          $message = $rs;
        }
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function do_upload($file, $code)
	{
    $this->load->library('upload');
    $sc = TRUE;
		$image_path = $this->config->item('image_path').'payments/';
    $image 	= new Upload($file);
    if( $image->uploaded )
    {
      $image->file_new_name_body = $code; 		//--- เปลี่ยนชือ่ไฟล์ตาม order_code
      $image->image_resize			 = TRUE;		//--- อนุญาติให้ปรับขนาด
      $image->image_retio_fill	 = TRUE;		//--- เติกสีให้เต็มขนาดหากรูปภาพไม่ได้สัดส่วน
      $image->file_overwrite		 = TRUE;		//--- เขียนทับไฟล์เดิมได้เลย
      $image->auto_create_dir		 = TRUE;		//--- สร้างโฟลเดอร์อัตโนมัติ กรณีที่ไม่มีโฟลเดอร์
      $image->image_x					   = 500;		//--- ปรับขนาดแนวนอน
      //$image->image_y					   = 800;		//--- ปรับขนาดแนวตั้ง
      $image->image_ratio_y      = TRUE;  //--- ให้คงสัดส่วนเดิมไว้
      $image->image_background_color	= "#FFFFFF";		//---  เติมสีให้ตามี่กำหนดหากรูปภาพไม่ได้สัดส่วน
      $image->image_convert			= 'jpg';		//--- แปลงไฟล์

      $image->process($image_path);						//--- ดำเนินการตามที่ได้ตั้งค่าไว้ข้างบน

      if( ! $image->processed )	//--- ถ้าไม่สำเร็จ
      {
        $sc 	= $image->error;
      }
    } //--- end if

    $image->clean();	//--- เคลียร์รูปภาพออกจากหน่วยความจำ

		return $sc;
	}


  public function view_payment_detail()
  {
    $this->load->model('orders/order_payment_model');
    $this->load->model('masters/bank_model');
    $sc = TRUE;
    $code = $this->input->post('order_code');
    $rs = $this->order_payment_model->get($code);

    if( ! empty($rs))
    {
      $bank = $this->bank_model->get_account_detail($rs->id_account);
      $img  = payment_image_url($code); //--- order_helper
      $ds   = array(
        'order_code' => $code,
        'orderAmount' => number($rs->order_amount, 2),
        'payAmount' => number($rs->pay_amount, 2),
        'payDate' => thai_date($rs->pay_date, TRUE, '/'),
        'bankName' => $bank->bank_name,
        'branch' => $bank->branch,
        'accNo' => $bank->acc_no,
        'accName' => $bank->acc_name,
        'date_add' => thai_date($rs->date_upd, TRUE, '/'),
        'imageUrl' => $img === FALSE ? '' : $img,
        'valid' => "no"
      );
    }
    else
    {
      $sc = FALSE;
    }

    echo $sc === TRUE ? json_encode($ds) : 'fail';
  }


  public function update_shipping_code()
  {
    $order_code = $this->input->post('order_code');
    $ship_code  = $this->input->post('shipping_code');
    if($order_code && $ship_code)
    {
      $rs = $this->orders_model->update_shipping_code($order_code, $ship_code);
      echo $rs === TRUE ? 'success' : 'fail';
    }
  }


  public function save_address()
  {
    $sc = TRUE;
    $this->load->model('address/address_model');

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->order_code) && ! empty($ds->name) && ! empty($ds->address))
    {
      $adr = $this->address_model->get_ship_to_address($ds->order_code);

      if(empty($adr))
      {
        $arr = array(
          'order_code' => $ds->order_code,
          'name' => $ds->name,
          'address' => $ds->address,
          'sub_district' => get_null($ds->sub_district),
          'district' => get_null($ds->district),
          'province' => get_null($ds->province),
          'postcode' => get_null($ds->postcode),
          'phone' => get_null($ds->phone)
        );

        if( ! $this->address_model->add_shipping_address($arr))
        {
          $sc = FALSE;
          set_error('insert');
        }
      }
      else
      {
        $arr = array(
          'name' => $ds->name,
          'address' => $ds->address,
          'sub_district' => get_null($ds->sub_district),
          'district' => get_null($ds->district),
          'province' => get_null($ds->province),
          'postcode' => get_null($ds->postcode),
          'phone' => get_null($ds->phone)
        );

        if( ! $this->address_model->update_shipping_address($adr->id, $arr))
        {
          $sc = FALSE;
          set_error('update');
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


  public function get_address_table()
  {
    $sc = TRUE;

		$customer_code = trim($this->input->post('customer_code'));
		$customer_ref = trim($this->input->post('customer_ref'));

    if( ! empty($customer_code) OR !empty($customer_ref))
    {
			$ds = array();
			$this->load->model('address/address_model');
			$adrs = empty($customer_ref) ? $this->address_model->get_ship_to_address($customer_code) : $this->address_model->get_shipping_address($customer_ref);
			if( ! empty($adrs))
			{
				foreach($adrs as $rs)
				{
					$arr = array(
						'id' => $rs->id,
						'name' => $rs->name,
						'address' => $rs->address.' '.$rs->sub_district.' '.$rs->district.' '.$rs->province.' '.$rs->postcode.' '.$rs->country,
						'phone' => $rs->phone,
						'email' => $rs->email,
						'alias' => $rs->alias,
						'default' => $rs->is_default == 1 ? 1 : ''
					);
					array_push($ds, $arr);
				}
			}
			else
			{
				$sc = FALSE;
			}
    }

    echo $sc === TRUE ? json_encode($ds) : 'noaddress';
  }


	public function set_sender()
	{
		$sc = TRUE;
		$order_code = trim($this->input->post('order_code'));
		$id_sender = trim($this->input->post('id_sender'));

		$arr = array(
			'id_sender' => $id_sender
		);

		if(! $this->orders_model->update($order_code, $arr))
		{
			$sc = FALSE;
			$this->error = "Update failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function set_never_expire()
  {
    $code = $this->input->post('order_code');
    $option = $this->input->post('option');
    $rs = $this->orders_model->set_never_expire($code, $option);
    echo $rs === TRUE ? 'success' : 'ทำรายการไม่สำเร็จ';
  }


  public function un_expired()
  {
		$sc = TRUE;
    $code = $this->input->get('order_code');
		$order = $this->orders_model->get($code);

		if( ! empty($order))
		{
			if($order->role == 'U' OR $order->role == 'P')
			{
				if($order->role == 'U')
				{
					$this->load->model('orders/support_model');
					$total_amount = $this->orders_model->get_order_total_amount($code);
					$current = $this->support_model->get_budget($order->customer_code);
					$used = $this->support_model->get_budget_used($order->customer_code);

					$balance = $current - $used;

					if($total_amount > $balance)
					{
						$sc = FALSE;
						$this->error = "งบประมาณไม่เพียงพอ";
					}
				}

				if($order->role == 'P')
				{
					$this->load->model('orders/sponsor_model');
					$total_amount = $this->orders_model->get_order_total_amount($code);
					$current = $this->sponsor_model->get_budget($order->customer_code);
					$used = $this->sponsor_model->get_budget_used($order->customer_code);

					$balance = $current - $used;

					if($total_amount > $balance)
					{
						$sc = FALSE;
						$this->error = "งบประมาณไม่เพียงพอ";
					}
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid order number";
		}

		if($sc === TRUE)
		{
			if( ! $this->orders_model->un_expired($code))
			{
				$sc = FALSE;
				$this->error = "ทำรายการไม่สำเร็จ";
			}
		}

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function do_approve($code)
  {
    $sc = TRUE;
    $this->load->model('approve_logs_model');

    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      if($order->state == 1)
      {
        $user = $this->_user->uname;
        $rs = $this->orders_model->update_approver($code, $user);
        if(! $rs)
        {
          $sc = FALSE;
          $this->error = "อนุมัติไม่สำเร็จ";
        }
        else
        {
          $this->approve_logs_model->add($code, 1, $user);
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
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }


    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function un_approve($code)
  {
    $sc = TRUE;
    $this->load->model('approve_logs_model');
    $order = $this->orders_model->get($code);
    if( ! empty($order))
    {
      if($order->state == 1 )
      {
        $user = $this->_user->uname;
        $rs = $this->orders_model->un_approver($code, $user);
        if(! $rs)
        {
          $sc = FALSE;
          $this->error = "อนุมัติไม่สำเร็จ";
        }
        else
        {
          $this->approve_logs_model->add($code, 0, $user);
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
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }


    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function order_state_change()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $code = $this->input->post('order_code');
      $state = $this->input->post('state');
      $order = $this->orders_model->get($code);
      $reason_id = $this->input->post('reason_id');
			$reason = $this->input->post('cancle_reason');
      $force_cancel = $this->input->post('force_cancel') == 1 ? 1 : 0;
      $uat = is_true(getConfig('IS_UAT'));

      if(! empty($order))
      {
        //--- ถ้าเป็นเบิกแปรสภาพ จะมีการผูกสินค้าไว้
        if($order->role == 'T')
        {
          $this->load->model('inventory/transform_model');
          //--- หากมีการรับสินค้าที่ผูกไว้แล้วจะไม่อนุญาติให้เปลี่ยนสถานะใดๆ
          $is_received = $this->transform_model->is_received($code);

          if($is_received === TRUE)
          {
            echo 'ใบเบิกมีการรับสินค้าแล้วไม่อนุญาติให้ย้อนสถานะ';
						exit;
          }
        }

        //--- ถ้าเป็นยืมสินค้า
        if($order->role == 'L')
        {
          $this->load->model('inventory/lend_model');
          //--- หากมีการรับสินค้าที่ผูกไว้แล้วจะไม่อนุญาติให้เปลี่ยนสถานะใดๆ
          $is_received = $this->lend_model->is_received($code);
          if($is_received === TRUE)
          {
            echo 'ใบเบิกมีการรับคืนสินค้าแล้วไม่อนุญาติให้ย้อนสถานะ';
						exit;
          }
        }

        if($order->role == 'P')
        {
          $this->load->model('masters/sponsor_budget_model');
          $this->load->model('inventory/invoice_model');
          $sold_amount = $this->invoice_model->get_billed_amount($order->code);
        }


        if($sc === TRUE)
        {
          $this->db->trans_begin();
          //--- ถ้าเปิดบิลแล้ว
          if($sc === TRUE && $order->state == 8)
          {
            if($state < 8)
            {
              if( ! $this->roll_back_action($code, $order->role) )
              {
                $sc = FALSE;
              }
              else
              {
                if($order->role == 'P')
                {
                  $this->sponsor_budget_model->rollback_used($order->budget_id, $sold_amount);
                }
              }
            }
            else if($state == 9)
            {
              if(! $this->cancle_order($code, $order->role, $order->state, $reason, $reason_id, $force_cancel) )
              {
                $sc = FALSE;
              }
            }
          }
          else if($sc === TRUE && $order->state != 8)
          {
            if($state == 9)
            {
              if(! $this->cancle_order($code, $order->role, $order->state, $reason, $reason_id, $force_cancel) )
              {
                $sc = FALSE;
              }
            }
          }

          if($sc === TRUE)
          {
            $rs = $this->orders_model->change_state($code, $state);

            if($rs)
            {
              $arr = array(
                'order_code' => $code,
                'state' => $state,
                'update_user' => $this->_user->uname
              );

              if(! $this->order_state_model->add_state($arr) )
              {
                $sc = FALSE;
                $this->error = "Add state failed";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เปลี่ยนสถานะไม่สำเร็จ";
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();

            if(is_true(getConfig('WRX_OB_INTERFACE')))
            {
              $this->load->library('wrx_ob_api');
              $this->wrx_ob_api->update_status($code);
            }
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
        $this->error = 'ไม่พบข้อมูลออเดอร์';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  } //--- order_state_change


  public function roll_back_action($code, $role)
  {
    $this->load->model('inventory/movement_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('inventory/transfer_model');
    $this->load->model('inventory/lend_model');
    $this->load->model('inventory/delivery_order_model');

    $sc = TRUE;
    $order = $this->orders_model->get($code);

    //---- set is_complete = 0
    if( ! $this->orders_model->un_complete($code) )
    {
      $sc = FALSE;
      $this->error = "Uncomplete details failed";
    }

    if($sc === TRUE)
    {
      $arr = array(
        'is_valid' => 0,
        'is_exported' => 0
      );

      if(! $this->orders_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "Clear Inv code failed";
      }
    }

    //---- move cancle product back to  buffer
    if($sc === TRUE)
    {
      if(! $this->cancle_model->restore_buffer($code) )
      {
        $sc = FALSE;
        $this->error = "Restore cancle failed";
      }
    }

    //--- remove movement
    if($sc === TRUE)
    {
      if(! $this->movement_model->drop_movement($code) )
      {
        $sc = FALSE;
        $this->error = "Drop movement failed";
      }
    }


    if($sc === TRUE)
    {
      //--- restore sold product back to buffer
      $sold = $this->invoice_model->get_details($code);

      if( ! empty($sold))
      {
        if($role == 'T' OR $role == 'Q' OR $role == 'C' OR $role == 'N' OR $role == 'L')
        {
          foreach($sold as $rs)
          {
            if($rs->is_count == 1)
            {
              $stock = $this->stock_model->get_stock_zone($order->zone_code, $rs->product_code);

              if($stock < $rs->qty)
              {
                $sc = FALSE;
                $this->error = "สต็อกคงเหลือในโซนไม่พอให้ย้อนสถานะ <br/>Zone : {$order->zone_code} <br/>SKU : {$rs->product_code} <br/>Qty : ".round($rs->qty, 2)."/{$stock}";
              }
            }
          }
        }

        if($sc === TRUE)
        {
          foreach($sold as $rs)
          {
            if($sc === FALSE) { break; }

            if($rs->is_count == 1)
            {
              //---- restore_buffer
              if($this->buffer_model->is_exists($rs->reference, $rs->product_code, $rs->zone_code, $rs->order_detail_id) === TRUE)
              {
                if(! $this->buffer_model->update($rs->reference, $rs->product_code, $rs->zone_code, $rs->qty, $rs->order_detail_id))
                {
                  $sc = FALSE;
                  $this->error = "Restore buffer (update) failed";
                }
              }
              else
              {
                $ds = array(
                  'order_code' => $rs->reference,
                  'product_code' => $rs->product_code,
                  'warehouse_code' => $rs->warehouse_code,
                  'zone_code' => $rs->zone_code,
                  'qty' => $rs->qty,
                  'user' => $rs->user,
                  'order_detail_id' => $rs->order_detail_id
                );

                if(! $this->buffer_model->add($ds) )
                {
                  $sc = FALSE;
                  $this->error = "Restore buffer (add) failed";
                }
              }

              if($sc === TRUE)
              {
                if($role == 'T' OR $role == 'Q' OR $role == 'C' OR $role == 'N' OR $role == 'L')
                {
                  if( ! $this->stock_model->update_stock_zone($order->zone_code, $rs->product_code, ($rs->qty * -1)))
                  {
                    $sc = FALSE;
                    $this->error = "Failed remove stock from Zone : {$order->zone_code} <br/>SKU : {$rs->product_code} <br/> Qty : ".round($rs->qty, 2);
                  }
                }
              }
            }

            if($sc === TRUE)
            {
              if( !$this->invoice_model->drop_sold($rs->id) )
              {
                $sc = FALSE;
                $this->error = "Drop sold data failed";
              }

              //------ หากเป็นออเดอร์เบิกแปรสภาพ
              if($role == 'T')
              {
                if( ! $this->transform_model->reset_sold_qty($code) )
                {
                  $sc = FALSE;
                  $this->error = "Reset Transform sold qty failed";
                }
              }

              //-- หากเป็นออเดอร์ยืม
              if($role == 'L')
              {
                if(! $this->lend_model->drop_backlogs_list($code) )
                {
                  $sc = FALSE;
                  $this->error = "Drop lend backlogs failed";
                }
              }
            }
          } //--- end foreach
        }
      } //---- end sold
    }

    return $sc;
  }


  public function cancle_order($code, $role, $state, $cancle_reason = NULL, $reason_id = NULL, $force_cancel = 0)
  {
    $this->load->model('inventory/prepare_model');
    $this->load->model('inventory/qc_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('inventory/transfer_model');
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('inventory/invoice_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
		$this->load->model('inventory/movement_model');
    $this->load->model('masters/zone_model');

    $sc = TRUE;

		if( ! empty($cancle_reason))
		{
			//----- add reason to table order_cancle_reason
			$reason = array(
				'code' => $code,
        'reason_id' => $reason_id,
				'reason' => $cancle_reason,
				'user' => $this->_user->uname
			);

			$this->orders_model->add_cancle_reason($reason);
		}

    if($state > 3 && $sc === TRUE)
    {
      //--- put prepared product to cancle zone
      $prepared = $this->prepare_model->get_details($code);

      if( ! empty($prepared))
      {
        foreach($prepared AS $rs)
        {
          if($sc === FALSE)
          {
            break;
          }

          $zone = $this->zone_model->get($rs->zone_code);

          $arr = array(
            'order_code' => $rs->order_code,
            'product_code' => $rs->product_code,
            'warehouse_code' => empty($zone->warehouse_code) ? NULL : $zone->warehouse_code,
            'zone_code' => $rs->zone_code,
            'qty' => $rs->qty,
            'user' => $this->_user->uname,
            'order_detail_id' => $rs->order_detail_id
          );

          if( ! $this->cancle_model->add($arr) )
          {
            $sc = FALSE;
            $this->error = "Move Items to Cancle failed";
          }
        }
      }

      //--- drop sold data
      if($sc === TRUE)
      {
        if(! $this->invoice_model->drop_all_sold($code) )
        {
          $sc = FALSE;
          $this->error = "Drop sold data failed";
        }
      }

    }

    if($sc === TRUE)
    {
      //---- เมื่อมีการยกเลิกออเดอร์
      //--- 1. เคลียร์ buffer
      if(! $this->buffer_model->delete_all($code) )
      {
        $sc = FALSE;
        $this->error = "Delete buffer failed";
      }

      //--- 2. ลบประวัติการจัดสินค้า
      if($sc === TRUE)
      {
        if(! $this->prepare_model->clear_prepare($code) )
        {
          $sc = FALSE;
          $this->error = "Delete prepared data failed";
        }
      }


      //--- 3. ลบประวัติการตรวจสินค้า
      if($sc === TRUE)
      {
        if(! $this->qc_model->clear_qc($code) )
        {
          $sc = FALSE;
          $this->error = "Delete QC failed";
        }
      }

			//--- remove movement
	    if($sc === TRUE)
	    {
	      if(! $this->movement_model->drop_movement($code) )
	      {
	        $sc = FALSE;
	        $this->error = "Drop movement failed";
	      }
	    }


      //--- 4. set รายการสั่งซื้อ ให้เป็น ยกเลิก
      if($sc === TRUE)
      {
        if(! $this->orders_model->cancle_order_detail($code) )
        {
          $sc = FALSE;
          $this->error = "Cancle Order details failed";
        }
      }


      //--- 5. ยกเลิกออเดอร์
      if($sc === TRUE)
      {
        $arr = array(
          'status' => 2,
          'is_exported' => 0
        );

        if(! $this->orders_model->update($code, $arr) )
        {
          $sc = FALSE;
          $this->error = "Change order status failed";
        }
      }


      if($sc === TRUE)
      {
        //--- 6. ลบรายการที่ผู้ไว้ใน order_transform_detail (กรณีเบิกแปรสภาพ)
        if($role == 'T' OR $role == 'Q')
        {
          if( ! $this->transform_model->clear_transform_detail($code) )
          {
            $sc = FALSE;
            $this->error = "Clear Transform backlogs failed";
          }

          $this->transform_model->close_transform($code);
        }

        //-- หากเป็นออเดอร์ยืม
        if($role == 'L')
        {
          if(! $this->lend_model->drop_backlogs_list($code) )
          {
            $sc = FALSE;
            $this->error = "Drop Lend backlogs failed";
          }
        }
      }
    }

    return $sc;
  }


  //--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
  public function clear_buffer($code)
  {
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');

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

          //--- move buffer to cancle
          $this->cancle_model->add($arr);
        }

        //--- delete cancle
        $this->buffer_model->delete($rs->id);
      }
    }
  }


  public function update_remark()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $remark = get_null($this->input->post('remark'));

    $arr = array(
      'remark' => $remark
    );

    if( ! $this->orders_model->update($code, $arr))
    {
      $sc = FALSE;
      $this->error = "Failed to update remark";
    }

    $this->_response($sc);
  }


  public function update_discount()
  {
    $sc = TRUE;
    $this->load->model('orders/discount_logs_model');
    $code = $this->input->post('order_code');
    $discount = $this->input->post('discount');
    $approver = $this->input->post('approver');
    $user = $this->_user->uname;

    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      if( $order->state == 1)
      {
        if( ! empty($discount))
      	{
      		foreach( $discount as $id => $value )
      		{
      			//----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
      			if( $value != "")
      			{
      				//--- ได้ Obj มา
      				$detail = $this->orders_model->get_detail($id);

      				//--- ถ้ารายการนี้มีอยู่
      				if( $detail !== FALSE )
      				{
      					//------ คำนวณส่วนลดใหม่
      					$step = explode('+', $value);
      					$discAmount = 0;
      					$discLabel = array(0, 0, 0);
      					$price = $detail->price;
      					$i = 0;
      					foreach($step as $discText)
      					{
      						if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
      						{
                    $discText = str_replace(' ', '', $discText);
                    $discText = str_replace('๔', '%', $discText);
      							$disc = explode('%', $discText);
      							$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
      							$discount = count($disc) == 1 ? floatval($disc[0]) : $price * (floatval($disc[0]) * 0.01); //--- ส่วนลดต่อชิ้น
      							$discLabel[$i] = count($disc) == 1 ? $disc[0] : number($disc[0], 2).'%';
      							$discAmount += $discount;
      							$price -= $discount;
      						}
      						$i++;
      					}

      					$total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
      					$total_amount = ( $detail->qty * $detail->price ) - $total_discount; //--- ยอดรวมสุดท้าย

      					$arr = array(
                  "discount1" => $discLabel[0],
                  "discount2" => $discLabel[1],
                  "discount3" => $discLabel[2],
                  "discount_amount"	=> $total_discount,
                  "total_amount" => $total_amount ,
                  "id_rule"	=> NULL,
                  "update_user" => $user
                );

      					$cs = $this->orders_model->update_detail($id, $arr);

                if($cs)
                {
                  $log_data = array(
                    "order_code"		=> $code,
                    "product_code"	=> $detail->product_code,
                    "old_discount"	=> discountLabel($detail->discount1, $detail->discount2, $detail->discount3),
                    "new_discount"	=> discountLabel($discLabel[0], $discLabel[1], $discLabel[2]),
                    "user"	=> $user,
                    "approver"		=> $approver
                  );
        					$this->discount_logs_model->logs_discount($log_data);
                }

      				}	//--- end if detail
      			} //--- End if value
      		}	//--- end foreach

          $doc_total = $this->orders_model->get_order_total_amount($code);
          $arr = array(
            'doc_total' => $doc_total,
            'status' => 0
          );

          $this->orders_model->update($code, $arr);
      	}
      }
      else
      {
        $sc = FALSE;
        $this->error = "สถานะออเดอร์ไม่ถูกต้อง กรุณาตรวจสอบ";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เลขที่เอกสารไม่ถูกต้อง";
    }



    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function update_gp()
  {
    $code = $this->input->post('code');
    $gp = $this->input->post('gp');
    $details = $this->orders_model->get_order_details($code);
    $user = get_cookie('uname');
    $this->load->model('orders/discount_logs_model');

    if( ! empty($details))
    {
      foreach($details as $detail)
      {
        //------ คำนวณส่วนลดใหม่
        $step = explode('+', $gp);
        $discAmount = 0;
        $discLabel = array(0, 0, 0);
        $price = $detail->price;
        $i = 0;
        foreach($step as $discText)
        {
          if($i < 3) //--- limit ไว้แค่ 3 เสต็ป
          {
            $disc = explode('%', $discText);
            $disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
            $discount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
            $discLabel[$i] = count($disc) == 1 ? $disc[0] : $disc[0].'%';
            $discAmount += $discount;
            $price -= $discount;
          }
          $i++;
        }

        $total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
        $total_amount = ( $detail->qty * $detail->price ) - $total_discount; //--- ยอดรวมสุดท้าย

        $arr = array(
              "discount1" => $discLabel[0],
              "discount2" => $discLabel[1],
              "discount3" => $discLabel[2],
              "discount_amount"	=> $total_discount,
              "total_amount" => $total_amount ,
              "id_rule"	=> NULL,
              "update_user" => $user
            );

        $cs = $this->orders_model->update_detail($detail->id, $arr);
        if($cs)
        {
          $log_data = array(
                        "order_code"		=> $code,
                        "product_code"	=> $detail->product_code,
                        "old_discount"	=> discountLabel($detail->discount1, $detail->discount2, $detail->discount3),
                        "new_discount"	=> discountLabel($discLabel[0], $discLabel[1], $discLabel[2]),
                        "user"	=> $user,
                        "approver"		=> get_cookie('uname')
                        );
          $this->discount_logs_model->logs_discount($log_data);
        }
      }

      $this->orders_model->set_status($code, 0);
    }

    echo 'success';
  }


  public function update_non_count_price()
  {
    $code = $this->input->post('order_code');
    $id = $this->input->post('id_order_detail');
    $price = $this->input->post('price');
    $user = get_cookie('uname');

    $order = $this->orders_model->get($code);
    if($order->state == 8) //--- ถ้าเปิดบิลแล้ว
    {
      echo 'ไม่สามารถแก้ไขราคาได้ เนื่องจากออเดอร์ถูกเปิดบิลไปแล้ว';
    }
    else
    {
        //----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
        if( $price != "" )
        {
          //--- ได้ Obj มา
          $detail = $this->orders_model->get_detail($id);

          //--- ถ้ารายการนี้มีอยู่
          if( $detail !== FALSE )
          {
            //------ คำนวณส่วนลดใหม่
            $price_c = $price;
  					$discAmount = 0;
            $step = array($detail->discount1, $detail->discount2, $detail->discount3);
            foreach($step as $discount)
            {
              $disc 	= explode('%', $discount);
              $disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
              $discount = count($disc) == 1 ? $disc[0] : $price_c * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
              $discAmount += $discount;
              $price_c -= $discount;
            }

            $total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
  					$total_amount = ( $detail->qty * $price ) - $total_discount; //--- ยอดรวมสุดท้าย

            $arr = array(
                  "price"	=> $price,
                  "discount_amount"	=> $total_discount,
                  "total_amount" => $total_amount,
                  "update_user" => $user
                );
            $cs = $this->orders_model->update_detail($id, $arr);
          }	//--- end if detail
        } //--- End if value

        $total_amount = $this->orders_model->get_order_total_amount($code);
        $this->orders_model->update($code, ['doc_total' => $total_amount, 'status' => 0]);

      echo 'success';
    }
  }


  public function update_item_price()
  {
    $sc = TRUE;
    $code = $this->input->post('order_code');
    $value = $this->input->post('price');
    $id = $this->input->post('id');

    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      if($order->state < 8)
      {
        $detail = $this->orders_model->get_detail($id);

        //--- ถ้ารายการนี้มีอยู่
  			if( ! empty($detail))
  			{
					//------ คำนวณส่วนลดใหม่
          $price = $value;
					$discAmount = 0;
					$step = array($detail->discount1, $detail->discount2, $detail->discount3);

					foreach($step as $discount_text)
					{
						$disc 	= explode('%', $discount_text);
						$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
						$discount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
						$discAmount += $discount;
						$price -= $discount;
					}

					$total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
					$total_amount = ( $detail->qty * $value ) - $total_discount; //--- ยอดรวมสุดท้าย

					$arr = array(
						'price' => $value,
						'discount_amount' => $total_discount,
						'total_amount' => $total_amount,
						'update_user' => $this->_user->uname
					);

					if( ! $this->orders_model->update_detail($id, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update item price";
          }
          else
          {
            $total_amount = $this->orders_model->get_order_total_amount($code);
            $this->orders_model->update($code, ['doc_total' => $total_amount, 'status' => 0]);
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
        $this->error = "Invalid order state";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid order number";
    }

    $this->_response($sc);
  }


  public function update_price()
  {
    $code = $this->input->post('order_code');
    $ds = $this->input->post('price');
  	$approver	= $this->input->post('approver');
  	$user = get_cookie('uname');
    $this->load->model('orders/discount_logs_model');
  	foreach( $ds as $id => $value )
  	{
  		//----- ข้ามรายการที่ไม่ได้กำหนดค่ามา
  		if( $value != "" )
  		{
  			//--- ได้ Obj มา
  			$detail = $this->orders_model->get_detail($id);

  			//--- ถ้ารายการนี้มีอยู่
  			if( ! empty($detail))
  			{
					//------ คำนวณส่วนลดใหม่
					$price 	= $value;
					$discAmount = 0;
					$step = array($detail->discount1, $detail->discount2, $detail->discount3);
					foreach($step as $discount_text)
					{
						$disc 	= explode('%', $discount_text);
						$disc[0] = trim($disc[0]); //--- ตัดช่องว่างออก
						$discount = count($disc) == 1 ? $disc[0] : $price * ($disc[0] * 0.01); //--- ส่วนลดต่อชิ้น
						$discAmount += $discount;
						$price -= $discount;
					}

					$total_discount = $detail->qty * $discAmount; //---- ส่วนลดรวม
					$total_amount = ( $detail->qty * $value ) - $total_discount; //--- ยอดรวมสุดท้าย

					$arr = array(
						'price' => $value,
						'discount_amount' => $total_discount,
						'total_amount' => $total_amount,
						'update_user' => $user
					);

					$cs = $this->orders_model->update_detail($id, $arr);

					if($cs)
					{
						$log_data = array(
							"order_code"		=> $code,
							"product_code"	=> $detail->product_code,
							"old_price"	=> $detail->price,
							"new_price"	=> $value,
							"user"	=> $user,
							"approver"		=> $approver
						);
						$this->discount_logs_model->logs_price($log_data);
					}

  			}	//--- end if detail
  		} //--- End if value
  	}	//--- end foreach

    $total_amount = $this->orders_model->get_order_total_amount($code);
    $this->orders_model->update($code, ['doc_total' => $total_amount, 'status' => 0]);

  	echo 'success';
  }


  public function get_summary()
  {
    $this->load->model('masters/bank_model');
    $code = $this->input->post('order_code');
    $order = $this->orders_model->get($code);
    $details = $this->orders_model->get_order_details($code);
    $bank = $this->bank_model->get_active_bank();
    if( ! empty($details))
    {
      echo get_summary($order, $details, $bank); //--- order_helper;
    }
  }


  public function get_available_stock($item)
  {
    $sell_stock = $this->stock_model->get_sell_stock($item);
    $ordered = $this->orders_model->get_reserv_stock($item);
    $reserv_stock = $this->reserv_stock_model->get_reserv_stock($item);
    $availableStock = $sell_stock - $ordered - $reserv_stock;
    return $availableStock < 0 ? 0 : $availableStock;
  }


	 //---- send calcurated stock to marketplace
  public function update_api_stock(array $ds = array())
  {
    if($this->sync_api_stock && ! empty($ds))
    {
      $this->load->library('wrx_stock_api');
      $warehouse_code = getConfig('IX_WAREHOUSE');

      foreach($ds as $item)
      {
        $rate = $item->rate > 0 ? ($item->rate < 100 ? $item->rate * 0.01 : 1) : 1;
        $available = $this->get_sell_stock($item->code, $warehouse_code);

        $qty = floor($available * $rate);

        $this->wrx_stock_api->update_available_stock($item->code, $qty);
      }
    }
  }


  public function clear_filter()
  {
    $filter = array(
      'order_code',
			'so_no',
      'order_customer',
      'order_user',
      'order_reference',
      'order_shipCode',
      'order_channels',
      'order_payment',
      'order_fromDate',
      'order_toDate',
      'order_warehouse',
      'notSave',
      'onlyMe',
      'isExpire',
			'sap_status',
			'DoNo',
			'method',
      'order_order_by',
      'order_sort_by',
      'state_1',
      'state_2',
      'state_3',
      'state_4',
      'state_5',
      'state_6',
      'state_7',
      'state_8',
      'state_9',
      'stated',
      'startTime',
      'endTime',
      'is_pre_order',
      'is_backorder',
      'tax_status',
      'is_etax'
    );

    clear_filter($filter);
  }



  public function export_ship_to_address($id)
  {
    $this->load->model('address/customer_address_model');
    $rs = $this->customer_address_model->get_customer_ship_to_address($id);
    if( ! empty($rs))
    {
      $ex = $this->customer_address_model->is_sap_address_exists($rs->code, $rs->address_code, 'S');
      if(! $ex)
      {
        $ds = array(
          'Address' => $rs->address_code,
          'CardCode' => $rs->customer_code,
          'Street' => $rs->address,
          'Block' => $rs->sub_district,
          'ZipCode' => $rs->postcode,
          'City' => $rs->province,
          'County' => $rs->district,
          'LineNum' => ($this->customer_address_model->get_max_line_num($rs->code, 'S') + 1),
          'AdresType' => 'S',
          'Address2' => '0000',
          'Address3' => 'สำนักงานใหญ่',
          'F_E_Commerce' => $ex ? 'U' : 'A',
          'F_E_CommerceDate' => sap_date(now(), TRUE)
        );

        $this->customer_address_model->add_sap_ship_to($ds);
      }
      else
      {
        $ds = array(
          'Address' => $rs->address_code,
          'CardCode' => $rs->customer_code,
          'Street' => $rs->address,
          'Block' => $rs->sub_district,
          'ZipCode' => $rs->postcode,
          'City' => $rs->province,
          'County' => $rs->district,
          'AdresType' => 'S',
          'Address2' => '0000',
          'Address3' => 'สำนักงานใหญ่',
          'F_E_Commerce' => $ex ? 'U' : 'A',
          'F_E_CommerceDate' => sap_date(now(), TRUE)
        );

        $this->customer_address_model->update_sap_ship_to($rs->code, $rs->address_code, $ds);
      }
    }
  }


  public function update_cod_amount()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $amount = $this->input->post('amount');
    $amount = $amount >= 0 ? $amount : 0;
    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      if( $order->state < 3)
      {
        $arr = array(
          'cod_amount' => $amount
        );

        if( ! $this->orders_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update data";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid order code";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_template_file()
  {
    $path = $this->config->item('upload_path').'orders/';
    $file_name = $path."import_order_template.xlsx";

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

}
?>
