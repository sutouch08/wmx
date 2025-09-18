<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Move extends PS_Controller
{
  public $menu_code = 'ICTRMV';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
	public $title = 'ย้ายพื้นที่จัดเก็บ';
  public $segment = 4;
  public $is_mobile = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/move';
    $this->load->model('inventory/move_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');
    $this->load->helper('warehouse');
    $this->load->library('user_agent');

    $this->is_mobile = $this->agent->is_mobile();
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'move_code', ''),
      'warehouse' => get_filter('warehouse', 'move_warehouse', 'all'),
      'user' => get_filter('user', 'move_user', 'all'),
      'from_date' => get_filter('fromDate', 'move_fromDate', ''),
      'to_date' => get_filter('toDate', 'move_toDate', ''),
      'status' => get_filter('status', 'move_status', ($this->is_mobile ? '0' : 'all')),
      'is_export' => get_filter('is_export', 'move_is_export', 'all'),
      'must_accept' => get_filter('must_accept', 'move_must_accept', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->move_model->count_rows($filter);
      $filter['list'] = $this->move_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);

      if($this->is_mobile)
      {
        $this->load->view('move/move_list_mobile', $filter);
      }
      else
      {
        $this->load->view('move/move_list', $filter);
      }
    }
  }


  public function view_detail($code)
  {
    $doc = $this->move_model->get($code);

    $details = $this->move_model->get_details($code);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $rs->temp_qty = $this->move_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'accept_list' => $doc->must_accept == 1 ? $this->move_model->get_accept_list($code) : NULL,
      'barcode' => FALSE
    );

    $this->load->view('move/move_view', $ds);
  }


  public function add_new()
  {
    $this->load->view('move/move_add');
  }


  public function add()
  {
    $sc = TRUE;
    $code = NULL;

    if($this->pm->can_add)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $date_add = db_date($ds->date_add);
        $bookcode = getConfig('BOOK_CODE_MOVE');

        if(empty($ds->warehouse_code))
        {
          $sc = FALSE;
          set_error('required');
        }

        if($sc === TRUE)
        {
          $code = $this->get_new_code($date_add);

          $arr = array(
            'code' => $code,
            'bookcode' => $bookcode,
            'reference' => get_null(trim($ds->reference)),
            'from_warehouse' => $ds->warehouse_code,
            'to_warehouse' => $ds->warehouse_code,
            'remark' => get_null(trim($ds->remark)),
            'user' => $this->_user->uname
          );

          if( ! $this->move_model->add($arr))
          {
            $sc = FALSE;
            set_error('insert');
          }
        }
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
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code, $barcode = 'Y')
  {
    $doc = $this->move_model->get($code);

    if( ! empty($doc))
    {
      $doc->warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
    }

    if($doc->status == 1)
    {
      redirect($this->home."/view_detail/{$code}");
      exit();
    }

    $details = $this->is_mobile ? NULL : $this->move_model->get_details($code);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->move_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'barcode' => $barcode == 'N' ? FALSE : TRUE
    );

    if($this->is_mobile)
    {
      $ds['title'] = $doc->code."<br/>".$doc->from_warehouse." | ".$doc->warehouse_name;
      $this->load->view('move/move_edit_mobile', $ds);
    }
    else
    {
      if($barcode == 'N')
      {
        $this->load->view('move/move_edit', $ds);
      }
      else
      {
        $this->load->view('move/move_edit_barcode', $ds);
      }
    }
  }


  public function check_temp_exists($code)
  {
    $temp = $this->move_model->is_exists_temp($code);

    if($temp === TRUE)
    {
      echo 'exists';
    }
    else
    {
      echo 'not_exists';
    }
  }


  public function save_move($code)
  {
    $sc = TRUE;

    $doc = $this->move_model->get($code);

    if( ! empty($doc))
    {
      $this->load->model('stock/stock_model');
      $this->load->model('inventory/movement_model');

      if($doc->status == 0)
      {
        $details = $this->move_model->get_details($code);

        if( ! empty($details))
        {
          foreach($details as $rs)
          {
            if($sc === FALSE) { break; }

            $stock = $this->stock_model->get_stock_zone($rs->from_zone, $rs->product_code);

            if($stock < $rs->qty)
            {
              $sc = FALSE;
              $this->error = "สต็อกในโซนต้นทางไม่พอย้ายออก <br/>Zone : {$rs->from_zone}<br/>SKU: {$rs->product_code}<br/>Qty: {$rs->qty} / {$stock}";
            }
          }
        }

        if($sc === TRUE)
        {
          $this->db->trans_begin();

          $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              if( ! $this->stock_model->update_stock_zone($rs->from_zone, $rs->product_code, ($rs->qty * -1)))
              {
                $sc = FALSE;
                $this->error = "ย้ายสต็อกออกจากโซนไม่สำเร็จ <br/>Zone : {$rs->from_zone}<br/>SKU: {$rs->product_code}<br/>Qty: {$rs->qty}";
              }

              if($sc === TRUE)
              {
                //--- 2. update movement
                $move_out = array(
                  'reference' => $code,
                  'warehouse_code' => $doc->from_warehouse,
                  'zone_code' => $rs->from_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => 0,
                  'move_out' => $rs->qty,
                  'date_add' => $date_add
                );

                //--- move out
                if($this->movement_model->add($move_out) === FALSE)
                {
                  $sc = FALSE;
                  $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->stock_model->update_stock_zone($rs->to_zone, $rs->product_code, $rs->qty))
                {
                  $sc = FALSE;
                  $this->error = "ย้ายสต็อกเข้าโซนไม่สำเร็จ <br/>Zone : {$rs->to_zone}<br/>SKU: {$rs->product_code}<br/>Qty: {$rs->qty}";
                }
              }

              if($sc === TRUE)
              {
                $move_in = array(
                  'reference' => $code,
                  'warehouse_code' => $doc->to_warehouse,
                  'zone_code' => $rs->to_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => $rs->qty,
                  'move_out' => 0,
                  'date_add' => $date_add
                );

                //--- move in
                if($this->movement_model->add($move_in) === FALSE)
                {
                  $sc = FALSE;
                  $this->error = 'บันทึก movement ขาเข้าไม่สำเร็จ';
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->move_model->update_detail($rs->id, ['valid' => 1]))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update row status";
                }
              }
            }
          }

          if($sc === TRUE)
          {
            $arr = array(
              'status' => 1,
              'shipped_date' => $date_add,
              'update_user' => $this->_user->uname
            );

            if( ! $this->move_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Update Status Failed";
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
        $this->error = "Invalid Document Status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid Document Number";
    }

    $this->_response($sc);
  }


  public function add_to_move()
  {
    $this->load->model('masters/products_model');

    $sc = TRUE;

    $code = $this->input->post('move_code');
    if( ! empty($code))
    {
      $doc = $this->move_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          $from_zone = $this->input->post('from_zone');
          $to_zone = $this->input->post('to_zone');

          if( $from_zone != $to_zone )
          {
            $fzone = $this->zone_model->get($from_zone);
            $tzone = $this->zone_model->get($to_zone);

            if( ! empty($fzone) && ! empty($tzone))
            {
              $items = json_decode($this->input->post('items'));

              if( ! empty($items))
              {
                $this->db->trans_begin();

                foreach($items as $item)
                {
                  if( $sc === FALSE) { break; }

                  $id = $this->move_model->get_id($code, $item->code, $from_zone, $to_zone);

                  if( ! empty($id))
                  {
                    if( ! $this->move_model->update_qty($id, $item->qty))
                    {
                      $sc = FALSE;
                      $this->error = "Update Move Item Qty Failed";
                    }
                  }
                  else
                  {
                    $arr = array(
                      'move_code' => $code,
                      'product_code' => $item->code,
                      'product_name' => $this->products_model->get_name($item->code),
                      'from_zone' => $from_zone,
                      'to_zone' => $to_zone,
                      'qty' => $item->qty
                    );

                    if( ! $this->move_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = "Insert Move Item Failed";
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
                $this->error = "ไม่พบรายการที่ต้องการย้าย";
              }
            }
            else
            {
              if(empty($fzone))
              {
                $sc = FALSE;
                $this->error = "โซนต้นทางไม่ถูกต้อง";
              }

              if(empty($tzone))
              {
                $sc = FALSE;
                $this->error = "โซนปลายทางไม่ถูกต้อง";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "โซนต้นทาง - ปลายทาง ต้องเป็นคนละโซนกัน";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid Document Status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Document Number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing Required Parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function add_to_temp()
  {
    $sc = TRUE;

    if($this->input->post('move_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('move_code');
      $zone_code = $this->input->post('from_zone');
      $barcode = trim($this->input->post('barcode'));
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);

      if( ! empty($item))
      {
        $product_code = $item->code;
        $stock = $this->stock_model->get_stock_zone($zone_code, $product_code);
        //--- จำนวนที่อยู่ใน temp
        $temp_qty = $this->move_model->get_temp_qty($code, $product_code, $zone_code);
        //--- จำนวนที่อยู่ใน move_detail และยังไม่ valid
        $move_qty = $this->move_model->get_move_qty($code, $product_code, $zone_code);
        //--- จำนวนที่โอนได้คงเหลือ
        $cqty = $stock - ($temp_qty + $move_qty);

        if($qty <= $cqty)
        {
          $arr = array(
            'move_code' => $code,
            'product_code' => $product_code,
            'zone_code' => $zone_code,
            'qty' => $qty
          );

          if($this->move_model->update_temp($arr) === FALSE)
          {
            $sc = FALSE;
            $this->error = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
          }

        }
        else
        {
          $sc = FALSE;
          $this->error = 'ยอดในโซนไม่เพียงพอ';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    $this->_response($sc);
  }


  public function move_to_zone()
  {
    $sc = TRUE;

    if($this->input->post('move_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('move_code');
      $barcode = trim($this->input->post('barcode'));
      $to_zone = $this->input->post('zone_code');
      $zone = $this->zone_model->get($to_zone);
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);

      if( ! empty($item))
      {
        //--- ย้ายจำนวนใน temp มาเพิ่มเข้า move detail
        //--- โดยเอา temp ออกมา(อาจมีหลายรายการ เพราะอาจมาจากหลายโซน
        //--- ดึงรายการจาก temp ตามรายการสินค้า (อาจมีหลายบรรทัด)
        $temp = $this->move_model->get_temp_product($code, $item->code);

        if(! empty($temp))
        {
          //--- เริ่มใช้งาน transction
          $this->db->trans_begin();

          foreach($temp as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            if($rs->zone_code != $to_zone)
            {
              if($qty > 0 && $rs->qty > 0)
              {
                //---- ยอดที่ต้องการย้าย น้อยกว่าหรือเท่ากับ ยอดใน temp มั้ย
                //---- ถ้าใช่ ใช้ยอดที่ต้องการย้ายได้เลย
                //---- แต่ถ้ายอดที่ต้องการย้ายมากว่ายอดใน temp แล้วยกยอดที่เหลือไปย้ายในรอบถัดไป(ถ้ามี)
                $temp_qty = $qty <= $rs->qty ? $qty : $rs->qty;
                $id = $this->move_model->get_id($code, $item->code, $rs->zone_code, $to_zone);
                //--- ถ้าพบไอดีให้แก้ไขจำนวน
                if( ! empty($id))
                {
                  if($this->move_model->update_qty($id, $temp_qty) === FALSE)
                  {
                    $sc = FALSE;
                    $this->error = 'แก้ไขยอดในรายการไม่สำเร็จ';
                    break;
                  }
                }
                else
                {
                  //--- ถ้ายังไม่มีรายการ ให้เพิ่มใหม่
                  $ds = array(
                    'move_code' => $code,
                    'product_code' => $item->code,
                    'product_name' => $item->name,
                    'from_zone' => $rs->zone_code,
                    'to_zone' => $to_zone,
                    'qty' => $temp_qty
                  );

                  if($this->move_model->add_detail($ds) === FALSE)
                  {
                    $sc = FALSE;
                    $this->error = 'เพิ่มรายการไม่สำเร็จ';
                    break;
                  }
                }
                //--- ถ้าเพิ่มหรือแก้ไข detail เสร็จแล้ว ทำการ ลดยอดใน temp ตามยอดที่เพิ่มเข้า detail
                if($this->move_model->update_temp_qty($rs->id, ($temp_qty * -1)) === FALSE)
                {
                  $sc = FALSE;
                  $this->error = 'แก้ไขยอดใน temp ไม่สำเร็จ';
                  break;
                }

                //--- ตัดยอดที่ต้องการย้ายออก เพื่อยกยอดไปรอบต่อไป
                $qty -= $temp_qty;
              }
              else
              {
                break;
              } //-- end if qty > 0
            }
            else
            {
              $sc = FALSE;
              $this->error = 'โซนต้นทาง - ปลายทาง ต้องไม่ใช่โซนเดียวกัน';
            }

            //--- ลบ temp ที่ยอดเป็น 0
            $this->move_model->drop_zero_temp();
          } //--- end foreach

          //--- เมื่อทำงานจนจบแล้ว ถ้ายังเหลือยอด แสดงว่ายอดที่ต้องการย้ายเข้า มากกว่ายอดที่ย้ายออกมา
          //--- จะให้ทำกร roll back แล้วแจ้งกลับ
          if($sc === TRUE)
          {
            if($qty > 0)
            {
              $sc = FALSE;
              $this->error = 'ยอดที่ย้ายเข้ามากกว่ายอดที่ย้ายออกมา';
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
          $this->error = 'ไม่พบรายการใน temp';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    $this->_response($sc);
  }


  public function is_exists_detail($code)
  {
    $detail = $this->move_model->is_exists_detail($code);

    $temp = $this->move_model->is_exists_temp($code);

    if($detail === FALSE && $temp === FALSE)
    {
      echo 'not_exists';
    }
    else
    {
      echo 'exists';
    }
  }


  public function get_temp_table($code)
  {
    $ds = array();
    $temp = $this->move_model->get_move_temp($code);
    if( ! empty($temp))
    {
      $no = 1;
      foreach($temp as $rs)
      {
        $btn_delete = '';
        $btn_delete .= '<button type="button" class="btn btn-minier btn-danger" ';
        $btn_delete .= 'onclick="deleteMoveTemp('.$rs->id.', \''.$rs->product_code.'\')">';
        $btn_delete .= '<i class="fa fa-trash"></i></button>';
        $arr = array(
          'no' => $no,
          'id' => $rs->id,
          'barcode' => $rs->barcode,
          'products' => $rs->product_code,
          'from_zone' => $rs->zone_code,
          'fromZone' => $this->zone_model->get_name($rs->zone_code),
          'qty' => $rs->qty,
          'btn_delete' => $btn_delete
        );

        array_push($ds, $arr);
        $no++;
      }
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }


  public function get_move_table($code)
  {
    $ds = array();
    $details = $this->move_model->get_details($code);

    if( ! empty($details))
    {
      $no = 1;
      $total_qty = 0;
      foreach($details as $rs)
      {
        $btn_delete = '';
        if($this->pm->can_add OR $this->pm->can_edit && $rs->valid == 0)
        {
          $btn_delete .= '<button type="button" class="btn btn-minier btn-danger" ';
          $btn_delete .= 'onclick="deleteMoveItem('.$rs->id.', \''.$rs->product_code.'\')">';
          $btn_delete .= '<i class="fa fa-trash"></i></button>';
        }

        $arr = array(
          'id' => $rs->id,
          'no' => $no,
          'barcode' => $rs->barcode,
          'products' => $rs->product_code,
          'from_zone' => $this->zone_model->get_name($rs->from_zone),
          'to_zone' => $this->zone_model->get_name($rs->to_zone),
          'qty' => number($rs->qty),
          'btn_delete' => $btn_delete
        );

        array_push($ds, $arr);
        $no++;
        $total_qty += $rs->qty;
      } //--- end foreach

      $arr = array(
        'total' => number($total_qty)
      );

      array_push($ds, $arr);
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }


  public function get_move_zone($warehouse = NULL)
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $zone = $this->zone_model->search($txt, $warehouse);
    if( ! empty($zone))
    {
      foreach($zone as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบโซน';
    }

    echo json_encode($sc);
  }


  public function get_to_zone()
  {
    $sc = TRUE;
    $move_code = $this->input->get('move_code');
    $zone_code = $this->input->get('zone_code');
    $whs_code = $this->input->get('warehouse_code');

    $zone = $this->zone_model->get_zone($zone_code, $whs_code);

    if(empty($zone))
    {
      $sc = FALSE;
      $this->error = "รหัสโซนไม่ถูกต้อง หรือ โซนไม่ตรงกับเอกสาร";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'zone' => $sc === TRUE ? $zone : NULL
    );

    echo json_encode($arr);
  }


  public function get_from_zone()
  {
    $sc = TRUE;
    $ds = array();
    $zone_code = $this->input->get('zone_code');
    $warehouse_code = $this->input->get('warehouse_code');
    $move_code = $this->input->get('move_code');

    if($zone_code && $move_code)
    {
      $zone = $this->zone_model->get_zone($zone_code, $warehouse_code);

      if( ! empty($zone))
      {
        $stock = $this->stock_model->get_all_stock_in_zone($zone_code);

        if( ! empty($stock))
        {
          $this->load->model('masters/products_model');
          $no = 1;

          foreach($stock as $rs)
          {
            //--- จำนวนที่อยู่ใน temp
            $temp_qty = $this->move_model->get_temp_qty($move_code, $rs->product_code, $zone_code);
            //--- จำนวนที่อยู่ใน move_detail และยังไม่ valid
            $move_qty = $this->move_model->get_move_qty($move_code, $rs->product_code, $zone_code);
            //--- จำนวนที่โอนได้คงเหลือ
            $qty = $rs->qty - ($temp_qty + $move_qty);

            if($qty > 0)
            {
              $ds[] = array(
                'no' => $no,
                'barcode' => $this->products_model->get_barcode($rs->product_code),
                'products' => $rs->product_code,
                'qty' => $qty
              );

              $no++;
            }
          }
        }
        else
        {
          $ds[] = array('nodata' => 'nodata');
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid zone";
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
      'zone' => $sc === TRUE ? $zone : NULL,
      'data' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_product_in_zone()
  {
    $sc = array();

    if($this->input->get('zone_code'))
    {
      $this->load->model('masters/products_model');

      $zone_code = $this->input->get('zone_code');
      $move_code = $this->input->get('move_code');
      $stock = $this->stock_model->get_all_stock_in_zone($zone_code);
      if( ! empty($stock))
      {
        $no = 1;
        foreach($stock as $rs)
        {
          //--- จำนวนที่อยู่ใน temp
          $temp_qty = $this->move_model->get_temp_qty($move_code, $rs->product_code, $zone_code);
          //--- จำนวนที่อยู่ใน move_detail และยังไม่ valid
          $move_qty = $this->move_model->get_move_qty($move_code, $rs->product_code, $zone_code);
          //--- จำนวนที่โอนได้คงเหลือ
          $qty = $rs->qty - ($temp_qty + $move_qty);

          if($qty > 0)
          {
            $arr = array(
              'no' => $no,
              'barcode' => $this->products_model->get_barcode($rs->product_code),
              'products' => $rs->product_code,
              'qty' => $qty
            );

            array_push($sc, $arr);
            $no++;
          }
        }
      }
      else
      {
        array_push($sc, array("nodata" => "nodata"));
      }
      echo json_encode($sc);
    }
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_MOVE');
    $run_digit = getConfig('RUN_DIGIT_MOVE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->move_model->get_max_code($pre);
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


  public function delete_detail()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $id = $this->input->post('id');

    if( ! $this->move_model->drop_detail($id))
    {
      $sc = FALSE;
      $this->error = "Delete Failed";
    }

    $this->_response($sc);
  }


  public function delete_temp()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $id = $this->input->post('id');

    if( ! $this->move_model->drop_temp($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete temp";
    }

    $this->_response($sc);
  }


  public function delete_move($code)
  {
    $sc = TRUE;
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/movement_model');

    $doc = $this->move_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status != 2)
      {
        if($doc->status == 1)
        {
          $details = $this->move_model->get_details($code);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              $stock = $this->stock_model->get_stock_zone($rs->to_zone, $rs->product_code);

              if($stock < $rs->qty)
              {
                $sc = FALSE;
                $this->error = "สต็อกคงเหลือในโซนไม่พอย้ายกลับ <br/>Zone : {$rs->to_zone} <br/>SKU : {$rs->product_code}<br/>Qty: {$rs->qty} / {$stock}";
              }
            }
          }
        }

        if($sc === TRUE)
        {
          $this->db->trans_begin();

          if($doc->status == 1 && ! empty($details))
          {
            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              if( ! $this->stock_model->update_stock_zone($rs->to_zone, $rs->product_code, ($rs->qty * -1)))
              {
                $sc = FALSE;
                $this->error = "ตัดสต็อกในโซนไม่สำเร็จ <br/>Zone : {$rs->to_zone} <br/>SKU : {$rs->product_code}<br/>Qty: {$rs->qty}";
              }

              if($sc === TRUE)
              {
                if( ! $this->stock_model->update_stock_zone($rs->from_zone, $rs->product_code, $rs->qty))
                {
                  $this->error = "ย้ายสต็อกเข้าโซนไม่สำเร็จ <br/>Zone : {$rs->from_zone} <br/>SKU : {$rs->product_code}<br/>Qty: {$rs->qty}";
                }
              }
            }
          }

          if($sc === TRUE)
          {
            //--- clear temp
            if(! $this->move_model->drop_all_temp($code))
            {
              $sc = FALSE;
              $this->error = "ลบ temp ไม่สำเร็จ";
            }
          }

          if($sc === TRUE)
          {
            //--- delete detail
            if(! $this->move_model->drop_all_detail($code))
            {
              $sc = FALSE;
              $this->error = "ลบรายการไม่สำเร็จ";
            }
          }

          if($sc === TRUE)
          {
            //--- drop movement
            if( ! $this->movement_model->drop_movement($code))
            {
              $sc = FALSE;
              $this->error = "ลบ Movement ไม่สำเร็จ";
            }
          }

          //--- Mare as Cancled
          if( $sc === TRUE)
          {
            $arr = array(
              'status' => 2,
              'cancle_reason' => trim($this->input->post('reason')),
              'cancle_user' => $this->_user->uname
            );

            if( ! $this->move_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "ยกเลิกเอกสารไม่สำเร็จ";
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
      set_error('notfound');
    }

    $this->_response($sc);
  }


  public function print_move($code)
  {
    $this->load->library('printer');
    $doc = $this->move_model->get($code);
    if( ! empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->move_model->get_details($code);
    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $rs->temp_qty = $this->move_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_move', $ds);
  }


  public function clear_filter()
  {
    $filter = array(
      'move_code',
      'move_warehouse',
      'move_user',
      'move_fromDate',
      'move_toDate',
      'move_status',
      'move_is_export',
      'move_must_accept'
    );

    clear_filter($filter);
    echo 'done';
  }

} //--- end class
?>
