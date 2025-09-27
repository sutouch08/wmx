<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Consignment_order extends PS_Controller
{
  public $menu_code = 'ACCMOD';
	public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
	public $title = 'ตัดยอดฝากขาย(เทียม)';
  public $filter;
  public $error;
  public $is_mobile = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'account/consignment_order';
    $this->load->model('account/consignment_order_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/products_model');
    $this->load->library('user_agent');
    $this->load->helper('discount');

    $this->is_mobile = $this->agent->is_mobile();
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'consign_code', ''),
      'customer' => get_filter('customer', 'consign_customer', ''),
      'zone' => get_filter('zone', 'consign_zone', ''),
      'from_date' => get_filter('from_date', 'consign_from_date', ''),
      'to_date' => get_filter('to_date', 'consign_to_date', ''),
      'status' => get_filter('status', 'consign_status', 'all'),
      'ref_code' => get_filter('ref_code', 'consign_ref_code', '')
    );

    //--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->consignment_order_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs = $this->consignment_order_model->get_list($filter, $perpage, $this->uri->segment($segment));
    if(!empty($docs))
    {
      foreach($docs as $rs)
      {
        $rs->amount = $this->consignment_order_model->get_sum_amount($rs->code);
      }
    }

    $filter['docs'] = $docs;

		$this->pagination->initialize($init);

    if( ! $this->is_mobile)
    {
      $this->load->view('account/consignment_order/consignment_order_list', $filter);
    }
    else
    {
      $this->load->view('account/consignment_order/mobile/consignment_order_list_mobile', $filter);
    }
  }



  public function add_new()
  {
    $this->load->view('account/consignment_order/consignment_order_add');
  }


  public function is_exists($code, $old_code = NULL)
  {
    $exists = $this->consignment_order_model->is_exists($code, $old_code);
    if($exists)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }


  public function add()
  {
    $sc = TRUE;
    if($this->pm->can_add)
    {
      if($this->input->post('date_add'))
      {
        $date_add = db_date($this->input->post('date_add'), TRUE);
        $zone = $this->zone_model->get($this->input->post('zone_code'));
        if($this->input->post('code'))
        {
          $code = $this->input->post('code');
        }
        else
        {
          $code = $this->get_new_code($date_add);
        }

        $bookcode = getConfig('BOOK_CODE_CONSIGNMENT_SOLD');

        $arr = array(
          'code' => $code,
          'bookcode' => $bookcode,
          'customer_code' => $this->input->post('customerCode'),
          'customer_name' => $this->input->post('customer'),
          'zone_code' => $zone->code,
          'zone_name' => $zone->name,
          'warehouse_code' => $zone->warehouse_code,
          'remark' => $this->input->post('remark'),
          'date_add' => $date_add,
          'user' => get_cookie('uname')
        );

        if(! $this->consignment_order_model->add($arr))
        {
          $sc = FALSE;
          set_error("เพิ่มเอกสารไม่สำเร็จ");
        }
      }
      else
      {
        $sc = FALSE;
        set_error('ไม่พบข้อมูล/ข้อมูลไม่ครบถ้วน');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('คุณไม่มีสิทธิ์ในการเพิ่มเอกสาร');
    }

    if($sc === TRUE)
    {
      redirect($this->home.'/edit/'.$code);
    }
    else
    {
      redirect($this->home.'/add_new');
    }

  }


  public function edit($code)
  {
    $this->load->helper('print');
    $doc = $this->consignment_order_model->get($code);
    $details = $this->consignment_order_model->get_details($code);

    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $gb_auz = getConfig('ALLOW_UNDER_ZERO');
    $wh_auz = $this->warehouse_model->is_auz($doc->warehouse_code);
    $auz = $gb_auz == 1 ? 1 : ($wh_auz === TRUE ? 1 : 0);
    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'auz' => $auz
    );

    $this->load->view('account/consignment_order/consignment_order_edit', $ds);
  }


  //--- updte header data
  public function update()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    if($code)
    {
      if($this->pm->can_edit)
      {
        $arr = array(
          'date_add' => db_date($this->input->post('date'), TRUE),
          'remark' => trim($this->input->post('remark'))
        );

        if(! $this->consignment_order_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "คุณไม่มีสิทธิ์แก้ไขข้อมูล";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function cancel()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $code = $this->input->post('code');
      $reason = trim($this->input->post('reason'));

      if( ! empty($code))
      {
        $doc = $this->consignment_order_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 1 OR $doc->status == 0)
          {
            $this->db->trans_begin();

            $arr = array(
              'status' => 2,
              'cancle_reason' => get_null($reason),
              'cancle_user' => $this->_user->uname
            );

            if( ! $this->consignment_order_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update document status";
            }

            if($sc === TRUE)
            {
              if( ! $this->consignment_order_model->update_details($code, ['status' => 2]))
              {
                $sc = FALSE;
                $this->error = "Faild to update line status";
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
    $this->load->helper('print');
    $doc = $this->consignment_order_model->get($code);
    $details = $this->consignment_order_model->get_details($code);
    if(!empty($details))
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

    $this->load->view('account/consignment_order/consignment_order_view_detail', $ds);
  }


  //---- add or update detail row by key in
  public function add_detail()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $code = $ds->code;

      $item = $this->products_model->get($ds->product_code);

      if(empty($item))
      {
        $sc = FALSE;
        $this->error = "รหัสสินค้าไม่ถูกต้อง";
      }

      if($sc === TRUE)
      {
        $doc = $this->consignment_order_model->get($ds->code);

        if(empty($doc))
        {
          $sc = FALSE;
          $this->error = "เลขที่เอกสารไม่ถูกต้อง";
        }
      }

      if($sc === TRUE)
      {
        if($doc->status != 0)
        {
          $sc = FALSE;
          set_error('status');
        }
      }

      if($sc === TRUE)
      {
        $this->load->model('stock/stock_model');

        $product_code = $ds->product_code;
        $price = $ds->price;
        $qty = $ds->qty;
        $discLabel = $ds->disc;
        $disc = parse_discount_text($discLabel, $price);
        $discount = $disc['discount_amount'];
        $amount = ($price - $discount) * $qty;

        $gb_auz = getConfig('ALLOW_UNDER_ZERO');
        $wh_auz = $this->warehouse_model->is_auz($doc->warehouse_code);
        $auz = $gb_auz == 1 ? TRUE : $wh_auz;

        $input_type = 1;  //--- 1 = key in , 2 = load diff, 3 = excel
        $stock = $item->count_stock == 1 ? $this->stock_model->get_consign_stock_zone($doc->zone_code, $item->code) : 10000000;
        $c_qty = $item->count_stock == 1 ? $this->consignment_order_model->get_unsave_qty($code, $item->code) : 0;
        $detail = $this->consignment_order_model->get_exists_detail($code, $product_code, $price, $discLabel, $input_type);
        $sum_qty = $qty + $c_qty;

        $id;

        if(empty($detail))
        {
          if($sum_qty <= $stock OR $auz === TRUE)
          {
            $arr = array(
              'consign_code' => $code,
              'product_code' => $item->code,
              'product_name' => $item->name,
              'cost' => $item->cost,
              'price' => $price,
              'qty' => $qty,
              'discount' => discountLabel($disc['discount1'], $disc['discount2'], $disc['discount3']),
              'discount_amount' => $discount * $qty,
              'amount' => $amount,
              'ref_code' => $doc->ref_code,
              'input_type' => $input_type
            );

            $id = $this->consignment_order_model->add_detail($arr);

            if( ! $id)
            {
              $sc = FALSE;
              $this->error = "เพิ่มรายการไม่สำเร็จ";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "{$item->code} ยอดในโซนไม่พอตัด  {$sum_qty}/{$stock}";
          }
        }
        else
        {
          //-- update new rows
          //--- ถ้าจำนวนที่ยังไม่บันทึก รวมกับจำนวนใหม่ไม่เกินยอดในโซน หรือ คลังสามารถติดลบได้
          $id = $detail->id;
          $new_qty = $qty + $detail->qty;

          if($sum_qty <= $stock OR $auz === TRUE)
          {
            $arr = array(
              'qty' => $new_qty,
              'discount_amount' => $discount * $new_qty,
              'amount' => ($price - $discount) * $new_qty
            );

            if( ! $this->consignment_order_model->update_detail($id, $arr))
            {
              $sc = FALSE;
              $this->error = "ปรับปรุงรายการไม่สำเร็จ";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "{$item->code} ยอดในโซนไม่พอตัด  {$sum_qty}/{$stock}";
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    if($sc === TRUE)
    {
      $rs = $this->consignment_order_model->get_detail($id);

      $row = array(
        'id' => $rs->id,
        'barcode' => $item->barcode,
        'product_code' => $rs->product_code,
        'product_name' => $rs->product_name,
        'price' => number($rs->price,2),
        'qty' => number($rs->qty, 2),
        'discount' => $rs->discount,
        'amount' => number($rs->amount, 2)
      );
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $row : NULL
    );

    echo $sc === TRUE ? json_encode($arr) : $this->error;
  }


  public function update_detail()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $doc = $this->consignment_order_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          $arr = array(
            'price' => $ds->price,
            'qty' => $ds->qty,
            'discount' => $ds->discount,
            'discount_amount' => $ds->discount_amount,
            'amount' => $ds->amount
          );

          if( ! $this->consignment_order_model->update_detail($ds->id, $arr))
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

    $this->_response($sc);
  }


  public function save_consign($code)
  {
    $sc = TRUE;
    $this->load->model("stock/stock_model");

    $doc = $this->consignment_order_model->get($code);
    $gb_auz = getConfig('ALLOW_UNDER_ZERO');
    $wh_auz = $this->warehouse_model->is_auz($doc->warehouse_code);
    $auz = $gb_auz == 1 ? TRUE : $wh_auz ;
    $ex = 0;

    if($doc->status == 0)
    {
      $details = $this->consignment_order_model->get_details($code);

      if( ! empty($details))
      {
        $this->db->trans_begin();

        //--- check stock and update status each row
        foreach($details as $rs)
        {
          //--- get item info
          $item = $this->products_model->get($rs->product_code);

          if( ! empty($item))
          {
            $stock = $item->count_stock == 1 ?$this->stock_model->get_consign_stock_zone($doc->zone_code, $item->code) : 1000000;
            $all_qty = $this->consignment_order_model->get_sum_order_qty($doc->code, $item->code);

            if($all_qty > $stock && ! $auz)
            {
              $sc = FALSE;
              $this->error .= "<span>{$item->code} ยอดในโซนไม่พอตัด  ในโซน: {$stock} ยอดตัด : {$all_qty} </span><br/>";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error .= "<span>ไม่พบรายการสินค้า : {$rs->product_code} </span></br/>";
          }
        }

        //--- if no error
        if($sc === TRUE)
        {
          if( ! $this->consignment_order_model->change_status($code, 1))
          {
            $sc = FALSE;
            $this->error = "บันทึกสถานะเอกสารไม่สำเร็จ";
          }
        }

        if($sc === FALSE)
        {
          $this->db->trans_rollback();
        }
        else
        {
          $this->db->trans_commit();
        }

        if($sc === TRUE )
        {
          if(is_true(getConfig('WRX_API')) && is_true(getConfig('WRX_CONSIGNMENT_INTERFACE')))
          {
            $this->load->library('wrx_consignment_api');

            if( ! $this->wrx_consignment_api->export_consignment($code))
            {
              $ex = 1;
              $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป Oracle ไม่สำเร็จ กรุณากดส่งข้อมูลใหม่อีกครั้งภายหลัง";
            }
          }
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('status');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function rollback($code)
  {
    $sc = TRUE;
    $this->load->model("stock/stock_model");

    $doc = $this->consignment_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 1 OR $doc->status == 2)
      {
        $arr = array(
          'status' => 0,
          'cancle_reason' => NULL,
          'cancle_user' => NULL
        );

        $this->db->trans_begin();

        if( ! $this->consignment_order_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update document status";
        }

        if($sc === TRUE)
        {
          if( ! $this->consignment_order_model->update_details($code, ['status' => 0]))
          {
            $sc = FALSE;
            $this->error = "Failed to update line status";
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
      set_error('notfound');
    }

    $this->_response($sc);
  }


  public function delete_detail($id)
  {
    $sc = TRUE;
    $ds = $this->consignment_order_model->get_detail($id);
    if(!empty($ds))
    {
      if($ds->status == 1)
      {
        $sc = FALSE;
        $this->error = "รายการถูกบันทึกแล้วไม่สามารถลบได้";
      }
      else
      {
        if(! $this->consignment_order_model->delete_detail($id))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการที่ต้องการลบ";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_item_by_code()
  {
    if($this->input->get('code'))
    {
      $this->load->model('stock/stock_model');

      $product_code = $this->input->get('code');
      $zone_code = $this->input->get('zone_code');
      $item = $this->products_model->get($product_code);

      if(!empty($item))
      {
        $gp = $this->consignment_order_model->get_item_gp($item->code, $zone_code);
        $stock = $item->count_stock == 1 ? $this->stock_model->get_consign_stock_zone($zone_code, $item->code) : 0;

        $arr = array(
          'pdCode' => $item->code,
          'barcode' => $item->barcode,
          'product' => $item->code,
          'price' => round($item->price, 2),
          'disc' => $gp,
          'stock' => $stock,
          'count_stock' => $item->count_stock
        );

        $sc = json_encode($arr);
      }
      else
      {
        $sc = 'สินค้าไม่ถูกต้อง';
      }

      echo $sc;
    }
    else
    {
      echo "สินค้าไม่ถูกต้อง";
    }
  }


  public function get_item_by_barcode()
  {
    if($this->input->get('barcode'))
    {
      $this->load->model('stock/stock_model');

      $barcode = $this->input->get('barcode');
      $zone_code = $this->input->get('zone_code');
      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        $gp  = $this->consignment_order_model->get_item_gp($item->code, $zone_code);
        $stock = $item->count_stock == 1 ? $this->stock_model->get_consign_stock_zone($zone_code, $item->code) : 0;

        $arr = array(
          'pdCode' => $item->code,
          'barcode' => $item->barcode,
          'product' => $item->code,
          'price' => round($item->price, 2),
          'disc' => $gp,
          'stock' => $stock,
          'count_stock' => $item->count_stock
        );

        $sc = json_encode($arr);
      }
      else
      {
        $sc = 'สินค้าไม่ถูกต้อง';
      }

      echo $sc;
    }
    else
    {
      echo "สินค้าไม่ถูกต้อง";
    }
  }


  public function send_to_erp($code)
  {
    $sc = TRUE;

    $doc = $this->consignment_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 1)
      {
        if(is_true(getConfig('WRX_API')) && is_true(getConfig('WRX_CONSIGNMENT_INTERFACE')))
        {
          $this->load->library('wrx_consignment_api');

          if( ! $this->wrx_consignment_api->export_consignment($code))
          {
            $sc = FALSE;
            $this->error = $this->wrx_consignment_api->error;
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
      set_error('required');
    }

    $this->_response($sc);
  }


  public function print_consign($code)
  {
    $this->load->library('printer');

    $doc = $this->consignment_order_model->get($code);
    if(!empty($doc))
    {
      $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    }

    $details = $this->consignment_order_model->get_details($code);
    if(!empty($details))
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

    $this->load->view('print/print_consign_sold', $ds);
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_CONSIGNMENT_SOLD');
    $run_digit = getConfig('RUN_DIGIT_CONSIGNMENT_SOLD');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->consignment_order_model->get_max_code($pre);
    if(!empty($code))
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
      'consign_code',
      'consign_customer',
      'consign_zone',
      'consign_from_date',
      'consign_to_date',
      'consign_status',
      'consign_ref_code'
    );
    clear_filter($filter);
  }


} //---- end class
 ?>
