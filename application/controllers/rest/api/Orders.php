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
  private $type = 'ORDER';

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
      //---- check duplicate order number
      $order = $this->orders_model->get_active_order_by_oracle_id($data->headerInternalId);

      $role = 'S';

      $date_add = date('Y-m-d H:i:s');
      $doc_date = empty($data->doc_date) ? NULL : db_date($data->doc_date, TRUE);
      $due_date = empty($data->due_date) ? NULL : db_date($data->due_date, TRUE);

      $ref_code = $data->order_number;

      $customer = $this->customers_model->get($data->customer_code);

      $state = 3;

      $warehouse_code = getConfig('IX_WAREHOUSE');

      //---- id_sender
      $sender = $this->sender_model->get_id($data->shipping);

      $id_sender = empty($sender) ? NULL : $sender;

      //--- order code gen จากระบบ
      $order_code = empty($order) ? $this->get_new_code($date_add) : $order->code;

      $tracking = get_null($data->tracking_no);

      $total_amount = 0;
      $is_hold = empty($data->on_hold) ? 0 : ($data->on_hold == 'Y' ? 1 : 0);
      $is_backorder = FALSE;
      $backorderList = [];

      $ship_to = empty($data->ship_to) ? NULL : (array) $data->ship_to;
      $customer_ref = empty(trim($data->customer_ref)) ? NULL : get_null(trim($data->customer_ref));

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
          'customer_code' => $data->customer_code,
          'customer_name' => $data->customer_name,
          'customer_ref' => $customer_ref,
          'channels_code' => $data->channel,
          'payment_code' => $data->payment_method,
          'cod_amount' => $data->cod_amount,
          'state' => 3,
          'status' => 1,
          'shipping_code' => $tracking,
          'user' => $this->user,
          'date_add' => $date_add,
          'doc_date' => $doc_date,
          'due_date' => $due_date,
          'warehouse_code' => $warehouse_code,
          'id_sender' => $id_sender
        );
      }
      else
      {
        //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
        $ds = array(
          'reference' => get_null($data->reference),
          'customer_code' => $data->customer_code,
          'customer_name' => $data->customer_name,
          'customer_ref' => $customer_ref,
          'channels_code' => $data->channel,
          'payment_code' => $data->payment_method,
          'cod_amount' => $data->cod_amount,
          'state' => 3,
          'shipping_code' => $tracking,
          'user' => $this->user,
          'date_add' => $date_add,
          'doc_date' => $doc_date,
          'due_date' => $due_date,
          'warehouse_code' => $warehouse_code,
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
              $disc = $rs->discount > 0 ? $rs->discount/$rs->qty : 0;

              if($data->channel == 'SHOPEE' && $rs->price == 0)
              {
                $is_hold = 1;
              }

              //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
              $arr = array(
                "line_id" => $rs->line_item_id,
                "order_code" => $order_code,
                "model_code" => $item->model_code,
                "product_code" => $item->code,
                "product_name" => $item->name,
                "cost"  => $item->cost,
                "price"	=> $rs->price, //--- price bef disc
                "qty" => $rs->qty,
                "discount1"	=> round($disc, 2),
                "discount2" => 0,
                "discount3" => 0,
                "discount_amount" => $rs->discount, //--- discount per item * qty
                "total_amount"	=> round($rs->amount, 2),
                "is_count" => $item->count_stock,
                "is_api" => 1
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
                $disc = $rs->discount > 0 ? $rs->discount/$rs->qty : 0;

                if($data->channel == 'SHOPEE' && $rs->price == 0)
                {
                  $is_hold = 1;
                }

                //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
                $arr = array(
                  "line_id" => $rs->line_item_id,
                  "order_code" => $order_code,
                  "product_code" => $item->code,
                  "product_name" => $item->name,
                  "model_code" => $item->model_code,
                  "cost"  => $item->cost,
                  "price"	=> $rs->price, //--- price bef disc
                  "qty" => $rs->qty,
                  "discount1"	=> round($disc, 2),
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $rs->discount, //--- discount per item * qty
                  "total_amount"	=> round($rs->amount, 2),
                  "is_count" => $item->count_stock,
                  "is_api" => 1
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
    if(! property_exists($data, 'order_number') OR $data->order_number == '')
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

    if( ! property_exists($data, 'customer_code') OR $data->customer_code == '')
    {
      $this->error = 'customer_code is required';
      return FALSE;
    }

    if( ! empty($data->customer_code) && ! $this->customers_model->is_exists($data->customer_code))
    {
      $this->error = "Invalid Customer Code";
      return FALSE;
    }

    if( ! property_exists($data, 'channel') OR ! $this->channels_model->is_exists($data->channel))
    {
      $this->error = "Invalid channels code : {$data->channel}";
      return FALSE;
    }

    if( ! property_exists($data, 'payment_method') OR ! $this->payment_methods_model->is_exists($data->payment_method))
    {
      $this->error = 'Invalid payment_method code';
      return FALSE;
    }

    if(property_exists($data, 'headerInternalId') && $this->orders_model->is_active_order_fulfillment($data->headerInternalId))
    {
      $this->error = 'Order number already exists';
			return FALSE;
    }

    if(property_exists($data, 'reference') && $this->orders_model->is_active_order_reference($data->reference))
    {
      $this->error = 'Marketplace order number '.$data->reference.' already exists';
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
