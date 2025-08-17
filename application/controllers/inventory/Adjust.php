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
    $this->load->helper('warehouse');
    $this->load->helper('adjust');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'adj_code', ''),
      'DocNum' => get_filter('DocNum', 'adj_docNum', ''),
      'reference' => get_filter('reference', 'adj_reference', ''),
      'warehouse_code' => get_filter('warehouse_code', 'adj_warehouse_code', 'all'),
      'user' => get_filter('user', 'adj_user', 'all'),
      'from_date' => get_filter('from_date', 'adj_from_date', ''),
      'to_date' => get_filter('to_date', 'adj_to_date', ''),
      'remark' => get_filter('remark', 'adj_remark', ''),
      'status' => get_filter('status', 'adj_status', 'all')
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
        'warehouse_code' => $ds->warehouse_code,
        'reference' => get_null($ds->reference),
        'remark' => get_null($ds->remark),
        'user' => $this->_user->uname
      );

      if( ! $this->adjust_model->add($ds))
      {
        $sc = FALSE;
        $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
      }

      if($sc === TRUE)
      {
        $logs = array(
          'code' => $code,
          'action' => 'add',
          'user' => $this->_user->uname
        );

        $this->adjust_model->add_logs($logs);
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
        if($doc->status === 'P' OR $doc->status === 'A' OR $doc->status == 'R')
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
              if($detail->line_status === 'O')
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
                      'adjust_id' => $doc->id,
                      'adjust_code' => $doc->code,
                      'warehouse_code' => $zone->warehouse_code,
                      'zone_code' => $zone->code,
                      'product_code' => $item->code,
                      'product_name' => $item->name,
                      'unit_code' => $item->unit_code,
                      'qty' => $qty,
                      'user' => $this->_user->uname
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

          if($sc === TRUE)
          {
            if($doc->status === 'A' OR $doc->status == 'R')
            {
              $arr = array(
                'status' => 'P',
                'update_user' => $this->_user->uname,
                'date_upd' => now()
              );

              $this->adjust_model->update($doc->code, $arr);

              $logs = array(
                'code' => $doc->code,
                'action' => 'edit',
                'user' => $this->_user->uname
              );

              $this->adjust_model->add_logs($logs);
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
          'up' => round(($rs->qty > 0 ? $rs->qty : 0)),
          'down' => ($rs->qty < 0 ? ($rs->qty * -1) : 0),
          'line_status' => $rs->line_status == 'D' ? 'Canceled' : ($rs->line_status == 'C' ? 'Closed' : 'Open')
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


  public function remove_selected_details()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $ids = json_decode($this->input->post('ids'));

    if( ! empty($code) && ! empty($ids))
    {
      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P' OR $doc->status == 'A' OR $doc->status == 'R')
        {
          if( ! $this->adjust_model->delete_details_by_ids($ids))
          {
            $sc = FALSE;
            $this->error = "Failed to remove item rows";
          }

          if($sc === TRUE)
          {
            if($doc->status == 'A' OR $doc->status == 'R')
            {
              $arr = array(
                'status' => 'P',
                'update_user' => $this->_user->uname,
                'date_upd' => now()
              );

              $this->adjust_model->update($doc->code, $arr);

              $logs = array(
                'code' => $doc->code,
                'action' => 'edit',
                'user' => $this->_user->uname
              );

              $this->adjust_model->add_logs($logs);
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


  public function save()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');

      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status === 'P' OR $doc->status == 'A' OR $doc->status == 'R')
        {
          $arr = array(
            'status' => 'A',
            'is_approved' => 0,
            'update_user' => $this->_user->uname,
            'date_upd' => now()
          );

          if( ! $this->adjust_model->update($doc->code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document status";
          }

          if($sc === TRUE)
          {
            $logs = array(
              'code' => $doc->code,
              'action' => 'save',
              'user' => $this->_user->uname
            );

            $this->adjust_model->add_logs($logs);
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


  public function do_reject()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'A')
        {
          $arr = array(
            'status' => 'R',
            'update_user' => $this->_user->uname,
            'date_upd' => now()
          );

          if( ! $this->adjust_model->update($doc->code, $arr))
          {
            $sc = FALSE;
            set_error('update');
          }

          if($sc === TRUE)
          {
            $logs = array(
              'code' => $doc->code,
              'action' => 'reject',
              'user' => $this->_user->uname
            );

            $this->adjust_model->add_logs($logs);
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


  public function do_approve()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'A')
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

              if($rs->line_status == 'O')
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
                  $arr = array(
                    'line_status' => 'C',
                    'date_upd' => now(),
                    'update_user' => $this->_user->uname
                  );

                  if(! $this->adjust_model->update_detail($rs->id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ <br/>โซน : {$rs->zone_code}<br/>SKU : {$rs->product_code}<br/>Qty : {$rs->qty}";
                  }
                }
              }
            } ///--- end foreach
          }

          //--- do approve
          if($sc === TRUE)
          {
            $arr = array(
              'status' => 'C',
              'is_approved' => 1,
              'update_user' => $this->_user->uname,
              'date_upd' => now()
            );

            if( ! $this->adjust_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "อนุมัติเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $logs = array(
                'code' => $doc->code,
                'action' => 'approve',
                'user' => $this->_user->uname
              );

              $this->adjust_model->add_logs($logs);
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

          if($sc === TRUE && is_true(getConfig('WRX_ADJUST_API')))
          {
            $this->load->library('wrx_adjust_api');

            if( ! $this->wrx_adjust_api->export_adjust($doc->code))
            {
              $sc = FALSE;
              $this->error = "บันทีกสำเร็จ แต่ส่งข้อมูลไป ERP ไม่สำเร็จ : {$this->wrx_adjust_api->error}";
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


  public function view_detail($code, $approve_view = NULL)
  {
    $doc = $this->adjust_model->get($code);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->adjust_model->get_details($code),
        'logs' => $this->adjust_model->get_logs($code)
      );

      $this->load->view('inventory/adjust/adjust_detail', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }
  }


  public function cancel($code)
  {
    $sc = TRUE;

    if( ! empty($code))
    {
      $reason = get_null($this->input->post('reason'));
      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status != 'D')
        {
          if($doc->status == 'C')
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
                $arr = array(
                  'line_status' => 'D',
                  'date_upd' => now(),
                  'update_user' => $this->_user->uname
                );

                if( ! $this->adjust_model->update_details($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
                }
              }

              //--- 3. cancel doc
              if($sc === TRUE)
              {
                $arr = array(
                  'status' => 'D',
                  'is_approved' => 0,
                  'update_user' => $this->_user->uname,
                  'date_upd' => now(),
                  'cancel_reason' => $reason
                );

                if( ! $this->adjust_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "บันทึกสถานะเอกสารไม่สำเร็จ";
                }
              }

              //--- 4. write approve logs
              if($sc === TRUE)
              {
                $logs = array(
                  'code' => $doc->code,
                  'action' => 'cancel',
                  'user' => $this->_user->uname
                );

                $this->adjust_model->add_logs($logs);
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


  public function send_to_erp()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      if(is_true(getConfig('WRX_ADJ_INTERFACE')))
      {
        $doc = $this->adjust_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 'C')
          {
            $this->load->library('wrx_adjust_api');

            if( ! $this->wrx_adjust_api->export_adjust($doc->code))
            {
              $sc = FALSE;
              $this->error = "ส่งข้อมูลไป ERP ไม่สำเร็จ : {$this->wrx_adjust_api->error}";
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
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
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
      'adj_warehouse_code',
      'adj_docNum'
    );

    return clear_filter($filter);
  }

} //---- End class
?>
