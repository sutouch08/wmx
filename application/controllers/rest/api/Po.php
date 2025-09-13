<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Po extends REST_Controller
{
  public $error;
  public $user;
	public $api_path = "rest/api/po";
	public $log_json = FALSE;
	public $api = FALSE;
  public $checkBackorder = FALSE;
  private $type = 'ADD23';

  public function __construct()
  {
    parent::__construct();
		$this->api = is_true(getConfig('IX_API'));

		if($this->api)
		{
      $this->load->model('rest/api/api_logs_model');

	    $this->load->model('purchase/po_model');
      $this->load->model('inventory/receive_po_model');
	    $this->load->model('masters/products_model');

	    $this->user = 'api@warrix';
			$this->logs_json = is_true(getConfig('IX_LOG_JSON'));
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => "Access denied",
        'retry' => FALSE
			);

			$this->response($arr, 400);
		}
  }


  public function create_post()
  {
    $action = 'create';
    $this->api_path = $this->api_path."/create";
    //--- Get raw post data
    $json = file_get_contents("php://input");

    if( ! $this->api)
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'API Not Enabled',
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'API Not Enabled',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $data = json_decode($json);

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data',
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'empty data',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

          $this->api_logs_model->add_logs($logs);
        }

        $this->response($arr, 400);
      }

      $sc = $this->verify_data($data);

      //---- if any error return
      if($sc === FALSE)
      {
        $arr = array(
          'status' => FALSE,
          'error' => $this->error,
          'retry' => FALSE
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $this->api_path,
            'type' => $this->type,
            'code' => $data->po_no,
            'action' => $action,
            'status' => 'failed',
            'message' => $this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->api_logs_model->add_logs($logs);
        }

        $this->response($arr, 400);
      }

      $is_cancel = empty($data->is_cancel) ? FALSE : ($data->is_cancel == 'Y' ? TRUE : FALSE);

      if( ! empty($data->items))
      {
        $lineNum = [];

        foreach($data->items as $rs)
        {
          if($sc === FALSE) { break; }

          if(empty($rs->line_num) OR empty($rs->sku) OR empty($rs->description) OR empty($rs->qty) OR empty($rs->unit))
          {
            $sc = FALSE;
            $this->error = "Missing required parameter for items";
          }
          else
          {
            $item = $this->products_model->get($rs->sku);

            if(empty($item))
            {
              $sc = FALSE;
              $this->error = "Invalid SKU {$rs->sku}";
            }

            if( ! isset($lineNum[$rs->line_num]))
            {
              $lineNum[$rs->line_num] = $rs->line_num;
            }
            else
            {
              $sc = FALSE;
              $this->error = "Duplicate Line Number {$rs->line_num}";
            }
          }
        }
      }


      //---- if any error return
      if($sc === FALSE)
      {
        $arr = array(
          'status' => FALSE,
          'error' => $this->error,
          'retry' => FALSE
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $this->api_path,
            'type' => $this->type,
            'code' => $data->po_no,
            'action' => $action,
            'status' => 'failed',
            'message' => $this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->api_logs_model->add_logs($logs);
        }

        $this->response($arr, 400);
      }

      $code = NULL;

      if($sc === TRUE)
      {
        //---- check duplicate order number
        $doc = $this->po_model->get_by_reference($data->po_no, 'NOT_CANCEL');

        if( ! empty($doc))
        {
          $action = 'update';

          if($doc->status == 'C')
          {
            $sc = FALSE;
            $this->error = "This document has been closed cannot be change";
          }

          if($sc === TRUE)
          {
            if( ! $is_cancel && $doc->status == 'D')
            {
              $sc = FALSE;
              $this->error = "This document has been canceled cannot be chane";
            }
          }

          if($sc === TRUE && $is_cancel)
          {
            $action = 'cancel';

            if($this->po_model->is_received($doc->code))
            {
              $sc = FALSE;
              $this->error = "This document has been received cannot be cancel";
            }

            if($sc === TRUE && $this->receive_po_model->is_exists_po_ref($data->po_no))
            {
              $sc = FALSE;
              $this->error = "This document has been received cannot be cancel";
            }

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 'D',
                'cancel_reason' => NULL,
                'cancel_user' => $this->user,
                'cancel_date' => now()
              );

              if( ! $this->po_model->update($doc->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'line_status' => 'D',
                  'update_user' => $this->user
                );

                if( ! $this->po_model->update_details($doc->code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update item rows status";
                }
              }
            }
          }
        }

        //---- if any error return
        if($sc === FALSE)
        {
          $arr = array(
            'status' => FALSE,
            'error' => $this->error,
            'retry' => FALSE
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $this->api_path,
              'type' => $this->type,
              'code' => $data->po_no,
              'action' => $action,
              'status' => 'failed',
              'message' => $this->error,
              'request_json' => $json,
              'response_json' => json_encode($arr)
            );

            $this->api_logs_model->add_logs($logs);
          }

          $this->response($arr, 400);
        }


        if($sc === TRUE && ! $is_cancel)
        {
          $doc_date = empty($data->doc_date) ? date('Y-m-d') : db_date($data->doc_date, FALSE);
          $due_date = empty($data->due_date) ? date('Y-m-d') : db_date($data->due_date, FALSE);

          $this->db->trans_begin();

          if(empty($doc))
          {
            $code = $this->get_new_code($doc_date);
            //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
            $arr = array(
              'code' => $code,
              'reference' => get_null($data->po_no),
              'vender_code' => $data->vender_code,
              'vender_name' => $data->vender_name,
              'doc_date' => $doc_date,
              'due_date' => $due_date,
              'remark' => empty($data->remark) ? NULL : get_null($data->remark),
              'is_api' => 1,
              'user' => $this->user
            );

            $po_id = $this->po_model->add($arr);

            if( ! $po_id)
            {
              $sc = FALSE;
              $this->error = "Failed to create new PO";
            }

            if($sc === TRUE)
            {
              $totalQty = 0;

              foreach($data->items as $rs)
              {
                if($sc === FALSE) { break; }

                $arr = array(
                  'po_id' => $po_id,
                  'po_code' => $code,
                  'po_ref' => trim($data->po_no),
                  'product_code' => trim($rs->sku),
                  'product_name' => trim($rs->description),
                  'unit' => trim($rs->unit),
                  'qty' => $rs->qty,
                  'open_qty' => $rs->qty,
                  'line_num' => $rs->line_num,
                  'update_user' => $this->user
                );

                if( ! $this->po_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert item row at line num {$rs->line_num}";
                }
                else
                {
                  $totalQty += $rs->qty;
                }
              }

              if($sc === TRUE)
              {
                $arr = array(
                  'total_qty' => $totalQty,
                  'total_open_qty' => $totalQty
                );

                $this->po_model->update($code, $arr);
              }
            }
          }
          else
          {
            $code = $doc->code;
            $action =  "update";

            //--- ! empty($doc)
            $arr = array(
              'vender_code' => $data->vender_code,
              'vender_name' => $data->vender_name,
              'doc_date' => $doc_date,
              'due_date' => $due_date,
              'remark' => empty($data->remark) ? NULL : get_null($data->remark),
              'is_api' => 1,
              'update_user' => $this->user
            );

            if( ! $this->po_model->update($doc->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update PO";
            }

            if($sc === TRUE)
            {
              if($doc->status == 'O' && $this->po_model->is_all_open($doc->code) && $this->receive_po_model->is_all_not_received($doc->code))
              {
                //--- if all details is open
                //--- drop current details
                if( ! $this->po_model->delete_all_details($doc->code))
                {
                  $sc = FALSE;
                  $this->error = "Failed to delete previous item rows";
                }

                if($sc === TRUE)
                {
                  $totalQty = 0;

                  foreach($data->items as $rs)
                  {
                    if($sc === FALSE) { break; }

                    if($rs->qty > 0)
                    {
                      $arr = array(
                        'po_id' => $doc->id,
                        'po_code' => $doc->code,
                        'po_ref' => $doc->reference,
                        'line_num' => $rs->line_num,
                        'product_code' => trim($rs->sku),
                        'product_name' => trim($rs->description),
                        'unit' => trim($rs->unit),
                        'qty' => $rs->qty,
                        'open_qty' => $rs->qty,
                        'update_user' => $this->user
                      );

                      if( ! $this->po_model->add_detail($arr))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to insert item row at line num {$rs->line_num}";
                      }
                      else
                      {
                        $totalQty += $rs->qty;
                      }
                    }
                  }

                  if($sc === TRUE)
                  {
                    $arr = array(
                      'total_qty' => $totalQty,
                      'total_open_qty' => $totalQty
                    );

                    $this->po_model->update($doc->code, $arr);
                  }
                } //--- end if($sc === TRUE)
              }
              else
              {
                $del_rows_id = []; //--- row must delete
                $add_rows = []; //--- row add new
                $update_rows = []; //--- rows to update

                foreach($data->items  as $rs)
                {
                  if($sc === FALSE) { break; }

                  $row = $this->po_model->get_detail_by_product_and_line_num($doc->code, $rs->sku, $rs->line_num);

                  if( ! empty($row))
                  {
                    $is_received = $this->receive_po_model->is_exists_po_detail($row->id);

                    if($rs->qty > 0)
                    {
                      if($row->line_status == 'O')
                      {
                        if($rs->qty != $row->qty)
                        {
                          $update_rows[] = (object) array(
                            'id' => $row->id,
                            'line_num' => $rs->line_num,
                            'product_code' => $rs->sku,
                            'product_name' => $rs->description,
                            'unit' => $rs->unit,
                            'qty' => $rs->qty,
                            'open_qty' => $rs->qty,
                            'update_user' => $this->user
                          );
                        }
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = "Po line number {$rs->line_num} has been received cannot be change";
                      }
                    }
                    else
                    {
                      //--- to cancel
                      if(($row->line_status == 'O' OR $row->line_status == 'D') && ! $is_received)
                      {
                        $del_rows_id[] = $row->id;
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = "Po line number {$rs->line_num} has been received cannot be change";
                      }
                    }
                  }
                  else
                  {
                    //--- if empty row
                    $add_rows[] = (object) array(
                      'po_id' => $doc->id,
                      'po_code' => $doc->code,
                      'po_ref' => $doc->reference,
                      'line_num' => $rs->line_num,
                      'product_code' => trim($rs->sku),
                      'product_name' => trim($rs->description),
                      'unit' => trim($rs->unit),
                      'qty' => $rs->qty,
                      'open_qty' => $rs->qty,
                      'update_user' => $this->user
                    );
                  }
                }
                //--- end foreach

                //--- update rows
                if($sc === TRUE && ! empty($update_rows))
                {
                  foreach($update_rows as $ru)
                  {
                    if($sc === FALSE) { break; }

                    $arr = (array) $ru;

                    if( ! $this->po_model->update_detail($ru->id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update item row at line number {$ru->line_num}";
                    }
                  }
                } //-- end update rows

                //--- add new rows
                if($sc === TRUE && ! empty($add_rows))
                {
                  foreach($add_rows as $ra)
                  {
                    if($sc === FALSE) { break; }

                    $arr = (array) $ra;

                    if( ! $this->po_model->add_detail($arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to insert new item row at line number {$ra->line_num}";
                    }
                  }
                } //--end add new rows

                //--- delete rows
                if($sc === TRUE && ! empty($del_rows_id))
                {
                  if( ! $this->po_model->delete_rows_id($del_rows_id))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to delete item rows";
                  }
                } //--- end delete rows


                if($sc === TRUE)
                {
                  $this->po_model->recal_total($doc->code);
                }
              } //-- end doc->status == 'O'
            }
          }
        }

        if($sc === TRUE)
        {
          $this->db->trans_commit();

          $arr = array(
            'status' => 'success',
            'message' => 'success',
            'doc_code' => $code
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $this->api_path,
              'type' => $this->type,
              'code' => $data->po_no,
              'action' => $action,
              'status' => 'success',
              'message' => 'success',
              'request_json' => $json,
              'response_json' => json_encode($arr)
            );

            $this->api_logs_model->add_logs($logs);
          }

          $this->response($arr, 200);
        }
        else
        {
          $this->db->trans_rollback();

          $arr = array(
            'status' => FALSE,
            'error' => $this->error,
            'retry' => TRUE
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $this->api_path,
              'type' => $this->type,
              'code' => $data->po_no,
              'action' => $action,
              'status' => 'failed',
              'message' => $this->error,
              'request_json' => $json,
              'response_json' => json_encode($arr)
            );

            $this->api_logs_model->add_logs($logs);
          }

          $this->response($arr, 200);
        }
      }
    } //--- create_post


  public function cancel_put()
  {
    $sc = TRUE;
    $action = 'cancel';

    if( ! $this->api)
    {
      if($this->logs_json)
      {
        $arr = array(
          'status' => FALSE,
          'error' => 'API Not Enabled',
          'retry' => FALSE
        );

        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'API Not Enabled',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $json = file_get_contents("php://input");

    $data = json_decode($json);

    $this->api_path."/cancel";

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data',
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'empty data',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }


    if(empty($data->order_number) && empty($data->order_code))
    {
      $this->error = 'order_number is required';

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $code = empty($data->order_number) ? $data->order_code : $data->order_number;

    $order = empty($data->order_number) ? $this->orders_model->get($code) : $this->orders_model->get_order_by_reference($code);

    if(empty($order))
    {
      $this->error = "Invalid order_number: {$code}";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }


    if($sc === TRUE)
    {
      if($order->state < 8 && $order->state != 9)
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

        $this->db->trans_begin();

        $reason = array(
          'code' => $order->code,
          'reason_id' => empty($data->reason_group_id) ? NULL : $data->reason_group_id,
          'reason' => empty($data->cancel_reason) ? "No reason for cancellation" : $data->cancel_reason,
          'user' => $this->user
        );

        $this->orders_model->add_cancle_reason($reason);

        if($sc === TRUE && $order->state > 3)
        {
          //--- put prepared product to cancle zone
          $prepared = $this->prepare_model->get_details($order->code);

          if(! empty($prepared))
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
                'user' => $this->user,
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
            if( ! $this->invoice_model->drop_all_sold($order->code))
            {
              $sc = FALSE;
              $this->error = "Drop shipped data failed";
            }
          }
        }

        if($sc === TRUE)
        {
          //---- เมื่อมีการยกเลิกออเดอร์
          //--- 1. เคลียร์ buffer
          if(! $this->buffer_model->delete_all($order->code) )
          {
            $sc = FALSE;
            $this->error = "Delete buffer failed";
          }

          //--- 2. ลบประวัติการจัดสินค้า
          if($sc === TRUE)
          {
            if(! $this->prepare_model->clear_prepare($order->code) )
            {
              $sc = FALSE;
              $this->error = "Delete prepared data failed";
            }
          }


          //--- 3. ลบประวัติการตรวจสินค้า
          if($sc === TRUE)
          {
            if(! $this->qc_model->clear_qc($order->code) )
            {
              $sc = FALSE;
              $this->error = "Delete QC failed";
            }
          }

    			//--- remove movement
    	    if($sc === TRUE)
    	    {
    	      if(! $this->movement_model->drop_movement($order->code) )
    	      {
    	        $sc = FALSE;
    	        $this->error = "Drop movement failed";
    	      }
    	    }


          //--- 4. set รายการสั่งซื้อ ให้เป็น ยกเลิก
          if($sc === TRUE)
          {
            if(! $this->orders_model->cancle_order_detail($order->code) )
            {
              $sc = FALSE;
              $this->error = "Cancle Order details failed";
            }
          }


          //--- 5. ยกเลิกออเดอร์
          if($sc === TRUE)
          {
            $arr = array(
              'state' => 9,
              'status' => 2,
              'is_backorder' => 0,
              'inv_code' => NULL,
              'is_exported' => 0,
              'is_report' => NULL
            );

            if( ! $this->orders_model->update($order->code, $arr) )
            {
              $sc = FALSE;
              $this->error = "Change order status failed";
            }
          }

          //--- 6. add order state change
          if($sc === TRUE)
          {
            $arr = array(
              'order_code' => $order->code,
              'state' => 9,
              'update_user' => $this->user
            );

            if( ! $this->order_state_model->add_state($arr) )
            {
              $sc = FALSE;
              $this->error = "Add state failed";
            }
          }

          //--- remove backorder details
          if($sc === TRUE && $order->is_backorder)
          {
            $this->orders_model->drop_backlogs_list($order->code);
          }


          if($sc === TRUE)
          {
            //--- 6. ลบรายการที่ผู้ไว้ใน order_transform_detail (กรณีเบิกแปรสภาพ)
            if($order->role == 'T' OR $order->role == 'Q')
            {
              if(! $this->transform_model->clear_transform_detail($order->code) )
              {
                $sc = FALSE;
                $this->error = "Clear Transform backlogs failed";
              }

              $this->transform_model->close_transform($order->code);
            }

            //-- หากเป็นออเดอร์ยืม
            if($order->role == 'L')
            {
              if(! $this->lend_model->drop_backlogs_list($order->code) )
              {
                $sc = FALSE;
                $this->error = "Drop Lend backlogs failed";
              }
            }

            //---- ถ้าเป็นฝากขายโอนคลัง ตามไปลบ transfer draft ที่ยังไม่เอาเข้าด้วย
            if($order->role == 'N')
            {
              $middle = $this->transfer_model->get_middle_transfer_draft($order->code);

              if( ! empty($middle))
              {
                foreach($middle as $rows)
                {
                  $this->transfer_model->drop_middle_transfer_draft($rows->DocEntry);
                }
              }
            }
            else if($order->role == 'T' OR $order->role == 'Q' OR $order->role == 'L')
            {
              $middle = $this->transfer_model->get_middle_transfer_doc($order->code);

              if( ! empty($middle))
              {
                foreach($middle as $rows)
                {
                  $this->transfer_model->drop_middle_exits_data($rows->DocEntry);
                }
              }
            }
            else
            {
              //---- ถ้าออเดอร์ยังไม่ถูกเอาเข้า SAP ลบออกจากถังกลางด้วย
              $middle = $this->delivery_order_model->get_middle_delivery_order($order->code);

              if( ! empty($middle))
              {
                foreach($middle as $rows)
                {
                  $this->delivery_order_model->drop_middle_exits_data($rows->DocEntry);
                }
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
        //--- add to cancel request
        if($order->state >= 8 && $order->state != 9)
        {
          $arr = array(
            'reference' => $order->reference,
            'order_code' => $order->code,
            'user' => $this->user
          );

          if( ! $this->orders_model->add_cancel_request($arr))
          {
            $sc = FALSE;
            $this->error = "Failed to create cancellation request";
          }
          else
          {
            $arr = array(
              'order_code' => $order->code,
              'state' => 36, //-- Cancelled
              'update_user' => $this->user
            );

            $this->order_state_model->add_state($arr);
          }
        }

        if($order->state == 9)
        {
          $arr = array(
            'order_code' => $order->code,
            'state' => 9,
            'update_user' => $this->user
          );

          $this->order_state_model->add_state($arr);
        }
      }
    }

    if($sc === TRUE)
    {
      //--- logs result
      $arr = array(
        'status' => 'success',
        'message' => "Order {$code} Cancellation Successful.",
        'order_number' => $code
      );

      if($this->logs_json)
			{
				$logs = array(
					'trans_id' => genUid(),
					'api_path' => $this->api_path,
					'type' => 'ORDER',
					'code' => $code,
					'action' => $action,
					'status' => 'success',
					'message' => 'success',
					'request_json' => $json,
					'response_json' => json_encode($arr)
				);

				$this->api_logs_model->add_logs($logs);
			}

			$this->response($arr, 200);
    }
    else
    {
      //--- logs result
      $arr = array(
        'status' => FALSE,
        'message' => $this->error,
        'order_number' => $code,
        'retry' => TRUE
      );

      if($this->logs_json)
			{
				$logs = array(
					'trans_id' => genUid(),
					'api_path' => $this->api_path,
					'type' => 'ORDER',
					'code' => $code,
					'action' => $action,
					'status' => 'failed',
					'message' => $this->error,
					'request_json' => $json,
					'response_json' => json_encode($arr)
				);

				$this->api_logs_model->add_logs($logs);
			}

			$this->response($arr, 200);
    }
  } //--- end cancel


  public function get_new_code($date, $prefix = 'PO', $run_digit = 5)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $prefix = empty($prefix) ? getConfig('PREFIX_PO') : $prefix;
    $run_digit = empty($run_digit) ? getConfig('RUN_DIGIT_PO') : $run_digit;

    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->po_model->get_max_code($pre);

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



  public function verify_data($data)
	{
    if(empty($data->po_no))
    {
      $this->error = "po_no is required";
      return FALSE;
    }

    if( ! empty($data->po_no))
    {
      $option = 'NOT_CANCEL'; //-- NOT_CANCEL = Only not cancel po, ALL = all document releate to reference
      $doc = $this->po_model->get_by_reference($data->po_no, 'NOT_CANCEL');

      if( ! empty($doc))
      {
        if($doc->status == 'C')
        {
          $this->error = "PO {$data->po_no} already closed cannot be change";
          return FALSE;
        }
      }
    }


    if(empty($data->vender_code))
    {
      $this->error = "vender_code is required";
      return FALSE;
    }

    if(empty($data->vender_name))
    {
      $this->error = "vender_name is required";
      return FALSE;
    }

    if(empty($data->doc_date))
    {
      $this->error = "doc_date is required";
      return FALSE;
    }

    if( ! empty($data->doc_date))
    {
      $year = date('Y', strtotime($data->doc_date));

      if($year < date('Y'))
      {
        $this->error = "Invalid doc_date";

        return FALSE;
      }
    }

    if(empty($data->items))
    {
      $this->error = "Items is required";
      return FALSE;
    }

		return TRUE;
	}
} //--- end class
