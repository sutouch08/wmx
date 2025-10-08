<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_po extends PS_Controller
{
  public $menu_code = 'ICPURC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title = 'Goods Receipt PO';
  public $filter;
  public $error;
  public $required_remark = 0;
  public $is_mobile = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'mobile/receive_po';
    $this->load->model('inventory/receive_po_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('purchase/po_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/warehouse_model');
    $this->load->helper('warehouse');
    $this->load->helper('receive_po');
  }


  public function index()
  {
    $this->title = "Goods Receipt PO - Pending List";

    $filter = array(
      'code' => get_filter('code', 'receive_code', ''),
      'invoice' => get_filter('invoice', 'receive_invoice', ''),
      'po' => get_filter('po', 'receive_po', ''),
      'vender' => get_filter('vender', 'receive_vender', ''),
      'user' => get_filter('user', 'receive_user', 'all'),
      'from_date' => get_filter('from_date', 'receive_from_date', ''),
      'to_date' => get_filter('to_date', 'receive_to_date', ''),
      'warehouse' => get_filter('warehouse', 'receive_warehouse', 'all'),
      'status' => 'O',
      'tab' => 'pending'
    );

    //--- แสดงผลกี่รายการต่อหน้า
    $perpage = get_rows();

    $segment  = 4; //-- url segment
    $rows = $this->receive_po_model->count_rows($filter);
    $init = mobile_pagination_config($this->home.'/index/', $rows, $perpage, $segment);
    $filter['data'] = $this->receive_po_model->get_list($filter, $perpage, $this->uri->segment($segment));
    $this->pagination->initialize($init);
    $this->load->view('mobile/receive_po/pending_list', $filter);
  }


  public function all_list()
  {
    $this->title = "Goods Receipt PO - All List";

    $filter = array(
      'code' => get_filter('code', 'receive_code', ''),
      'invoice' => get_filter('invoice', 'receive_invoice', ''),
      'po' => get_filter('po', 'receive_po', ''),
      'vender' => get_filter('vender', 'receive_vender', ''),
      'user' => get_filter('user', 'receive_user', 'all'),
      'from_date' => get_filter('from_date', 'receive_from_date', ''),
      'to_date' => get_filter('to_date', 'receive_to_date', ''),
      'warehouse' => get_filter('warehouse', 'receive_warehouse', 'all'),
      'status' => get_filter('status', 'receive_status', 'all'),
      'tab' => 'all'
    );

    //--- แสดงผลกี่รายการต่อหน้า
    $perpage = get_rows();

    $segment  = 4; //-- url segment
    $rows = $this->receive_po_model->count_rows($filter);
    $init = mobile_pagination_config($this->home.'/index/', $rows, $perpage, $segment);
    $filter['data'] = $this->receive_po_model->get_list($filter, $perpage, $this->uri->segment($segment));
    $this->pagination->initialize($init);
    $this->load->view('mobile/receive_po/all_list', $filter);
  }


  public function process_list()
  {
    $this->title = "Goods Receipt PO - Receiving List";
    $filter = array(
      'code' => get_filter('code', 'receive_code', ''),
      'invoice' => get_filter('invoice', 'receive_invoice', ''),
      'po' => get_filter('po', 'receive_po', ''),
      'vender' => get_filter('vender', 'receive_vender', ''),
      'user' => get_filter('user', 'receive_user', 'all'),
      'from_date' => get_filter('from_date', 'receive_from_date', ''),
      'to_date' => get_filter('to_date', 'receive_to_date', ''),
      'warehouse' => get_filter('warehouse', 'receive_warehouse', 'all'),
      'status' => 'R',
      'tab' => 'process'
    );

    //--- แสดงผลกี่รายการต่อหน้า
    $perpage = get_rows();

    $segment  = 4; //-- url segment
    $rows = $this->receive_po_model->count_rows($filter);
    $init = mobile_pagination_config($this->home.'/index/', $rows, $perpage, $segment);
    $filter['data'] = $this->receive_po_model->get_list($filter, $perpage, $this->uri->segment($segment));
    $this->pagination->initialize($init);
    $this->load->view('mobile/receive_po/process_list', $filter);
  }


  public function process($code)
  {
    $this->load->model('masters/products_model');
    $this->load->helper('warehouse');

    $doc = $this->receive_po_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'O' OR $doc->status == 'R')
      {
        if($doc->status == 'O')
        {
          $this->receive_po_model->update($doc->code, ['status' => 'R', 'update_user' => $this->_user->uname]);
        }

        $totalQty = 0;
        $totalReceive = 0;

        $uncomplete = $this->receive_po_model->get_in_complete_list($code);

        if( ! empty($uncomplete))
        {
          foreach($uncomplete as $rs)
          {
            $rs->barcode = $this->products_model->get_barcode($rs->product_code);
            $totalQty += $rs->qty;
            $totalReceive += $rs->receive_qty;
          }
        }

        $complete = $this->receive_po_model->get_complete_list($code);

        if( ! empty($complete))
        {
          foreach($complete as $rs)
          {
            $rs->barcode = $this->products_model->get_barcode($rs->product_code);
            $totalQty += $rs->qty;
            $totalReceive += $rs->receive_qty;
          }
        }

        $ds = array(
          'doc' => $doc,
          'incomplete' => $uncomplete,
          'complete' => $complete,
          'allQty' => $totalQty,
          'totalReceive' => $totalReceive,
          'finished' => empty($uncomplete) ? TRUE : FALSE,
          'allow_over_po' => getConfig('ALLOW_RECEIVE_OVER_PO'),
          'zone' => empty($doc->zone_code) ? NULL : $this->zone_model->get($doc->zone_code)
        );

        $this->load->view('mobile/receive_po/receive_process', $ds);
      }
      else
      {
        redirect($this->home . '/view_detail/' . $code);
      }
    }
    else
    {
      $this->page_error();
    }
  }


  //---- save receive transection while doing receive
  public function save_receive_rows()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->rows))
    {
      $doc = $this->receive_po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'O' OR $doc->status == 'R')
        {
          $this->db->trans_begin();

          foreach($ds->rows as $rs)
          {
            if($sc === FALSE)
            {
              break;
            }

            $detail = $this->receive_po_model->get_detail($rs->id);

            if( ! empty($detail))
            {
              $newQty = $rs->qty + $detail->receive_qty;

              if($detail->qty < $newQty)
              {
                $sc = FALSE;
                $this->error = "{$detail->product_code} : จำนวนที่รับ เกินจำนวนที่ส่ง กรุณาตรวจสอบ <br/> จำนวนส่ง : {$detail->qty}<br/>รับแล้ว : {$detail->receive_qty}<br/>บันทึกเพิ่ม : {$rs->qty}";
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'receive_qty' => $newQty,
                  'valid' => $detail->qty <= $newQty ? 1 : 0
                );

                if( ! $this->receive_po_model->update_detail($rs->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update receive qty at {$detail->product_code}";
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "No item row id {$rs->id} for {$rs->product_code}";
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


  //---- to finish receive in mobile mode
  public function save_and_close()
  {
    $sc = TRUE;
    $ex = 0;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code))
    {
      $doc = $this->receive_po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'O' OR $doc->status == 'R')
        {
          if( ! empty($ds->rows))
          {
            $this->db->trans_begin();

            foreach($ds->rows as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              $detail = $this->receive_po_model->get_detail($rs->id);

              if( ! empty($detail))
              {
                $newQty = $rs->qty + $detail->receive_qty;

                if($detail->qty < $newQty)
                {
                  $sc = FALSE;
                  $this->error = "{$detail->product_code} : จำนวนที่รับ เกินจำนวนที่ส่ง กรุณาตรวจสอบ <br/> จำนวนส่ง : {$detail->qty}<br/>รับแล้ว : {$detail->receive_qty}<br/>บันทึกเพิ่ม : {$rs->qty}";
                }

                if($sc === TRUE)
                {
                  $arr = array(
                    'receive_qty' => $newQty,
                    'valid' => $detail->qty <= $newQty ? 1 : 0
                  );

                  if( ! $this->receive_po_model->update_detail($rs->id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update receive qty at {$detail->product_code}";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "No item row id {$rs->id} for {$rs->product_code}";
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

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            $details = $this->receive_po_model->get_details($doc->code);

            if( ! empty($details))
            {
              $movement_date = getConfig('ORDER_SOLD_DATE') == 'D' ? db_date($doc->date_add, TRUE) : now();

              foreach($details as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }

                //--- update stock
                if($sc === TRUE)
                {
                  if( ! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, $rs->receive_qty))
                  {
                    $sc = FALSE;
                    $this->error = "Update stock failed";
                  }
                }

                //---- update po open_qty
                if($sc === TRUE)
                {
                  $po_detail = $this->po_model->get_detail($rs->po_detail_id);

                  if( ! empty($po_detail))
                  {
                    $open_qty = $po_detail->open_qty - $rs->receive_qty;
                    $open_qty = $open_qty < 0 ? 0 : $open_qty;

                    $arr = ['open_qty' => $open_qty];

                    if($open_qty == 0)
                    {
                      $arr['line_status'] = 'C';
                    }

                    if( ! $this->po_model->update_detail($rs->po_detail_id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update po open qty";
                    }
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = "รหัสสินค้าไม่ตรงกับใบสั่งซื้อ : {$rs->product_code}";
                  }
                }

                if($sc === TRUE)
                {
                  //--- insert Movement in
                  $arr = array(
                    'reference' => $doc->code,
                    'warehouse_code' => $doc->warehouse_code,
                    'zone_code' => $doc->zone_code,
                    'product_code' => $rs->product_code,
                    'move_in' => $rs->receive_qty,
                    'move_out' => 0,
                    'date_add' => $movement_date
                  );

                  if( ! $this->movement_model->add($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to create movemnt";;
                  }
                }
              }
            }

            if($sc === TRUE)
            {
              if( ! $this->receive_po_model->update_details($doc->code, ['line_status' => 'C']))
              {
                $sc = FALSE;
                $this->error = "Failed to update line status";
              }
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 'C',
                'shipped_date' => now(),
                'update_user' => $this->_user->uname
              );

              if( ! $this->receive_po_model->update($doc->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to close document";
              }
            }

            if($sc === TRUE)
            {
              if($this->po_model->recal_total($doc->po_code))
              {
                if($this->po_model->is_all_done($doc->po_code))
                {
                  $this->po_model->update($doc->po_code, ['status' => 'C']);
                }
                else
                {
                  $this->po_model->update($doc->po_code, ['status' => 'P']);
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

            if($sc === TRUE)
            {
              if(is_true(getConfig('WRX_GRPO_INTERFACE')))
              {
                $this->load->library('wrx_ib_api');

                if( ! $this->wrx_ib_api->export_receive_po($doc->code))
                {
                  $sc = FALSE;
                  $ex = 1;
                  $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป ERP ไม่สำเร็จ : {$this->wrx_ib_api->error}";
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
      if(is_true(getConfig('WRX_GRPO_INTERFACE')))
      {
        $doc = $this->receive_po_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 'C')
          {
            $this->load->library('wrx_ib_api');

            if( ! $this->wrx_ib_api->export_receive_po($doc->code))
            {
              $sc = FALSE;
              $this->error = "ส่งข้อมูลไป ERP ไม่สำเร็จ : {$this->wrx_ib_api->error}";
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


  public function view_detail($code)
  {
    $this->load->model('masters/products_model');
    $this->load->helper('warehouse');

    $doc = $this->receive_po_model->get($code);

    if( ! empty($doc))
    {
      $totalQty = 0;
      $totalReceive = 0;

      $uncomplete = $this->receive_po_model->get_in_complete_list($code);

      if( ! empty($uncomplete))
      {
        foreach($uncomplete as $rs)
        {
          $rs->barcode = $this->products_model->get_barcode($rs->product_code);
          $totalQty += $rs->qty;
          $totalReceive += $rs->receive_qty;
        }
      }

      $complete = $this->receive_po_model->get_complete_list($code);

      if( ! empty($complete))
      {
        foreach($complete as $rs)
        {
          $rs->barcode = $this->products_model->get_barcode($rs->product_code);
          $totalQty += $rs->qty;
          $totalReceive += $rs->receive_qty;
        }
      }

      $zone = empty($doc->zone_code) ? NULL : $this->zone_model->get($doc->zone_code);

      if(empty($zone))
      {
        $zone = (object)['code' => 'ไม่ระบุ', 'name' => 'ไม่ระบุ'];
      }

      $ds = array(
        'doc' => $doc,
        'incomplete' => $uncomplete,
        'complete' => $complete,
        'allQty' => $totalQty,
        'totalReceive' => $totalReceive,
        'finished' => empty($uncomplete) ? TRUE : FALSE,
        'allow_over_po' => getConfig('ALLOW_RECEIVE_OVER_PO'),
        'zone' => $zone
      );

      $this->load->view('mobile/receive_po/receive_view_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function print_detail($code)
  {
    $this->load->library('printer');

    $doc = $this->receive_po_model->get($code);

    if( ! empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
      $doc->warehouse_name = $this->warehouse_model->get_name($doc->warehouse_code);
    }

    $details = $this->receive_po_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_received', $ds);
  }


  public function save()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $doc = $this->receive_po_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'P')
        {
          $movement_date = getConfig('ORDER_SOLD_DATE') == 'D' ? db_date($ds->doc_date, TRUE) : now();

          if( ! empty($ds->rows))
          {
            $zone = $this->zone_model->get($ds->zone_code);

            if(empty($zone))
            {
              $sc = FALSE;
              $this->error = "รหัสโซนไม่ถูกต้อง";
            }

            $date_add = db_date($ds->doc_date, TRUE);
            $remark = get_null(trim($ds->remark));

            if($sc === TRUE)
            {
              $approver = get_null($ds->approver);

              $arr = array(
                'date_add' => $date_add,
                'shipped_date' => $movement_date,
                'vender_code' => $ds->vender_code,
                'vender_name' => $ds->vender_name,
                'po_code' => $ds->po_code,
                'po_ref' => $ds->po_ref,
                'invoice_code' => $ds->invoice,
                'zone_code' => $zone->code,
                'warehouse_code' => $zone->warehouse_code,
                'update_user' => $this->_user->uname,
                'approver' => $ds->approver,
                'status' => $ds->save_type == '0' ? 'P' : ($ds->save_type == '3' ? 'O' : 'C')
              );

              $this->db->trans_begin();

              if( ! $this->receive_po_model->update($doc->code, $arr))
              {
                $sc = FALSE;
                $this->error = 'Update Document Fail';
              }

              if($sc === TRUE)
              {
                //--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
                if( ! $this->receive_po_model->drop_details($doc->code))
                {
                  $sc = FALSE;
                  $this->error = "Failed to delete prevoius item rows";
                }

                if($sc === TRUE)
                {
                  foreach($ds->rows as $rs)
                  {
                    if($sc === FALSE) { break; }

                    if($rs->qty != 0)
                    {
                      $po_detail = $this->po_model->get_detail($rs->po_detail_id);

                      if( ! empty($po_detail))
                      {
                        $de = array(
                          'receive_code' => $ds->code,
                          'po_code' => $rs->po_code,
                          'po_ref' => $ds->po_ref,
                          'po_detail_id' => $rs->po_detail_id,
                          'po_line_num' => $rs->po_line_num,
                          'zone_code' => $zone->code,
                          'product_code' => $rs->product_code,
                          'product_name' => $rs->product_name,
                          'unit' => $rs->unit,
                          'qty' => $rs->qty,
                          'receive_qty' => $ds->save_type == '1' ? $rs->qty : 0,
                          'valid' => $ds->save_type == '1' ? 1 : 0,
                          'line_status' => $ds->save_type == '1' ? 'C' : 'O',
                          'update_user' => $this->_user->uname
                        );

                        if( ! $this->receive_po_model->add_detail($de))
                        {
                          $sc = FALSE;
                          $this->error = 'Add Receive Row Fail';
                          break;
                        }

                        if($sc === TRUE)
                        {
                          if($ds->save_type == '1')
                          {
                            //--- update stock
                            if( ! $this->stock_model->update_stock_zone($zone->code, $rs->product_code, $rs->qty))
                            {
                              $sc = FALSE;
                              $this->error = "Update stock failed";
                            }

                            //---- update po open_qty
                            if($sc === TRUE)
                            {
                              $open_qty = $po_detail->open_qty - $rs->qty;
                              $open_qty = $open_qty < 0 ? 0 : $open_qty;
                              $arr = array(
                                'open_qty' => $open_qty,
                                'line_status' => $open_qty == 0 ? 'C' : 'P',
                                'update_user' => $this->_user->uname
                              );

                              if( ! $this->po_model->update_detail($rs->po_detail_id, $arr))
                              {
                                $sc = FALSE;
                                $this->error = "Failed to update po open qty";
                              }
                            }

                            //--- insert Movement in
                            if($sc === TRUE)
                            {
                              $arr = array(
                                'reference' => $ds->code,
                                'warehouse_code' => $zone->warehouse_code,
                                'zone_code' => $zone->code,
                                'product_code' => $rs->product_code,
                                'move_in' => $rs->qty,
                                'move_out' => 0,
                                'date_add' => $movement_date
                              );

                              if( ! $this->movement_model->add($arr))
                              {
                                $sc = FALSE;
                                $this->error = "Insert Movement Failed";
                              }
                            }
                          }
                        }
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = "รายการสินค้าไม่ตรงกับใบสั่งซื้อ {$rs->product_code}";
                      }
                    } //--- end if qty != 0
                  } //-- end foreach

                  //--- if all po details was received close the po
                  if($sc === TRUE)
                  {
                    if($this->po_model->is_all_done($ds->po_code))
                    {
                      $this->po_model->update($ds->po_code, ['status' => 'C']);
                    }
                  }
                }//--- end if $sc === TRUE
              } //--- $sc == TRUE

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
                if($ds->save_type == '1')
                {
                  if(is_true(getConfig('WRX_GRPO_INTERFACE')))
                  {
                    $this->load->library('wrx_ib_api');

                    if( ! $this->wrx_ib_api->export_receive_po($ds->code))
                    {
                      $sc = FALSE;
                      $this->error = $this->wrx_ib_api->error;
                    }
                  }
                }
              }
            } //-- $sc == TRUE
          }
          else
          {
            $sc = FALSE;
            $this->error = "Items rows not found!";
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


  public function pull_back()
	{
		$sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      if($this->pm->can_edit)
      {
        $doc = $this->receive_po_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 'P')
          {
            if($doc->status != 'D' OR $this->_SuperAdmin)
            {
              $rows = [];

              $details = $this->receive_po_model->get_details($code);

              if( ! empty($details))
              {
                if($doc->status == 'C')
                {
                  //--- check stock for rollback
                  foreach($details as $rs)
                  {
                    if($sc === FALSE) { break; }
                    //--- check stock first
                    $stock = $this->stock_model->get_stock_zone($rs->zone_code, $rs->product_code);

                    if($stock < $rs->receive_qty)
                    {
                      $sc = FALSE;
                      $this->error = "ไม่สามารถย้อนสถานะได้เนื่องจากสต็อกคงเหลือ {$rs->product_code} ไม่เพียงพอ";
                    }
                  }
                }

                //--- if stock enough to rollback
                if($sc === TRUE)
                {
                  $this->db->trans_begin();

                  foreach($details as $rs)
                  {
                    if($sc === FALSE) { break; }

                    if($doc->status == 'C')
                    {
                      //--- updat stock zone
                      if( ! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, ($rs->receive_qty * -1)))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to update stock";
                      }

                      //---- update receive_qty
                      if($sc === TRUE)
                      {
                        $arr = array(
                          'receive_qty' => 0,
                          'valid' => 0,
                          'line_status' => 'O'
                        );

                        if( ! $this->receive_po_model->update_detail($rs->id, $arr))
                        {
                          $sc = FALSE;
                          $this->error = "Failed to update received qty";
                        }
                      }

                      //---- update po detail
                      if($sc === TRUE)
                      {
                        $po_detail = $this->po_model->get_detail($rs->po_detail_id);

                        if( ! empty($po_detail))
                        {
                          $open_qty = $po_detail->open_qty;
                          $openQty = $open_qty + $rs->receive_qty;

                          $arr = ['open_qty' => $openQty, 'valid' => 0, 'line_status' => $openQty == $po_detail->qty ? 'O' : 'P'];

                          if( ! $this->po_model->update_detail($rs->po_detail_id, $arr))
                          {
                            $sc = FALSE;
                            $this->error = "Failed to update po line item";
                          }
                        }
                      }
                    } //-- if
                  } //- foreach

                  //--- drop movement
                  if($sc === TRUE && $doc->status == 'C')
                  {
                    if( ! $this->movement_model->drop_movement($code))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to remove movement";
                    }

                    //---- roll back po status
                    if($sc === TRUE)
                    {
                      $this->po_model->recal_total($doc->po_code);

                      $po_status = $this->po_model->is_all_open($doc->po_code) ? 'O' : 'P';

                      if( ! $this->po_model->update($doc->po_code, ['status' => $po_status]))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to change PO status";
                      }
                    }
                  }

                  //---- update doc status
                  if($sc === TRUE)
                  {
                    $arr = array(
                      'status' => 'P',
                      'update_user' => $this->_user->uname
                    );

                    if( ! $this->receive_po_model->update($code, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to change document status";
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
              set_error('status');
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


  public function cancle()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $reason = $this->input->post('reason');

    if( ! empty($code))
    {
      $doc = $this->receive_po_model->get($code);

      if( ! empty($doc))
      {
        $details = $this->receive_po_model->get_details($code);

        if( ! empty($details))
        {
          if($doc->status == 'C')
          {
            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }
              //--- check stock first
              $stock = $this->stock_model->get_stock_zone($rs->zone_code, $rs->product_code);

              if($stock < $rs->receive_qty)
              {
                $sc = FALSE;
                $this->error = "ไม่สามารถย้อนสถานะได้เนื่องจากสต็อกคงเหลือ {$rs->product_code} ไม่เพียงพอ";
              }
            }
          }

          //--- if stock enough to rollback
          if($sc === TRUE)
          {
            $this->db->trans_begin();

            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              if($doc->status == 'C')
              {
                //--- updat stock zone
                if( ! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, ($rs->receive_qty * -1)))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update stock";
                }

                //---- update po detail
                if($sc === TRUE)
                {
                  $po_detail = $this->po_model->get_detail($rs->po_detail_id);

                  if( ! empty($po_detail))
                  {
                    $open_qty = $po_detail->open_qty;
                    $openQty = $open_qty + $rs->receive_qty;

                    $arr = ['open_qty' => $openQty, 'valid' => 0, 'line_status' => 'O'];

                    if( ! $this->po_model->update_detail($rs->po_detail_id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update po line item";
                    }
                  }
                }
              } //-- if

              //---- update receive_qty
              if($sc === TRUE)
              {
                $arr = array(
                  'receive_qty' => 0,
                  'valid' => 0,
                  'line_status' => 'D'
                );

                if( ! $this->receive_po_model->update_detail($rs->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update received qty";
                }
              }
            } //- foreach

            //--- drop movement
            if($sc === TRUE && $doc->status == 'C')
            {
              if( ! $this->movement_model->drop_movement($code))
              {
                $sc = FALSE;
                $this->error = "Failed to remove movement";
              }

              //---- roll back po status
              if($sc === TRUE && ! $this->po_model->update($doc->po_code, ['status' => 'O']))
              {
                $sc = FALSE;
                $this->error = "Failed to change PO status";
              }
            }

            //---- update doc status
            if($sc === TRUE)
            {
              $arr = array(
              'status' => 'D',
              'cancel_date' => now(),
              'cancle_reason' => get_null($reason),
              'cancle_user' => $this->_user->uname,
              'update_user' => $this->_user->uname
              );

              if( ! $this->receive_po_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to change document status";
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
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function get_po_detail()
  {
    $sc = TRUE;
    $ds = array();

    $po_code = $this->input->get('po_code');

    $po = $this->po_model->get($po_code);

    if( ! empty($po))
    {
      $ro = getConfig('RECEIVE_OVER_PO');

      $rate = ($ro * 0.01);

      $details = $this->po_model->get_details($po_code);

      if( ! empty($details))
      {
        $no = 1;

        foreach($details as $rs)
        {
  				if($rs->open_qty > 0)
  				{
            $dif = $rs->qty - $rs->open_qty;
            $onOrder = $this->receive_po_model->get_on_order_qty($rs->product_code, $rs->po_code, $rs->id);

            $qty = $rs->open_qty - $onOrder;

            $arr = array(
              'no' => $no,
              'uid' => $rs->po_id."-".$rs->id,
              'po_code' => $po_code,
              'po_ref' => $rs->po_ref,
              'po_detail_id' => $rs->id,
              'po_line_num' => $rs->line_num,
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'unit' => $rs->unit,
              'on_order' => $onOrder,
              'on_order_label' => number($onOrder, 2),
              'qty_label' => number($qty, 2),
              'qty' => round($qty, 2),
              'onOrder' => $onOrder,
              'limit' => ($rs->qty + ($rs->qty * $rate)) - $dif,
              'backlog_label' => number($rs->open_qty, 2),
              'backlog' => round($rs->open_qty, 2),
              'isOpen' => ($rs->line_status == 'O' OR $rs->line_status == 'P') ? TRUE : FALSE
            );

            array_push($ds, $arr);
            $no++;
  				}
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ใบสั่งซื้อไม่ถูกต้อง หรือ ใบสั่งซื้อถูกปิดไปแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบใบสั่งซื้อ";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'po_code' => $sc === TRUE ? $po->code : NULL,
      'po_ref' => $sc === TRUE ? $po->reference : NULL,
      'vender_code' => $sc === TRUE ? $po->vender_code : NULL,
      'vender_name' => $sc === TRUE ? $po->vender_name : NULL,
      'details' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->helper('warehouse');

    $doc = $this->receive_po_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'P' OR $doc->status == 'O')
      {
        $details = $this->receive_po_model->get_details($code);

        if( ! empty($details))
        {
          $ro = getConfig('RECEIVE_OVER_PO');
    	    $rate = ($ro * 0.01);

          foreach($details as $rs)
          {
            //-- get Quantity, Openqty
            $row = $this->po_model->get_detail($rs->po_detail_id);

            if( ! empty($row))
            {
              $diff = $row->qty - $row->open_qty;
              $rs->backlogs = $row->open_qty;
              $rs->limit = ($row->qty + ($row->qty * $rate)) - $diff;
              $rs->line_status = $row->line_status;
              $rs->po_id = $row->po_id;
            }
            else
            {
              $rs->backlogs = 0;
              $rs->limit = 0;
              $rs->line_status = 'D';
            }
          }
        }


        $ds = array(
          'doc' => $doc,
          'details' => $details,
          'allow_over_po' => getConfig('ALLOW_RECEIVE_OVER_PO'),
          'zone' => empty($doc->zone_code) ? NULL : $this->zone_model->get($doc->zone_code)
        );

        $this->load->view('inventory/receive_po/receive_po_edit', $ds);
      }
      else
      {
        redirect($this->home . '/view_detail/' . $code);
      }
    }
    else
    {
      $this->page_error();
    }
  }


  public function add_new()
  {
    $this->load->view('inventory/receive_po/receive_po_add');
  }


  public function add()
  {
    $sc = TRUE;
    $date_add = db_date($this->input->post('date_add'), TRUE);
    $remark = trim($this->input->post('remark'));

    $code = $this->get_new_code($date_add);

    if( ! empty($code))
    {
      $arr = array(
        'code' => $code,
        'date_add' => $date_add,
        'remark' => get_null($remark),
        'user' => $this->_user->uname
      );

      if( ! $this->receive_po_model->add($arr))
      {
        $sc = FALSE;
        $this->error = "Create Document Failed";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Cannot generate document number at this time";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function update_header()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $date_add = db_date($this->input->post('date_add'), TRUE);
    $due_date = empty($this->input->post('due_date')) ? $date_add : db_date($this->input->post('due_date'), FALSE);
    $posting_date = empty($this->input->post('posting_date')) ? $due_date : db_date($this->input->post('posting_date', TRUE));
    $remark = get_null(trim($this->input->post('remark')));

    if(!empty($code))
    {
      $doc = $this->receive_po_model->get($code);

      if(!empty($doc))
      {
        if($doc->status == 0)
        {
          $arr = array(
            'date_add' => $date_add,
            'due_date' => $due_date,
            'shipped_date' => $posting_date,
            'remark' => $remark
          );

          if(! $this->receive_po_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกบันทึกแล้วไม่สามารถแก้ไขได้";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบข้อมูล";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขทีเอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_PO');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_PO');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_po_model->get_max_code($pre);
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
      'receive_code',
      'receive_invoice',
      'receive_po',
      'receive_vender',
      'receive_from_date',
      'receive_to_date',
      'receive_status',
      'receive_warehouse',
      'receive_sap',
      'receive_user'
    );

    return clear_filter($filter);
  }

} //--- end class
