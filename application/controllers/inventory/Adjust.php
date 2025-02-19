<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adjust extends PS_Controller
{
  public $menu_code = 'ICSTAJ';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
	public $title = 'ปรับปรุงสต็อก';
  public $filter;
  public $segment = 4;

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
    $this->load->model('document_logs_model');
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'adj_code', ''),
      'reference'  => get_filter('reference', 'adj_reference', ''),
      'from_date' => get_filter('from_date', 'adj_from_date', ''),
      'to_date'   => get_filter('to_date', 'adj_to_date', ''),
      'user'      => get_filter('user', 'adj_user', 'all'),
      'warehouse' => get_filter('warehouse', 'adj_warehouse', 'all'),
      'status' => get_filter('status', 'adj_status', 'all'),
      'approve' => get_filter('approve', 'adj_approve', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();
      $rows = $this->adjust_model->count_rows($filter);
      $filter['list'] = $this->adjust_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('inventory/adjust/adjust_list', $filter);
    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('inventory/adjust/adjust_add');
    }
    else
    {
      $this->deny_page();
    }
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->date_add) && ! empty($ds->warehouse_id))
    {
      if($this->pm->can_add)
      {
        $doc_date = db_date($ds->date_add);
        $posting_date = empty($ds->posting_date) ? NULL : db_date($ds->posting_date);
        $code = $this->get_new_code($doc_date);


        $arr = array(
          'code' => $code,
          'warehouse_id' => $ds->warehouse_id,
          'reference' => get_null(trim($ds->reference)),
          'doc_date' => $doc_date,
          'posting_date' => $posting_date,
          'user' => $this->_user->uname,
          'remark' => get_null(trim($ds->remark))
        );

        if( ! $this->adjust_model->add($arr))
        {
          $sc = FALSE;
          set_error('insert');
        }

        if($sc === TRUE)
        {
          $logs = array(
            'type' => 'ADJ',
            'code' => $code,
            'action' => 'create',
            'user' => $this->_user->uname
          );

          $this->document_logs_model->add_logs($logs);
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

    $this->_json_response($sc, $code);
  }


  public function edit($code)
  {
    if($this->pm->can_edit OR $this->pm->can_add)
    {
      $doc = $this->adjust_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 1 OR $doc->status == 2)
        {
          $this->page_error();
        }
        else
        {
          $ds = array(
            'doc' => $this->adjust_model->get($code),
            'details' => $this->adjust_model->get_details($doc->id)
          );

          $this->load->view('inventory/adjust/adjust_edit', $ds);
        }
      }
      else
      {
        $this->page_error();
      }
    }
    else
    {
      $this->deny_page();
    }
  }


  public function view_detail($code)
  {
    $doc = $this->adjust_model->get($code);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $this->adjust_model->get($code),
        'details' => $this->adjust_model->get_details($doc->id),
        'logs' => $this->document_logs_model->get_logs($doc->code)
      );

      $this->load->view('inventory/adjust/adjust_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function get_stock_zone()
  {
    $sc = TRUE;
    $zone_id = $this->input->get('zone_id');
    $product_code = $this->input->get('product_code');
    $product_id = $this->input->get('product_id');
    $stock = $this->stock_model->get_stock_zone($zone_id, $product_id);
    echo $stock;
  }


  public function add_detail()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->product_code))
    {
      $qty = $ds->qty_up - $ds->qty_down;

      if($qty != 0)
      {
        $doc = $this->adjust_model->get($ds->code);

        if(! empty($doc) && $doc->status < 1)
        {
          //--- ตรวจสอบรหัสสินค้า
          $item = $this->products_model->get($ds->product_id);

          if( ! empty($item))
          {
            //--- ตรวจสอบรหัสโซน
            $zone = $this->zone_model->get($ds->zone_id);

            if( ! empty($zone))
            {
              //--- ตรวจสอบว่ามีรายการที่เงื่อนไขเดียวกันแล้วยังไม่ได้บันทึกหรือเปล่า
              //--- ถ้ามีรายการอยู่จะได้ ข้อมูล กลับมา
              $detail = $this->adjust_model->get_exists_detail($doc->id, $ds->product_id, $ds->zone_id);

              if( ! empty($detail))
              {
                if($detail->valid == 0)
                {
                  //---- ถ้ามีรายการอยู่แล้ว ทำการ update
                  $qty = $ds->qty_up - $ds->qty_down;
                  $stock = $this->stock_model->get_stock_zone($ds->zone_id, $ds->product_id);
                  $new_qty = $stock + ($qty + $detail->qty);

                  if($new_qty < 0)
                  {
                    $sc = FALSE;
                    $this->error = "ยอดคงเหลือไม่เพียงพอ มีในรายการแล้ว : {$detail->qty}";
                  }
                  else
                  {
                    if(! $this->adjust_model->update_qty($detail->id, $qty))
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
                //---- ถ้ายังไม่มีรายการ เพิ่มใหม่
                $arr = array(
                  'adjust_id' => $doc->id,
                  'adjust_code' => $doc->code,
                  'warehouse_id' => $zone->warehouse_id,
                  'zone_id' => $zone->id,
                  'zone_code' => $zone->code,
                  'product_id' => $item->id,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'qty' => $qty
                );

                if( ! $this->adjust_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "เพิ่มรายการไม่สำเร็จ";
                }
              }

              if($sc === TRUE && $doc->status == 0)
              {
                $this->adjust_model->update($doc->id, ['status' => -1]);
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
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารไม่ถูกต้อง หรือ สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "จำนวนต้องมากกว่า 1";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $res = [];

    if($sc === TRUE)
    {
      $rs = $this->adjust_model->get_exists_detail($doc->id, $ds->product_id, $ds->zone_id);

      if( ! empty($rs))
      {
        $res = array(
          'id' => $rs->id,
          'product_id' => $rs->product_id,
          'product_code' => $rs->product_code,
          'product_name' => $rs->product_name,
          'zone_code' => $zone->code,
          'zone_name' => $zone->name,
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

    $this->_json_response($sc, $res);
  }


  //---- update doc header
  public function update()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if($this->pm->can_edit)
    {
      if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->code) && ! empty($ds->warehouse_id))
      {
        $doc = $this->adjust_model->get($ds->code);

        if( ! empty($doc))
        {
          if($doc->status < 1)
          {
            $posting_date = empty($ds->posting_date) ? NULL : db_date($ds->posting_date);

            $arr = array(
              'doc_date' => db_date($ds->date_add),
              'posting_date' => $posting_date,
              'reference' => get_null(trim($ds->reference)),
              'warehouse_id' => $ds->warehouse_id,
              'remark' => get_null(trim($ds->remark)),
              'date_upd' => now(),
              'update_user' => $this->_user->uname
            );

            $this->db->trans_begin();

            if($doc->warehouse_id != $ds->warehouse_id)
            {
              if( ! $this->adjust_model->delete_details($doc->id))
              {
                $sc = FALSE;
                $this->error = "Failed to remove previous item";
              }
            }

            if($sc === TRUE)
            {
              if( ! $this->adjust_model->update($doc->id, $arr))
              {
                $sc = FALSE;
                $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
              }
            }

            if($sc === TRUE)
            {
              $logs = array(
                'type' => 'ADJ',
                'code' => $ds->code,
                'action' => 'update',
                'user' => $this->_user->uname
              );

              $this->document_logs_model->add_logs($logs);
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
            $this->error = "สถานะเอกสารไม่ถูกต้อง";
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
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
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
            if($doc->status < 1)
            {
              if( ! $this->adjust_model->delete_detail($id))
              {
                $sc = FALSE;
                set_error('delete');
              }

              if($sc === TRUE && $doc->status == 0)
              {
                $this->adjust_model->update($doc->id, ['status' => -1]);
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


  //---- Just change status to 0
  public function save()
  {
    $sc = TRUE;

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        $doc = $this->adjust_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status < 1)
          {
            $details = $this->adjust_model->get_details($doc->id);

            if( ! empty($details))
            {
              $arr = array(
                'status' => 0,
                'approve' => 'P',
                'date_upd' => now(),
                'update_user' => $this->_user->uname
              );

              if( ! $this->adjust_model->update($doc->id, $arr))
              {
                $sc = FALSE;
                $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
              }

              if($sc === TRUE)
              {
                $logs = array(
                  'type' => 'ADJ',
                  'code' => $code,
                  'action' => 'update',
                  'user' => $this->_user->uname
                );

                $this->document_logs_model->add_logs($logs);
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


  public function do_reject()
  {
    $sc = TRUE;

    if($this->pm->can_approve)
    {
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        $doc = $this->adjust_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 0 && $doc->approve == 'P')
          {
            if( ! $this->adjust_model->update($doc->id, ['approve' => 'R']))
            {
              $sc = FALSE;
              $this->error = "ปฏิเสธเอกสารไม่สำเร็จ";
            }

            if($sc === TRUE)
            {
              $logs = array(
                'type' => 'ADJ',
                'code' => $code,
                'action' => 'reject',
                'user' => $this->_user->uname
              );

              $this->document_logs_model->add_logs($logs);
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
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function do_approve()
  {
    $sc = TRUE;

    if($this->pm->can_approve)
    {
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        $doc = $this->adjust_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 0)
          {
            $this->db->trans_begin();

            $details = $this->adjust_model->get_details($doc->id);

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
                  //--- check stock
                  if($rs->qty < 0)
                  {
                    $qty = $rs->qty * -1;

                    $stock = $this->stock_model->get_stock_zone($rs->zone_id, $rs->product_id);

                    if($qty > $stock)
                    {
                      $sc = FALSE;
                      $this->error = "สต็อกคงเหลือไม่เพียงพอ  {$rs->product_code} : {$rs->zone_code}";
                    }
                  }

                  if($sc === TRUE)
                  {
                    //--- update stock
                    if( ! $this->stock_model->update_stock_zone($rs->product_id,$rs->product_code, $rs->zone_id, $rs->warehouse_id, $rs->qty))
                    {
                      $sc = FALSE;
                      $this->error = "ปรับยอดสต็อกคงเหลือไม่สำเร็จ {$rs->product_code} : {$rs->zone_code}";
                    }
                  }

                  if($sc === TRUE)
                  {
                    //--- 1. update movement
                    $move_in = $rs->qty > 0 ? $rs->qty : 0;
                    $move_out = $rs->qty < 0 ? ($rs->qty * -1) : 0;

                    $arr = array(
                      'reference' => $rs->adjust_code,
                      'product_id' => $rs->product_id,
                      'product_code' => $rs->product_code,
                      'zone_id' => $rs->zone_id,
                      'zone_code' => $rs->zone_code,
                      'warehouse_id' => $rs->warehouse_id,
                      'move_in' => $move_in,
                      'move_out' => $move_out,
                      'date_add' => empty($doc->posting_date) ? now() : db_date($doc->posting_date, TRUE)
                    );

                    if( ! $this->movement_model->add($arr))
                    {
                      $sc = FALSE;
                      $this->error = 'บันทึก movement ไม่สำเร็จ';
                      break;
                    }

                    //--- 2 ปรับรายการเป็น บันทึกรายการแล้ว (valid = 1)
                    if( ! $this->adjust_model->valid_detail($rs->id))
                    {
                      $sc = FALSE;
                      $this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
                      break;
                    }
                  }
                }
              } ///--- end foreach
            }

            //--- do approve
            if($sc === TRUE)
            {
              $arr = array(
                'status' => 1,
                'posting_date' => empty($doc->posting_date) ? date('Y-m-d') : $doc->posting_date,
                'approve' => 'A',
                'date_upd' => now(),
                'update_user' => $this->_user->uname
              );

              if( ! $this->adjust_model->update($doc->id, $arr))
              {
                $sc = FALSE;
                $this->error = "อนุมัติเอกสารไม่สำเร็จ";
              }

              //--- write approve logs
              if($sc === TRUE)
              {
                $logs = array(
                  'type' => 'ADJ',
                  'code' => $code,
                  'action' => 'approve',
                  'user' => $this->_user->uname
                );

                $this->document_logs_model->add_logs($logs);
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
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }

  //---- ถ้าเอสารยังไม่อนุมัติ ยกเลิกได้เลย
  public function cancel()
  {
    $sc = TRUE;

    $code = trim($this->input->post('code'));
    $reason = trim($this->input->post('reason'));

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->adjust_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 2 && $doc->status != 9 )
          {
            $this->db->trans_begin();

            //---- when document already complete must create new document to reverse operation
            if($doc->status == 1 && $doc->approve == 'A')
            {
              $details = $this->adjust_model->get_details($doc->id);

              if( ! empty($details))
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  //--- check available stock
                  if($rs->qty > 0)
                  {
                    $stock = $this->stock_model->get_stock_zone($rs->zone_id, $rs->product_id);

                    //--- ถ้าสต็อกไม่พอ
                    if($stock < $rs->qty)
                    {
                      $sc = FALSE;
                      $this->error = "สต็อกคงเหลือไม่เพียงพอ - {$rs->product_code} : {$rs->zone_code}";
                    }
                  }
                } //-- end foreach

                //--- ถ้าสต็อกมีพอ
                if($sc === TRUE)
                {
                  foreach($details as $rs)
                  {
                    if($sc === FALSE)
                    {
                      break;
                    }

                    $qty = $rs->qty * -1; //--- กลับด้านยอดเพือ่ไปบวกกลับในสต็อก

                    if( ! $this->stock_model->update_stock_zone($rs->product_id, $rs->product_code, $rs->zone_id, $rs->warehouse_id, $qty))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update stock qty";
                    }
                  }
                }

                if($sc === TRUE)
                {
                  if( ! $this->movement_model->drop_movement($doc->code))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to remove stock movement";
                  }
                }
              } //--- ! empty details
            }

            if($sc === TRUE)
            {
              if( ! $this->adjust_model->cancel_details($doc->id))
              {
                $sc = FALSE;
                $this->error = "Failed to cancel rows items";
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'status' => 2,
                  'date_upd' => now(),
                  'update_user' => $this->_user->uname,
                  'cancel_reason' => get_null($reason),
                  'cancel_user' => $this->_user->uname
                );

                if( ! $this->adjust_model->update($doc->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to change document status";
                }
              }
            }


            if($sc === TRUE)
            {
              $this->db->trans_commit();

              $logs = array(
                'type' => 'ADJ',
                'code' => $code,
                'action' => 'cancel',
                'user' => $this->_user->uname
              );

              $this->document_logs_model->add_logs($logs);
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


  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = 'AJ';
    $run_digit = 4;
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
      'adj_warehouse',
      'adj_from_date',
      'adj_to_date',
      'adj_remark',
      'adj_status',
      'adj_approve'
    );

    return clear_filter($filter);
  }

} //---- End class
?>
