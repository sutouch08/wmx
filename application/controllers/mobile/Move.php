<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Move extends PS_Controller
{
  public $menu_code = 'ICTRMV';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
	public $title = 'ย้ายพื้นที่จัดเก็บ';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'mobile/move';
    $this->load->model('inventory/move_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');
    $this->load->helper('warehouse');
    $this->load->helper('move');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'move_code', ''),
      'warehouse' => get_filter('warehouse', 'move_warehouse', 'all'),
      'user' => get_filter('user', 'move_user', 'all'),
      'from_date' => get_filter('fromDate', 'move_fromDate', ''),
      'to_date' => get_filter('toDate', 'move_toDate', ''),
      'status' => get_filter('status', 'move_status', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->move_model->count_rows($filter);
      $filter['data'] = $this->move_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = mobile_pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('mobile/move/move_list', $filter);
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
      'details' => $details
    );

    $this->load->view('mobile/move/move_view_detail', $ds);
  }


  public function add_new()
  {
    $this->load->view('mobile/move/move_add');
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
            'date_add' => $date_add,
            'reference' => get_null(trim($ds->reference)),
            'warehouse_code' => $ds->warehouse_code,
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


  public function edit($code, $tab = 'summary')
  {
    $doc = $this->move_model->get($code);

    if( empty($doc))
    {
      $this->page_error();
      exit();
    }

    if($doc->status != 'P')
    {
      redirect($this->home."/view_detail/{$code}");
      exit();
    }

    $totalQty = 0;
    $zoneName = [];

    if($tab == "summary")
    {
      $details = $this->move_model->get_details($code);

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          if(empty($zoneName[$rs->from_zone]))
          {
            $zoneName[$rs->from_zone] = $this->zone_model->get_name($rs->from_zone);
          }

          if(empty($zoneName[$rs->to_zone]))
          {
            $zoneName[$rs->to_zone] = $this->zone_model->get_name($rs->to_zone);
          }

          $rs->from_zone_name = $zoneName[$rs->from_zone];
          $rs->to_zone_name = $zoneName[$rs->to_zone];
          $totalQty += $rs->qty;
        }
      }
    }
    else
    {
       $details = $this->move_model->get_move_temp($code);

       if( ! empty($details))
       {
         foreach($details as $rs)
         {
           if(empty($zoneName[$rs->zone_code]))
           {
             $zoneName[$rs->zone_code] = $this->zone_model->get_name($rs->zone_code);
           }

           $rs->zone_name = $zoneName[$rs->zone_code];
           $totalQty += $rs->qty;
         }
       }
    }


    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'totalQty' => $totalQty,
      'tab' => $tab
    );

    $this->load->view('mobile/move/move_process', $ds);
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


  public function save()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->move_model->get($code);

      if( ! empty($doc))
      {
        $this->load->model('inventory/movement_model');

        if($doc->status == 'P')
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
                    'warehouse_code' => $doc->warehouse_code,
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
                      'warehouse_code' => $doc->warehouse_code,
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
                }
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'status' => 'C',
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
                $arr = array(
                  'line_status' => 'C',
                  'valid' => 1
                );

                if( ! $this->move_model->update_details($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update item rows status";
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
      set_error('required');
    }


    $this->_response($sc);
  }


  function rollback()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      if($this->pm->can_delete)
      {
        $doc = $this->move_model->get($code);

        if( ! empty($doc))
        {
          $this->load->model('inventory/movement_model');

          if($doc->status != 'P')
          {
            $this->db->trans_begin();

            if($doc->status == 'C')
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

                if($sc === TRUE)
                {
                  if(! empty($details))
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
                    } // end foreach
                  }
                } //-- endif

              } // endif ! empty($details)
            } // end if $doc->status == 'C'


            if($sc === TRUE)
            {
              //--- drop movement
              if( ! $this->movement_model->drop_movement($code))
              {
                $sc = FALSE;
                $this->error = "ลบ Movement ไม่สำเร็จ";
              }
            }

            if( $sc === TRUE)
            {
              $arr = array(
                'status' => 'P',
                'update_user' => $this->_user->uname
              );

              if( ! $this->move_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "ย้อนสถานะเอกสารไม่สำเร็จ";
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'line_status' => 'P',
                'valid' => 0
              );

              if( ! $this->move_model->update_details($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update item rows status";
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
        set_error('permission');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function add_to_temp()
  {
    $sc = TRUE;

    if($this->input->post('move_code'))
    {
      $this->load->model('masters/products_model');

      $temp = NULL;
      $code = $this->input->post('move_code');
      $zone_code = $this->input->post('zone_code');
      $barcode = trim($this->input->post('barcode'));
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);

      if( ! empty($item))
      {
        $product_code = $item->code;
        $stock = $this->stock_model->get_stock_zone($zone_code, $product_code);

        $temp = $this->move_model->get_temp_detail($code, $product_code, $zone_code);

        //--- จำนวนที่อยู่ใน temp
        $temp_qty = empty($temp) ? 0 : $temp->qty;

        //--- จำนวนที่อยู่ใน move_detail และยังไม่ valid
        $move_qty = $this->move_model->get_move_qty($code, $product_code, $zone_code);
        //--- จำนวนที่โอนได้คงเหลือ
        $cqty = $stock - ($temp_qty + $move_qty);

        if($qty <= $cqty)
        {
          //--- เช็คว่า temp จะติดลบหรือไม่ (กรณีที่ส่งยอดติดลบมา)
          if(($qty + $temp_qty) < 0)
          {
            $sc = FALSE;
            $this->error = "จำนวนคงเหลือใน Temp ไม่สามารถติดลบได้";
          }

          if($sc === TRUE)
          {
            if( ! empty($temp))
            {
              if( ! $this->move_model->update_temp_qty($temp->id, $qty))
              {
                $sc = FALSE;
                $this->error = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
              }
              else
              {
                $temp->qty += $qty;
                $temp->product_name = $item->name;
                $temp->zone_name = $this->zone_model->get_name($temp->zone_code);

                if($temp->qty <= 0)
                {
                  $this->move_model->drop_temp($temp->id);
                }
              }
            }
            else
            {
              $arr = array(
                'move_code' => $code,
                'product_code' => $product_code,
                'zone_code' => $zone_code,
                'qty' => $qty
              );

              $id = $this->move_model->add_temp($arr);

              if( ! $id)
              {
                $sc = FALSE;
                $this->error = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
              }
              else
              {
                $arr['id'] = $id;
                $arr['product_name'] = $item->name;
                $arr['zone_name'] = $this->zone_model->get_name($arr['zone_code']);
                $temp = (object) $arr;
              }
            }
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'temp' => $sc === TRUE ? $temp : NULL
    );

    echo json_encode($arr);
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
          $effected_id = []; //-- เก็บ temp id ของรายการที่มีผลเปลียนแปลงจากการย้ายสินค้าออกจาก temp เข้าปลายทาง
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
                  if( ! $this->move_model->update_qty($id, $temp_qty))
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
                if( ! $this->move_model->update_temp_qty($rs->id, ($temp_qty * -1)))
                {
                  $sc = FALSE;
                  $this->error = 'แก้ไขยอดใน temp ไม่สำเร็จ';
                  break;
                }

                if($sc === TRUE)
                {
                  $effected_id[] = ['id' => $rs->id, 'qty' => $temp_qty];
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'effected_ids' => $sc === TRUE ? $effected_id : NULL
    );

    echo json_encode($arr);
  }


  //--- remove from move list and insert to temp
  public function move_to_temp()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $ids = $this->input->post('ids');

    if( ! empty($code) && ! empty($ids))
    {
      $doc = $this->move_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status === 'P')
        {
          $this->db->trans_begin();

          foreach($ids as $id)
          {
            if($sc === FALSE) { break; }

            $row = $this->move_model->get_detail($id);

            if( ! empty($row))
            {
              $temp = $this->move_model->get_temp_detail($code, $row->product_code, $row->from_zone);

              if( ! empty($temp))
              {
                if( ! $this->move_model->update_temp_qty($temp->id, $row->qty))
                {
                  $sc = FALSE;
                  $this->error = "ย้ายสินค้าเข้า Temp ไม่สำเร็จ";
                }
              }
              else
              {
                $arr = array(
                  'move_code' => $code,
                  'product_code' => $row->product_code,
                  'zone_code' => $row->from_zone,
                  'qty' => $row->qty
                );

                if( ! $this->move_model->add_temp($arr))
                {
                  $sc = FALSE;
                  $this->error = "ย้ายสินค้าเข้า Temp ไม่สำเร็จ";
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->move_model->delete_detail($row->id))
                {
                  $sc = FALSE;
                  $this->error = "Failed to delete move item row item : {$row->product_code} From : {$row->from_zone}";
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


  public function get_zone()
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


  public function delete_selected_temp()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $ids = $this->input->post('ids');

    if( ! empty($code) && ! empty($ids))
    {
      $doc = $this->move_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          $this->db->trans_begin();

          if( ! $this->move_model->delete_selected_temp($ids))
          {
            $sc = FALSE;
            $this->error = "Failed to delete selected rows";
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


  public function cancel()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->move_model->get($code);

        if( ! empty($doc))
        {
          $this->load->model('inventory/movement_model');

          $this->db->trans_begin();

          if($doc->status == 'C')
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
              } //- end foreach
            } //-- end if ! empty($details)

            if($sc === TRUE)
            {
              if($doc->status == 'C' && ! empty($details))
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
            }
          } //--- statu == 'C'

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
              'status' => 'D',
              'update_user' => $this->_user->uname,
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
            $arr = array(
              'line_status' => 'D',
              'valid' => 0
            );

            if( ! $this->move_model->update_details($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update item rows status";
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


  public function clear_filter()
  {
    $filter = array(
      'move_code',
      'move_warehouse',
      'move_user',
      'move_fromDate',
      'move_toDate',
      'move_status'
    );

    return clear_filter($filter);
  }

} //--- end class
?>
