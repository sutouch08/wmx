<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Orders extends REST_Controller
{
  public $error;
  public $user;
	public $api_path = "rest/api/orders";
	public $log_json = FALSE;
	public $api = FALSE;
  public $checkBackorder = FALSE;
  private $type = 'INT20';

  public function __construct()
  {
    parent::__construct();
		$this->api = is_true(getConfig('IX_API'));

		if($this->api)
		{
      $this->load->model('rest/api/api_logs_model');

	    $this->load->model('orders/orders_model');
	    $this->load->model('orders/order_state_model');
      $this->load->model('orders/reserv_stock_model');
	    $this->load->model('masters/products_model');
	    $this->load->model('masters/customers_model');
	    $this->load->model('masters/channels_model');
			$this->load->model('masters/sender_model');
	    $this->load->model('masters/payment_methods_model');
			$this->load->model('masters/warehouse_model');
	    $this->load->model('address/address_model');
      $this->load->model('stock/stock_model');
			$this->load->helper('sender');

	    $this->user = 'api@warrix';
			$this->logs_json = is_true(getConfig('IX_LOG_JSON'));
      $this->checkBackorder = is_true(getConfig('IX_BACK_ORDER'));
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
          'code' => $data->order_number,
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

    if($is_cancel)
    {
      $action = "cancel";
      //---- check duplicate order number
      $order = $this->orders_model->get_order_by_oracle_id($data->headerInternalId);

      if(empty($order))
      {
        $this->error = "Order fulfillment number '{$data->fulfillment}' not found";

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
            'code' => $data->fulfillment,
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

          $statusCode = 200;
          $retry = FALSE;

          $this->db->trans_begin();

          $reason = array(
            'code' => $order->code,
            'reason' => empty($data->cancel_reason) ? "No reason for cancellation" : $data->cancel_reason,
            'user' => $this->user
          );

          $this->orders_model->add_cancel_reason($reason);

          if($sc === TRUE && $order->state <= 3)
          {
            //--- put prepared product to cancle zone
            $prepared = $this->prepare_model->get_details($order->code);

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
                  'user' => $this->user,
                  'order_detail_id' => $rs->order_detail_id
                );

                if( ! $this->cancle_model->add($arr) )
                {
                  $sc = FALSE;
                  $this->error = "Move Items to Cancle failed";
                  $statusCode = 500;
                  $retry = TRUE;
                }
              } // end foreach
            } // if prepared

            //--- drop sold data
            if($sc === TRUE)
            {
              if( ! $this->invoice_model->drop_all_sold($order->code))
              {
                $sc = FALSE;
                $this->error = "Drop shipped data failed";
                $statusCode = 500;
                $retry = TRUE;
              }
            }

            //--- 1. เคลียร์ buffer
            if($sc === TRUE)
            {
              if(! $this->buffer_model->delete_all($order->code) )
              {
                $sc = FALSE;
                $this->error = "Delete buffer failed";
                $statusCode = 500;
                $retry = TRUE;
              }
            }

            //--- 2. ลบประวัติการจัดสินค้า
            if($sc === TRUE)
            {
              if(! $this->prepare_model->clear_prepare($order->code))
              {
                $sc = FALSE;
                $this->error = "Delete prepared data failed";
                $statusCode = 500;
                $retry = TRUE;
              }
            }

            //--- 3. ลบประวัติการตรวจสินค้า
            if($sc === TRUE)
            {
              if(! $this->qc_model->clear_qc($order->code))
              {
                $sc = FALSE;
                $this->error = "Delete QC failed";
                $statusCode = 500;
                $retry = TRUE;
              }
            }

            //--- remove movement
            if($sc === TRUE)
            {
              if(! $this->movement_model->drop_movement($order->code))
              {
                $sc = FALSE;
                $this->error = "Drop movement failed";
                $statusCode = 500;
                $retry = TRUE;
              }
            }

            //--- 4. set รายการสั่งซื้อ ให้เป็น ยกเลิก
            if($sc === TRUE)
            {
              if(! $this->orders_model->cancle_order_detail($order->code) )
              {
                $sc = FALSE;
                $this->error = "Cancle Order details failed";
                $statusCode = 500;
                $retry = TRUE;
              }
            }

            //--- 5. ยกเลิกออเดอร์
            if($sc === TRUE)
            {
              $arr = array(
                'state' => 9,
                'status' => 2,
                'is_backorder' => 0,
                'DocNum' => NULL,
                'is_exported' => 0
              );

              if( ! $this->orders_model->update($order->code, $arr) )
              {
                $sc = FALSE;
                $this->error = "Change order status failed";
                $statusCode = 500;
                $retry = TRUE;
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
                $statusCode = 500;
                $retry = TRUE;
              }
            }

            //--- remove backorder details
            if($sc === TRUE && $order->is_backorder)
            {
              $this->orders_model->drop_backlogs_list($order->code);
            }
          } //--- if $state <= 3
          else
          {
            //---- set cancel request flag
            $arr = array(
              'is_cancel' => 1
            );

            if( ! $this->orders_model->update($order->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to cancel fulfillment order";
              $statusCode = 500;
              $retry = TRUE;
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
          if($order->state == 8)
          {
            $sc = FALSE;
            $this->error = "Order fulfillment number {$data->fulfillment_code} already shipped cannot be cancel";
            $statusCode = 200;
            $retry = FALSE;
          }
        }

        if($sc === FALSE)
        {
          $arr = array(
            'status' => FALSE,
            'error' => $statusCode == 500 ? "Cancellation error : ".$this->error : $this->error,
            'retry' => FALSE
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $this->api_path,
              'type' => $this->type,
              'code' => $data->fulfillment,
              'action' => $action,
              'status' => 'failed',
              'message' => $this->error,
              'request_json' => $json,
              'response_json' => json_encode($arr)
            );

            $this->api_logs_model->add_logs($logs);
          }

          $this->response($arr, $statusCode);
        }
        else
        {
          $arr = array(
            'status' => 'success',
            'message' => 'Cancellation Successful',
            'fulfillment' => $data->fulfillment
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $this->api_path,
              'type' => $this->type,
              'code' => $data->order_number,
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
      } //-- $sc = TRUE
    } //-- if is cancel


    if( ! $is_cancel)
    {
      //--- check each item code
      $details = $data->details;

      if(empty($details))
      {
        $sc = FALSE;
        $this->error = "Items not found";

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
            'code' => $data->order_number,
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

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          //---- check valid items
          $item = $this->products_model->get($rs->item);

          if(empty($item))
          {
            $sc = FALSE;
            $this->error = "Invalid SKU : {$rs->item}";
          }
          else
          {
            $rs->item = $item;
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
            'code' => $data->order_number,
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

      //---- new code start
      if($sc === TRUE)
      {
        $roleMap = array(
          'WO' => 'S',
          'WS' => 'P',
          'WU' => 'U',
          'WL' => 'L',
          'WT' => 'N',
          'WC' => 'C',
          'WQ' => 'T',
          'WV' => 'Q',
          'WW' => 'W'
        );

        $ref_code = $data->order_number;

        $prefix = substr($ref_code, 0, 2);
        $role = empty($roleMap[$prefix]) ? 'S' : $roleMap[$prefix];

        $is_transfer = ($prefix == 'WT' OR $prefix == 'WQ' OR $prefix == 'WW') ? TRUE : FALSE;

        //---- check duplicate order number
        $order = $this->orders_model->get_active_order_by_oracle_id($data->headerInternalId);
        // $order = $this->orders_model->get_active_order_by_so($data->order_number);

        $date_add = date('Y-m-d H:i:s');
        $doc_date = empty($data->doc_date) ? NULL : db_date($data->doc_date, TRUE);
        $due_date = empty($data->due_date) ? NULL : db_date($data->due_date, TRUE);

        $customer = empty($data->customer_code) ? NULL : $this->customers_model->get($data->customer_code);

        $state = 3;

        $warehouse_code = empty($data->from_warehouse_code) ? getConfig('IX_WAREHOUSE') : $data->from_warehouse_code;
        $to_warehouse = empty($data->to_warehouse_code) ? NULL : $data->to_warehouse_code;

        //---- id_sender
        $sender = $this->sender_model->get_id($data->shipping);

        $id_sender = empty($sender) ? NULL : $sender;

        //--- order code gen จากระบบ
        $order_code = empty($order) ? $this->get_new_code($date_add, $prefix) : $order->code;

        $tracking = get_null($data->tracking_no);

        $total_amount = 0;
        $is_hold = empty($data->on_hold) ? 0 : ($data->on_hold == 'Y' ? 1 : 0);
        $is_backorder = FALSE;
        $backorderList = [];

        $ship_to = empty($data->ship_to) ? NULL : (array) $data->ship_to;
        $customer_ref = empty(trim($data->customer_ref)) ? NULL : get_null(trim($data->customer_ref));
        $channel_code = $this->channels_model->get_code($data->channel);

        if(empty($order))
        {
          //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
          $ds = array(
            'code' => $order_code,
            'role' => $role,
            'so_no' => $data->order_number,
            'oracle_id' => $data->headerInternalId,
            'fulfillment_code' => $data->fulfillment,
            'reference' => get_null($data->reference),
            'customer_code' => empty($data->customer_code) ? NULL : $data->customer_code,
            'customer_name' => empty($data->customer_name) ? NULL : $data->customer_name,
            'customer_ref' => $customer_ref,
            'channels_code' => $channel_code,
            'payment_code' => NULL, //empty($data->payment_method) ? NULL : $data->payment_method,
            'cod_amount' => empty($data->cod_amount) ? 0 : $data->cod_amount,
            'budget_code' => empty($data->budget_code) ? NULL : $data->budget_code,
            'state' => 3,
            'status' => 1,
            'shipping_code' => $tracking,
            'user' => $this->user,
            'date_add' => $date_add,
            'doc_date' => $doc_date,
            'due_date' => $due_date,
            'warehouse_code' => $warehouse_code,
            'to_warehouse' => $to_warehouse,
            'id_sender' => $id_sender
          );
        }
        else
        {
          $action = 'update';

          $ds = array(
            'reference' => get_null($data->reference),
            'customer_code' => empty($data->customer_code) ? NULL : $data->customer_code,
            'customer_name' => empty($data->customer_name) ? NULL : $data->customer_name,
            'customer_ref' => $customer_ref,
            'channels_code' => $channel_code,
            'payment_code' => NULL, //empty($data->payment_method) ? NULL : $data->payment_method,
            'cod_amount' => empty($data->cod_amount) ? 0 : $data->cod_amount,
            'budget_code' => empty($data->budget_code) ? NULL : $data->budget_code,
            'state' => 3,
            'shipping_code' => $tracking,
            'user' => $this->user,
            'date_add' => $date_add,
            'doc_date' => $doc_date,
            'due_date' => $due_date,
            'warehouse_code' => $warehouse_code,
            'to_warehouse' => $to_warehouse,
            'id_sender' => $id_sender
          );
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
              'code' => $data->order_number,
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

        $this->db->trans_begin();

        if(empty($order))
        {
          if(  ! $this->orders_model->add($ds))
          {
            $sc = FALSE;
            $this->error = "Order create failed";
          }
        }
        else
        {
          if( ! $this->orders_model->update($order->code, $ds))
          {
            $sc = FALSE;
            $this->error = "Failed to update order";
          }
        }

        if($sc === TRUE)
        {
          $arr = array(
            'order_code' => $order_code,
            'state' => 1,
            'update_user' => $this->user
          );

          //--- add state event
          $this->order_state_model->add_state($arr);

          if( ! empty($ship_to) && ! empty($ship_to['address']))
          {
            $arr = array(
              'order_code' => $order_code,
              'name' => $data->ship_to->name,
              'address' => $data->ship_to->address,
              'sub_district' => get_null($data->ship_to->sub_district),
              'district' => get_null($data->ship_to->district),
              'province' => get_null($data->ship_to->province),
              'postcode' => get_null($data->ship_to->postcode),
              'phone' => get_null($data->ship_to->phone)
            );

            $adr = $this->address_model->get_ship_to_address($order_code);

            if(empty($adr))
            {
              $this->address_model->add_shipping_address($arr);
            }
            else
            {
              $this->address_model->update_shipping_address($adr->id, $arr);
            }
          }

          //---- add order details
          $details = $data->details;

          if( ! empty($details))
          {
            if( ! empty($order))
            {
              $rows_id = []; //--- rows ids must update or ignore change use to  get row to delete by exclude this ids in query
              $del_rows_id = []; //--- row must delete

              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                $item = $rs->item;
                $disc = $is_transfer ? 0 : ($rs->discount > 0 ? $rs->discount/$rs->qty : 0);

                // if($data->channel == 'SHOPEE' && $rs->price == 0)
                // {
                //   $is_hold = 1;
                // }

                //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
                $arr = array(
                  "line_id" => $rs->line_item_id,
                  "order_code" => $order_code,
                  "model_code" => $item->model_code,
                  "product_code" => $item->code,
                  "product_name" => $item->name,
                  "unit_code" => $item->unit_code,
                  "cost"  => $is_transfer ? 0 : $item->cost,
                  "price"	=> $is_transfer ? 0 : $rs->price, //--- price bef disc
                  "qty" => $rs->qty,
                  "discount1"	=> $is_transfer ? 0 : round($disc, 2),
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $is_transfer ? 0 : $rs->discount, //--- discount per item * qty
                  "total_amount"	=> $is_transfer ? 0 : round($rs->amount, 2),
                  "is_count" => $item->count_stock
                );

                $row = $this->orders_model->get_detail_by_product_and_line_id($order->code, $item->code, $rs->line_item_id);

                if( ! empty($row))
                {
                  // ถ้าสินค้าเหมือนกัน  update row
                  if($row->product_code == $item->code)
                  {
                    $rows_id[] = $row->id;

                    if( ! $this->orders_model->update_detail($row->id, $arr))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update item at Line {$rs->line_item_id}";
                    }
                  }

                  //--- if item not match add new row
                  if($row->product_code != $item->code)
                  {
                    $order_detail_id = $this->orders_model->add_detail($arr);

                    if( ! $order_detail_id)
                    {
                      $sc = FALSE;
                      $this->error = "Order item insert failed : {$item->code}";
                    }
                    else
                    {
                      $rows_id[] = $order_detail_id; //--- use to get row id to delete
                    }
                  }
                }
                else
                {
                  $order_detail_id = $this->orders_model->add_detail($arr);

                  if( ! $order_detail_id)
                  {
                    $sc = FALSE;
                    $this->error = "Order item insert failed : {$item->code}";
                  }
                  else
                  {
                    $rows_id[] = $order_detail_id; //--- use to get row id to delete
                  }
                }

                if($sc === TRUE)
                {
                  $total_amount += round($rs->amount, 2);

                  if($this->checkBackorder && $item->count_stock)
                  {
                    $available = $this->get_available_stock($item->code, $warehouse_code);

                    if($available < $rs->qty)
                    {
                      $is_backorder = TRUE;

                      $backorderList[] = (object) array(
                        'order_code' => $order_code,
                        'product_code' => $item->code,
                        'order_qty' => $rs->qty,
                        'available_qty' => $available
                      );
                    }
                  }
                }
              } // end foreach

              //--- check row to delete
              if($sc === TRUE && ! empty($rows_id))
              {
                $rows_to_delete = $this->orders_model->get_exclude_details_ids($order->code, $rows_id);

                if( ! empty($rows_to_delete))
                {
                  foreach($rows_to_delete as $rd)
                  {
                    $del_rows_id[] = $rd->id;
                  }
                }

                //--- delete previous rows that remove by api
                if( ! empty($del_rows_id))
                {
                  if( ! $this->orders_model->remove_details_by_ids($del_rows_id))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to delete item rows";
                  }
                } //--- end delete rows
              } //--- end if rows_id
            }
            else //-- empty $order
            {
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                if( ! empty($rs->item))
                {
                  //--- check item code
                  $item = $rs->item;
                  $disc = $is_transfer ? 0 : ($rs->discount > 0 ? $rs->discount/$rs->qty : 0);

                  // if($data->channel == 'SHOPEE' && $rs->price == 0)
                  // {
                  //   $is_hold = 1;
                  // }

                  //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
                  $arr = array(
                    "line_id" => $rs->line_item_id,
                    "order_code" => $order_code,
                    "product_code" => $item->code,
                    "product_name" => $item->name,
                    "model_code" => $item->model_code,
                    "unit_code" => $item->unit_code,
                    "cost"  => $is_transfer ? 0 : $item->cost,
                    "price"	=> $is_transfer ? 0 : $rs->price, //--- price bef disc
                    "qty" => $rs->qty,
                    "discount1"	=> $is_transfer ? 0 : round($disc, 2),
                    "discount2" => 0,
                    "discount3" => 0,
                    "discount_amount" => $is_transfer ? 0 : $rs->discount, //--- discount per item * qty
                    "total_amount"	=> $is_transfer ? 0 : round($rs->amount, 2),
                    "is_count" => $item->count_stock
                  );

                  if( ! $this->orders_model->add_detail($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Order item insert failed : {$item->code}";
                    break;
                  }
                  else
                  {
                    $total_amount += round($rs->amount, 2);

                    if($this->checkBackorder && $item->count_stock)
                    {
                      $available = $this->get_available_stock($item->code, $warehouse_code);

                      if($available < $rs->qty)
                      {
                        $is_backorder = TRUE;

                        $backorderList[] = (object) array(
                          'order_code' => $order_code,
                          'product_code' => $item->code,
                          'order_qty' => $rs->qty,
                          'available_qty' => $available
                        );
                      }
                    }
                  }
                } //--- end if item
              }  //--- endforeach add details
            }

            if($sc === TRUE)
            {
              $arr = array(
                'doc_total' => $total_amount,
                'total_sku' => $this->orders_model->count_order_sku($order_code),
                'is_backorder' => $is_backorder == TRUE ? 1 : 0,
                'is_hold' => $is_hold
              );

              $this->orders_model->update($order_code, $arr);

              if($this->checkBackorder && ! empty($backorderList))
              {
                $this->orders_model->drop_backlogs_list($order_code);

                foreach($backorderList as $rs)
                {
                  $backlogs = array(
                    'order_code' => $rs->order_code,
                    'product_code' => $rs->product_code,
                    'order_qty' => $rs->order_qty,
                    'available_qty' => $rs->available_qty
                  );

                  $this->orders_model->add_backlogs_detail($backlogs);
                }
              }

              if($this->orders_model->change_state($order_code, 3))
              {
                $arr = array(
                  'order_code' => $order_code,
                  'state' => 3,
                  'update_user' => $this->user
                );

                $this->order_state_model->add_state($arr);
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Items not found";
          }
        } //--- if add order

        if($sc === TRUE)
        {
          $this->db->trans_commit();

          $arr = array(
            'status' => 'success',
            'message' => 'success',
            'order_code' => $order_code
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $this->api_path,
              'type' => $this->type,
              'code' => $data->order_number,
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
              'code' => $data->order_number,
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
      } // ! is_cancel
    }
  } //--- create_post


  public function get_new_code($date, $prefix = 'WO', $run_digit = 5)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $prefix = empty($prefix) ? getConfig('PREFIX_ORDER') : $prefix;
    $run_digit = empty($run_digit) ? getConfig('RUN_DIGIT_ORDER') : $run_digit;

    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
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


  public function verify_data($data)
	{
    if( ! property_exists($data, 'order_number') OR $data->order_number == '')
    {
      $this->error = 'order_number is required';
      return FALSE;
    }

    if( ! property_exists($data, 'headerInternalId') OR empty($data->headerInternalId))
    {
      $this->error = 'headerInternalId is required';
      return FALSE;
    }

    if( ! property_exists($data, 'fulfillment') OR empty($data->fulfillment))
    {
      $this->error = 'fulfillment is required';
      return FALSE;
    }

    if(property_exists($data, 'from_warehouse_code') && ! empty($data->from_warehouse_code))
    {
      $wh = $this->warehouse_model->get($data->from_warehouse_code);

      if(empty($wh))
      {
        $this->error = "Invalid from_warehouse_code or warehouse not found : {$data->from_warehouse_code}";
        return FALSE;
      }
    }

    $prefix = substr($data->order_number, 0, 2);
    $is_cancel = empty($data->is_cancel) ? FALSE : ($data->is_cancel == 'Y' ? TRUE : FALSE);

    if($prefix == 'WO')
    {
      if( ! property_exists($data, 'channel') OR ! $this->channels_model->is_exists_name($data->channel))
      {
        $this->error = "Invalid channels code : {$data->channel}";
        return FALSE;
      }
    }

    if($prefix == 'WO' OR $prefix == 'WU' OR $prefix == 'WS' OR $prefix == 'WC')
    {
      if( ! property_exists($data, 'customer_code') OR $data->customer_code == '')
      {
        $this->error = 'customer_code is required';
        return FALSE;
      }
    }

    if($prefix == 'WS' OR $prefix == 'WU')
    {
      if( ! property_exists($data, 'budget_code') OR $data->budget_code == '')
      {
        $this->error = 'budget_code is required';
        return FALSE;
      }
    }

    if($prefix == 'WT' OR $prefix == 'WQ' OR $prefix == 'WW')
    {
      if( ! property_exists($data, 'to_warehouse_code') OR empty($data->to_warehouse_code))
      {
        $this->error = 'to_warehouse_code is required for transfer order';
        return FALSE;
      }
      else
      {
        $whs = $this->warehouse_model->get($data->to_warehouse_code);

        if(empty($whs))
        {
          $this->error = "Invalid to_warehouse_code or warehouse not found : {$data->to_warehouse_code}";
        }
      }
    }

    if(property_exists($data, 'fulfillment') && $this->orders_model->is_active_order_fulfillment($data->fulfillment) && ! $is_cancel)
    {
      $this->error = "Order Fulfillment already exists : {$data->fulfillment}";
			return FALSE;
    }

		return TRUE;
	}


  public function get_available_stock($item_code, $warehouse_code)
  {
    //---- สต็อกคงเหลือในคลัง
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse_code);

    //---- ยอดจองสินค้า ไม่รวมรายการที่กำหนด
    $ordered = $this->orders_model->get_reserv_stock($item_code, $warehouse_code);

    //---- ยอดจองสินค้า ไม่รวมรายการที่กำหนด
    $reserv_stock = $this->reserv_stock_model->get_reserv_stock($item_code, $warehouse_code);

    $available = $sell_stock - $ordered - $reserv_stock;

    return $available < 0 ? 0 : $available;
  }
} //--- end class
