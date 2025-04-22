<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adjust extends PS_Controller
{
  public $menu_code = 'ICSTAJ';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
	public $title = 'ปรับปรุงสต็อก';
  public $filter;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/adjust';
    $this->load->model('inventory/adjust_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('inventory/check_stock_diff_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'adj_code', ''),
      'reference' => get_filter('reference', 'adj_reference', ''),
      'user' => get_filter('user', 'adj_user', 'all'),
      'from_date' => get_filter('from_date', 'adj_from_date', ''),
      'to_date' => get_filter('to_date', 'adj_to_date', ''),
      'remark' => get_filter('remark', 'adj_remark', ''),
      'status' => get_filter('status', 'adj_status', 'all'),
      'isApprove' => get_filter('isApprove', 'adj_isApprove', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment  = 4; //-- url segment
		$rows     = $this->adjust_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list   = $this->adjust_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('inventory/adjust/adjust_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('inventory/adjust/adjust_add');
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->date_add))
    {
      $date_add = db_date($ds->date_add, TRUE);
      $code = $this->get_new_code($ds->date_add);

      $ds = array(
        'code' => $code,
        'date_add' => $date_add,
        'reference' => get_null($ds->reference),
        'remark' => get_null($ds->remark),
        'user' => $this->_user->uname
      );

      if( ! $this->adjust_model->add($ds))
      {
        $sc = FALSE;
        $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
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
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $doc = $this->adjust_model->get($code);
    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $this->adjust_model->get($code),
        'details' => $this->adjust_model->get_details($code)
      );

      $this->load->view('inventory/adjust/adjust_edit', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }
  }


  public function add_detail()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->zone_code) && ! empty($ds->product_code))
    {
      $doc = $this->adjust_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          $up_qty = $ds->qty_up;
          $down_qty = $ds->qty_down;
          $qty = $up_qty - $down_qty;

          if($qty != 0)
          {
            //--- ตรวจสอบว่ามีรายการที่เงื่อนไขเดียวกันแล้วยังไม่ได้บันทึกหรือเปล่า
            //--- ถ้ามีรายการอยู่จะได้ ข้อมูล กลับมา
            $detail = $this->adjust_model->get_exists_detail($doc->code, $ds->product_code, $ds->zone_code);

            if( ! empty($detail))
            {
              if($detail->valid == 0)
              {
                $stock = $this->stock_model->get_stock_zone($ds->zone_code, $ds->product_code);
                $new_qty = $stock + ($qty + $detail->qty);

                if($new_qty < 0)
                {
                  $sc = FALSE;
                  $this->error = "ยอดคงเหลือไม่เพียงพอ มีในรายการแล้ว : {$detail->qty}";
                }
                else
                {
                  if(! $this->adjust_model->update_detail_qty($detail->id, $qty))
                  {
                    $sc = FALSE;
                    $this->error = "ปรับปรุงรายการไม่สำเร็จ";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่สามารถปรับปรุงรายการได้เนื่องจากรายการถูกปรับยอดไปแล้ว";
              }
            }
            else
            {
              $item = $this->products_model->get($ds->product_code);

              if( ! empty($item))
              {
                $zone = $this->zone_model->get($ds->zone_code);

                if( ! empty($zone))
                {
                  $stock = $this->stock_model->get_stock_zone($ds->zone_code, $ds->product_code);
                  $new_qty = $stock + $qty;

                  if($new_qty < 0)
                  {
                    $sc = FALSE;
                    $this->error = "ยอดคงเหลือไม่เพียงพอ";
                  }
                  else
                  {
                    $arr = array(
                      'adjust_code' => $ds->code,
                      'warehouse_code' => $zone->warehouse_code,
                      'zone_code' => $zone->code,
                      'product_code' => $item->code,
                      'qty' => $qty
                    );

                    if( ! $this->adjust_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = "เพิ่มรายการไม่สำเร็จ";
                    }
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "รหัสโซนไม่ถูกต้อง";
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "รหัสสินค้าไม่ถูกต้อง";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "จำนวนต้องไม่ถูกต้อง";
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

    if($sc === TRUE)
    {
      $rs = $this->adjust_model->get_exists_detail($ds->code, $ds->product_code, $ds->zone_code);

      if( ! empty($rs))
      {
        $row = (object) array(
          'id' => $rs->id,
          'pdCode' => $rs->product_code,
          'pdName' => $rs->product_name,
          'zoneCode' => $rs->zone_code,
          'zoneName' => $rs->zone_name,
          'up' => round(($rs->qty > 0 ? $rs->qty : 0)),
          'down' => ($rs->qty < 0 ? ($rs->qty * -1) : 0),
          'valid' => $rs->valid
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "การบันทึกข้อมูลผิดพลาด";
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'row' => $sc === TRUE ? $row : NULL
    );

    echo json_encode($arr);
  }


  //---- update doc header
  public function update()
  {
    $sc = TRUE;
    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $reference = get_null($this->input->post('reference'));
      $remark = get_null($this->input->post('remark'));

      $doc = $this->adjust_model->get($code);
      if( ! empty($doc))
      {
        $arr = array(
          'reference' => $reference,
          'remark' => $remark
        );

        //---- ถ้าบันทึกแล้ว จะไม่สามารถเปลี่ยนแปลงวันที่ได้
        //--- เนื่องจากมีการบันทึก movement ไปแล้วตามวันที่เอกสาร
        if($doc->status == 0)
        {
          $arr['date_add'] = $date_add;
        }

        if(! $this->adjust_model->update($code, $arr))
        {
          $sc = FALSE;
          $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function delete_detail()
  {
    $sc = TRUE;
    if($this->pm->can_edit)
    {
      $id = $this->input->post('id');
      if( ! empty($id))
      {
        $detail = $this->adjust_model->get_detail($id);

        if( ! empty($detail))
        {
          $doc = $this->adjust_model->get($detail->adjust_code);

          if( ! empty($doc))
          {
            if($doc->status == 0)
            {
              if( ! $this->adjust_model->delete_detail($id))
              {
                $sc = FALSE;
                $this->error = "ลบรายการไม่สำเร็จ";
              }
              else
              {
                if($detail->id_diff)
                {
                  $this->check_stock_diff_model->update($detail->id_diff, array('status' => 0));
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "เอกสารถูกบันทึกไปแล้ว ไม่สามารถแก้ไขรายการได้";
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
          $this->error = "ไม่พบรายการที่ต้องการลบ หรือ รายการถูกลบไปแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบ ID";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ในการแก้ไขรายการ";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  ///----- Just change status to 0
  public function unsave()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 1)
        {
          $details = $this->adjust_model->get_details($code);

          if( ! empty($details))
          {
            $status = 0; //--- 0 = not save, 1 = saved, 2 = cancled

            if( ! $this->adjust_model->change_status($code, $status))
            {
              $sc = FALSE;
              $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการปรับยอดกรุณาตรวจสอบ";
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    $this->_response($sc);
  }



  //---- Just change status to 1
  public function save()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');

      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          $details = $this->adjust_model->get_details($code);
          if( ! empty($details))
          {
            $status = 1; //--- 0 = not save, 1 = saved, 2 = cancled
            if( ! $this->adjust_model->change_status($code, $status))
            {
              $sc = FALSE;
              $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการปรับยอดกรุณาตรวจสอบ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกบันทึกไปแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    $this->_response($sc);
  }


  public function do_approve()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $this->load->model('approve_logs_model');

      $code = $this->input->post('code');

      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 1)
        {
          $this->db->trans_begin();

          $details = $this->adjust_model->get_details($code);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              if($rs->valid == 0)
              {
                if($rs->qty < 0)
                {
                  $stock = $this->stock_model->get_stock_zone($rs->zone_code, $rs->product_code);
                  $newQty = $stock + $rs->qty;

                  if($newQty < 0)
                  {
                    $sc = FALSE;
                    $this->error = "สต็อกคงเหลือไม่เพียงพอ <br/>โซน : {$rs->zone_code}<br/>SKU : {$rs->product_code}<br/>Qty : {$rs->qty}";
                  }
                }

                if($sc === TRUE)
                {
                  if( ! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, $rs->qty))
                  {
                    $sc = FALSE;
                    $this->error = "ปรับปรุงสต็อกไม่สำเร็จ <br/>โซน : {$rs->zone_code}<br/>SKU : {$rs->product_code}<br/>Qty : {$rs->qty}";
                  }
                }

                if($sc === TRUE)
                {
                  $move_in = $rs->qty > 0 ? $rs->qty : 0;
                  $move_out = $rs->qty < 0 ? ($rs->qty * -1) : 0;

                  $arr = array(
                    'reference' => $rs->adjust_code,
                    'warehouse_code' => $rs->warehouse_code,
                    'zone_code' => $rs->zone_code,
                    'product_code' => $rs->product_code,
                    'move_in' => $move_in,
                    'move_out' => $move_out,
                    'date_add' => $doc->date_add
                  );

                  if(! $this->movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "บันทึก movement ไม่สำเร็จ <br/>โซน : {$rs->zone_code}<br/>SKU : {$rs->product_code}<br/>Qty : {$rs->qty}";
                  }
                }

                if($sc === TRUE)
                {
                  if(! $this->adjust_model->valid_detail($rs->id))
                  {
                    $sc = FALSE;
                    $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ <br/>โซน : {$rs->zone_code}<br/>SKU : {$rs->product_code}<br/>Qty : {$rs->qty}";
                  }
                }

                if($sc === TRUE)
                {
                  if( ! empty($rs->id_diff))
                  {
                    $this->check_stock_diff_model->update($rs->id_diff, array('status' => 2));
                  }
                }
              }
            } ///--- end foreach
          }

          //--- do approve
          if($sc === TRUE)
          {
            if( ! $this->adjust_model->do_approve($code, $this->_user->uname))
            {
              $sc = FALSE;
              $this->error = "อนุมัติเอกสารไม่สำเร็จ";
            }

            //--- write approve logs
            if($sc === TRUE)
            {
              $this->approve_logs_model->add($code, 1, $this->_user->uname);
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
          $this->error = "เอกสารยังไม่ถูกบันทึก";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function un_approve()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $sc = $this->unapprove($code);
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function unapprove($code)
  {
    $sc = TRUE;

    $this->load->model('approve_logs_model');

    $doc = $this->adjust_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 1)
      {
        $details = $this->adjust_model->get_details($code);

        if( ! empty($details))
        {
          //--- check validate for stock
          foreach($details as $rs)
          {
            if($sc === FALSE) { break; }

            if($rs->qty > 0)
            {
              $stock = $this->stock_model->get_stock_zone($rs->zone_code, $rs->product_code);
              $newQty = $stock + ($rs->qty * -1);

              if($newQty < 0)
              {
                $sc = FALSE;
                $this->error = "สต็อกคงเหลือไม่เพียงพอให้ย้อนสถานะ <br/>โซน : {$rs->zone_code}<br/>SKU : {$rs->product_code}<br/>Qty : {$rs->qty}";
              }
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

              if( ! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, ($rs->qty * -1)))
              {
                $sc = FALSE;
                $this->error = "Failed to update stock : <br/>Zone : {$rs->zone_code}<br/>SKU : {$rs->product_code}<br/>Qty : {$rs->qty}";
              }

              if($sc === TRUE && ! empty($rs->id_diff))
              {
                if( ! $this->check_stock_diff_model->update($rs->id_diff, array('status'=> 1)))
                {
                  $sc = FALSE;
                  $this->error = "Failed to rollback check stock diff";
                }
              }
            }
          }

          //-- 1. drop movements
          if($sc === TRUE)
          {
            if( ! $this->movement_model->drop_movement($code))
            {
              $sc = FALSE;
              $this->error = "ลบ movement ไม่สำเร็จ";
            }
          }

          //--- 2. change details valid to 0
          if($sc === TRUE)
          {
            if( ! $this->adjust_model->unvalid_details($code))
            {
              $sc = FALSE;
              $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
            }
          }

          //--- 3. un_approve
          if($sc === TRUE)
          {
            if( ! $this->adjust_model->un_approve($code, $this->_user->uname))
            {
              $sc = FALSE;
              $this->error = "ยกเลิกการอนุมัติไม่สำเร็จ";
            }
          }

          //--- 4. write approve logs
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

    return $sc;
  }


  public function view_detail($code, $approve_view = NULL)
  {
    $this->load->model('approve_logs_model');

    $doc = $this->adjust_model->get($code);

    if( ! empty($doc))
    {
      $doc->user_name = $this->user_model->get_name($doc->user);
      $ds = array(
        'doc' => $doc,
        'details' => $this->adjust_model->get_details($code),
        'approve_view' => $approve_view,
        'approve_list' => $this->approve_logs_model->get($code)
      );

      $this->load->view('inventory/adjust/adjust_detail', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }

  }


  public function cancle($code)
  {
    $sc = TRUE;

    if( ! empty($code))
    {
      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->is_approved == 1)
        {
          if(! $this->unapprove($code))
          {
            $sc = FALSE;
          }
        }

        //---- ถ้าสามารถยกเลิกการอนุมัติได้ หรือ หากยังไม่ได้อนุมัติ
        //---- set is_cancle  = 1 in adjust_detail
        //---- change status = 2 in adjust
        if($sc === TRUE)
        {
          $this->db->trans_begin();
          //---- set is_cancle = 1 in adjust_detail
          if(! $this->adjust_model->cancle_details($code))
          {
            $sc = FALSE;
            $this->error = "ยกเลิกรายการไม่สำเร็จ";
          }

          //--- change doc status to 2 Cancled
          if($sc === TRUE)
          {
            $arr = array(
              'status' => 2,
              'cancle_reason' => trim($this->input->post('reason')),
              'cancle_user' => $this->_user->uname
            );

            if(! $this->adjust_model->update($code, $arr))
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
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เลขที่เอกสารไม่ถูกต้อง";
    }

    $this->_response($sc);
  }


  public function load_check_diff($code)
  {
    $sc = TRUE;
    $list = $this->input->post('diff');
    if( ! empty($list))
    {
      $this->db->trans_begin();
      //---- add diff list to adjust
      foreach($list as $id => $val)
      {
        $diff = $this->check_stock_diff_model->get($id);
        if( ! empty($diff))
        {
          if($sc === FALSE)
          {
            break;
          }

          if($diff->status == 0)
          {
            $zone = $this->zone_model->get($diff->zone_code);
            if( ! empty($zone))
            {
              $arr = array(
                'adjust_code' => $code,
                'warehouse_code' => $zone->warehouse_code,
                'zone_code' => $zone->code,
                'product_code' => $diff->product_code,
                'qty' => $diff->qty,
                'id_diff' => $diff->id
              );

              $adjust_id = $this->adjust_model->get_not_save_detail($code, $diff->product_code, $diff->zone_code);
              if( ! empty($adjust_id))
              {
                if(! $this->adjust_model->update_detail($adjust_id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Update Failed : {$diff->product_code} : {$diff->zone_code}";
                }
              }
              else
              {
                if(! $this->adjust_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "Add detail failed : {$diff->product_code} : {$diff->zone_code}";
                }
              }

              if($sc === TRUE)
              {
                $this->check_stock_diff_model->update($diff->id, array('status' => 1));
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "โซนไม่ถูกต้อง";
            }
          }
        }

      } //--- endforeach;

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
      $this->error = "ไม่พบรายการยอดต่าง";
    }

    if($sc === TRUE)
    {
      set_message('Loaded');
    }
    else
    {
      set_error($this->error);
    }

    redirect("{$this->home}/edit/{$code}");
  }


  public function get_stock_zone()
  {
    $zone_code = $this->input->get('zone_code');
    $product_code = $this->input->get('product_code');
    $stock = $this->stock_model->get_stock_zone($zone_code, $product_code);

    echo get_zero($stock);
  }


  public function get_new_code($date = '')
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ADJUST');
    $run_digit = getConfig('RUN_DIGIT_ADJUST');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->adjust_model->get_max_code($pre);
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
      'adj_code',
      'adj_reference',
      'adj_user',
      'adj_from_date',
      'adj_to_date',
      'adj_remark',
      'adj_status',
      'adj_isApprove',
      'adj_sap'
    );

    clear_filter($filter);

    echo 'done';
  }

} //---- End class
?>
