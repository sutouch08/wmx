<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends PS_Controller
{
  public $menu_code = 'SOODSO';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
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
      'role' => get_filter('role', 'order_role', 'all'),
      'code' => get_filter('code', 'order_code', ''),
      'so_no' =>  get_filter('so_no', 'so_no', ''),
      'fulfillment_code' => get_filter('fulfillment_code', 'fulfillment_code', ''),
      'reference' => get_filter('reference', 'order_reference', ''),
      'customer' => get_filter('customer', 'order_customer', ''),
      'ship_code' => get_filter('shipCode', 'order_shipCode', ''),
      'channels' => get_filter('channels', 'order_channels', 'all'),
      'payment' => get_filter('payment', 'order_payment', 'all'),
      'from_date' => get_filter('fromDate', 'order_fromDate', ''),
      'to_date' => get_filter('toDate', 'order_toDate', ''),
      'warehouse' => get_filter('warehouse', 'order_warehouse', 'all'),
      'stated' => get_filter('stated', 'stated', ''),
      'startTime' => get_filter('startTime', 'startTime', ''),
      'endTime' => get_filter('endTime', 'endTime', ''),
      'is_backorder' => get_filter('is_backorder', 'is_backorder', 'all'),
      'is_cancled' => get_filter('is_cancled', 'is_cancled', 'all')
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
			$ds['cancle_reason'] = ($rs->state == 9 ? $this->orders_model->get_cancel_reason($code) : NULL);
	    $this->load->view('orders/order_edit', $ds);
    }
		else
		{
			$err = "ไม่พบเลขที่เอกสาร : {$code}";
			$this->page_error($err);
		}
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

			$this->orders_model->add_cancel_reason($reason);
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


  public function cancel_order()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $code = $ds->code;
        $order = $this->orders_model->get($code);

        if( ! empty($order))
        {
          $this->load->model('inventory/prepare_model');
          $this->load->model('inventory/qc_model');
          $this->load->model('inventory/invoice_model');
          $this->load->model('inventory/buffer_model');
          $this->load->model('inventory/cancle_model');
      		$this->load->model('inventory/movement_model');
          $this->load->model('masters/zone_model');

          $this->db->trans_begin();

          if( ! empty($ds->reason_id))
      		{
      			//----- add reason to table order_cancle_reason
      			$reason = array(
      				'code' => $code,
              'reason_id' => $ds->reason_id,
      				'reason' => get_null($ds->reason),
      				'user' => $this->_user->uname
      			);

      			$this->orders_model->add_cancel_reason($reason);
      		}

          if($order->state > 3)
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
            //--- 1. เคลียร์ buffer
            if(! $this->buffer_model->delete_all($code) )
            {
              $sc = FALSE;
              $this->error = "Delete buffer failed";
            }

            if($sc === TRUE)
            {
              if(! $this->prepare_model->clear_prepare($code) )
              {
                $sc = FALSE;
                $this->error = "Delete prepared data failed";
              }
            }

            if($sc === TRUE)
            {
              if(! $this->qc_model->clear_qc($code) )
              {
                $sc = FALSE;
                $this->error = "Delete QC failed";
              }
            }

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
              if(! $this->orders_model->cancle_order_detail($code) )
              {
                $sc = FALSE;
                $this->error = "Cancle Order details failed";
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 2,
                'state' => 9,
                'is_exported' => 0,
                'update_user' => $this->_user->uname
              );

              if(! $this->orders_model->update($code, $arr) )
              {
                $sc = FALSE;
                $this->error = "Change order status failed";
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'order_code' => $code,
                'state' => 9,
                'update_user' => $this->_user->uname
              );

              if( ! $this->order_state_model->add_state($arr) )
              {
                $sc = FALSE;
                $this->error = "Add state failed";
              }
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
      'order_role',
      'order_code',
			'so_no',
      'fulfillment_code',
      'order_customer',
      'order_reference',
      'order_shipCode',
      'order_channels',
      'order_payment',
      'order_fromDate',
      'order_toDate',
      'order_warehouse',
      'state_1',
      'state_2',
      'state_3',
      'state_4',
      'state_5',
      'state_6',
      'state_7',
      'state_8',
      'state_9',
      'is_backorder',
      'is_cancled'
    );

    clear_filter($filter);
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

}
?>
