<?php
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Return_order extends REST_Controller
{
  public $error;
  public $user = 'api@warrix';
  public $api_path = "rest/api/return_order";
  public $logs_json = FALSE;
  public $api = FALSE;
  public $checkBackorder = FALSE;
  private $type = 'ADD90';

  public function __construct()
  {
    parent::__construct();
    $this->api = is_true(getConfig('IX_API'));

    if (! $this->api)
    {
      $arr = array(
        'code' => 503,
        'status' => 'error',
        'message' => 'Service Unavailable',
        'retry' => FALSE
      );

      $this->response($arr, 503);
    }

    $this->load->model('rest/api/api_logs_model');
    $this->load->model('inventory/return_order_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->logs_json = is_true(getConfig('IX_LOG_JSON'));
  }


  public function create_post()
  {
    $req_start = now(TRUE);
    $retry = FALSE;
    $trans_id = genUid();
    $refCode = NULL; //-- return authorization number
    $code = NULL; //-- return code
    $action = 'create';
    $this->api_path = $this->api_path . "/create";
    $json = file_get_contents("php://input");
    $data = json_decode($json);

    if (empty($data))
    {
      $this->error = "Empty request data";

      $arr = array(
        'trans_id' => $trans_id,
        'code' => 400,
        'status' => 'error',
        'error' => $this->error,
        'retry' => $retry
      );

      if ($this->logs_json)
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
          'response_json' => json_encode($arr),
          'req_start' => $req_start,
          'req_end' => now(TRUE),
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $sc = $this->verify_data($data);

    $refCode = empty($data->returnAuthNumber) ? NULL : $data->returnAuthNumber;

    //---- if any error return
    if ($sc === FALSE)
    {
      $arr = array(
        'trans_id' => $trans_id,
        'code' => 400,
        'status' => 'error',
        'error' => $this->error,
        'retry' => FALSE
      );

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $refCode,
          'reference' => $code,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr),
          'req_start' => $req_start,
          'req_end' => now(TRUE)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    if (! empty($data->items))
    {
      $lineNum = [];

      $i = 0;

      foreach ($data->items as $rs)
      {
        if ($sc === FALSE)
        {
          break;
        }

        if (empty($rs->line_num))
        {
          $sc = FALSE;
          $this->error = "Missing required parameter : Items[{$i}].line_num";
        }

        if (empty($rs->sku))
        {
          $sc = FALSE;
          $this->error = "Missing required parameter : Items[{$i}].sku";
        }

        if (empty($rs->description))
        {
          $sc = FALSE;
          $this->error = "Missing required parameter : Items[{$i}].description";
        }

        if (empty($rs->qty) or $rs->qty <= 0)
        {
          $sc = FALSE;
          $this->error = "Missing required parameter : Items[{$i}].qty OR qty <= 0";
        }

        if (empty($rs->unit))
        {
          $sc = FALSE;
          $this->error = "Missing required parameter : Items[{$i}].unit";
        }

        if ($sc === TRUE)
        {
          if (! isset($lineNum[$rs->line_num]))
          {
            $lineNum[$rs->line_num] = $rs->line_num;
          }
          else
          {
            $sc = FALSE;
            $this->error = "Duplicate Line Number at Items[{$i}].line_num : {$rs->line_num}";
          }
        }

        $i++;
      } //--- end foreach
    }

    //---- if any error return
    if ($sc === FALSE)
    {
      $arr = array(
        'trans_id' => $trans_id,
        'code' => 400,
        'status' => 'error',
        'message' => $this->error,
        'retry' => FALSE
      );

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => $trans_id,
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $refCode,
          'reference' => $code,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr),
          'req_start' => $this->req_start,
          'req_end' => now(TRUE)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $doc = $this->return_order_model->get_by_active_reference($data->returnAuthNumber);

    if (! empty($doc))
    {
      $code = $doc->code;

      if ($sc === TRUE && $doc->status == 'C')
      {
        $sc = FALSE;
        $this->error = "{$data->returnAuthNumber} already closed cannot be change";
      }

      if ($sc === TRUE && $doc->status == 'O')
      {
        $sc = FALSE;
        $this->error = "{$data->returnAuthNumber} already in progress cannot be change";
      }
    }

    //---- if any error return
    if ($sc === FALSE)
    {
      $arr = array(
        'trans_id' => $trans_id,
        'code' => 200,
        'status' => 'failed',
        'message' => $this->error,
        'retry' => $retry,
        'return_code' => $code
      );

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $refCode,
          'reference' => $code,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr),
          'req_start' => $this->req_start,
          'req_end' => now(TRUE)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $doc_date = empty($data->doc_date) ? date('Y-m-d') : db_date($data->doc_date, FALSE);
    $zone = $this->zone_model->get(getConfig('IX_RETURN_ZONE'));

    $this->db->trans_begin();

    if (empty($doc))
    {
      $code = $this->get_new_code($doc_date);
      //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
      $arr = array(
        'code' => $code,
        'reference' => $data->returnAuthNumber,
        'order_code' => empty($data->order_code) ? NULL : get_null($data->order_code),
        'customer_code' => $data->customer_code,
        'customer_name' => $data->customer_name,
        'warehouse_code' => empty($zone) ? NULL : $zone->warehouse_code,
        'warehouse_name' => empty($zone) ? NULL : $zone->warehouse_name,
        'zone_code' => empty($zone) ? NULL : $zone->code,
        'zone_name' => empty($zone) ? NULL : $zone->name,
        'date_add' => $doc_date,
        'remark' => empty($data->remark) ? NULL : get_null($data->remark),
        'user' => $this->user
      );

      $return_id = $this->return_order_model->add($arr);

      if (! $return_id)
      {
        $sc = FALSE;
        $this->error = "Failed to create new document";
        $retry = TRUE;
      }

      if ($sc === TRUE)
      {
        $totalQty = 0;

        foreach ($data->items as $rs)
        {
          if ($sc === FALSE)
          {
            break;
          }

          $arr = array(
            'return_id' => $return_id,
            'return_code' => $code,
            'line_num' => $rs->line_num,
            'product_code' => trim($rs->sku),
            'product_name' => trim($rs->description),
            'unit_code' => trim($rs->unit),
            'qty' => $rs->qty,
            'receive_qty' => 0,
            'line_status' => 'O',
            'update_user' => $this->user
          );

          if (! $this->return_order_model->add_detail($arr))
          {
            $sc = FALSE;
            $this->error = "Failed to insert item row at line num {$rs->line_num}";
            $retry = TRUE;
          }

          $totalQty += $rs->qty;
        }

        if ($sc === TRUE)
        {
          $arr = array(
            'total_qty' => $totalQty
          );

          $this->return_order_model->update($code, $arr);
        }
      }
    }
    else
    {
      $arr = array(
        'order_code' => empty($data->order_code) ? NULL : get_null($data->order_code),
        'customer_code' => $data->customer_code,
        'customer_name' => $data->customer_name,
        'warehouse_code' => empty($zone) ? NULL : $zone->warehouse_code,
        'warehouse_name' => empty($zone) ? NULL : $zone->warehouse_name,
        'zone_code' => empty($zone) ? NULL : $zone->code,
        'zone_name' => empty($zone) ? NULL : $zone->name,
        'date_add' => $doc_date,
        'status' => 'P',
        'total_qty' => 0,
        'remark' => empty($data->remark) ? NULL : get_null($data->remark),
        'update_user' => $this->user
      );

      if (! $this->return_order_model->update($doc->code, $arr))
      {
        $sc = FALSE;
        $this->error = "Failed to update document";
        $retry = TRUE;
      }

      if ($sc === TRUE)
      {
        //--- drop current details
        if (! $this->return_order_model->drop_details($doc->code))
        {
          $sc = FALSE;
          $this->error = "Failed to delete previous item rows";
          $retry = TRUE;
        }

        if ($sc === TRUE)
        {
          $totalQty = 0;

          foreach ($data->items as $rs)
          {
            if ($sc === FALSE)
            {
              break;
            }

            $arr = array(
              'return_id' => $doc->id,
              'return_code' => $doc->code,
              'line_num' => $rs->line_num,
              'product_code' => trim($rs->sku),
              'product_name' => trim($rs->description),
              'unit_code' => trim($rs->unit),
              'qty' => $rs->qty,
              'receive_qty' => 0,
              'line_status' => 'O',
              'update_user' => $this->user
            );

            if (! $this->return_order_model->add_detail($arr))
            {
              $sc = FALSE;
              $this->error = "Failed to insert item row at line num {$rs->line_num}";
              $retry = TRUE;
            }

            $totalQty += $rs->qty;
          }

          if ($sc === TRUE)
          {
            $arr = array(
              'total_qty' => $totalQty
            );

            $this->return_order_model->update($doc->code, $arr);
          }
        } //--- end if($sc === TRUE)
      }
    }

    if ($sc === TRUE)
    {
      $this->db->trans_commit();
    }
    else
    {
      $this->db->trans_rollback();
    }

    $arr = array(
      'trans_id' => $trans_id,
      'code' => 200,
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'Success' : $this->error,
      'retry' => $retry,
      'return_code' => $code
    );

    if($this->logs_json)
    {
      $logs = array(
        'trans_id' => $trans_id,
        'api_path' => $this->api_path,
        'type' => $this->type,
        'code' => $refCode,
        'reference' => $code,
        'action' => $action,
        'status' => $sc === TRUE ? 'success' : 'failed',
        'message' => $sc === TRUE ? 'Success' : $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr),
        'req_start' => $req_start,
        'req_end' => now(TRUE),
      );

      $this->api_logs_model->add_logs($logs);
    }

    $this->response($arr, 200);
  } //--- create_post

  public function cancel_post()
  {
    $req_start = now(TRUE);
    $sc = TRUE;
    $code = NULL;
    $reference = NULL;
    $retry = FALSE;
    $action = 'cancel';
    $trans_id = genUid();
    $this->api_path = $this->api_path . '/cancel';
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    if(empty($data))
    {
      $this->error = "Empty request data";
      $arr = array(
        'trans_id' => $trans_id,
        'code' => 400,
        'status' => 'error',
        'message' => $this->error,
        'retry' => $retry
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => $trans_id,
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $code,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr),
          'req_start' => $req_start,
          'req_end' => now(TRUE),
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }
    
    $code = empty($data->refCode) ? NULL : $data->refCode;

    $fields = ['refCode', 'cancel_reason', 'cancel_user'];

    foreach($fields as $field)
    {
      if(empty($data->$field))
      {
        $this->error = "Missing required field: $field";
        $sc = FALSE;
        break;        
      }
    }

    if($sc === FALSE)
    {
      $arr = array(
        'trans_id' => $trans_id,
        'code' => 400,
        'status' => 'error',
        'message' => $this->error,
        'retry' => $retry
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => $trans_id,
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $code,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr),
          'req_start' => $req_start,
          'req_end' => now(TRUE),
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $doc = $this->return_order_model->get_by_active_reference($data->refCode);

    if( ! empty($doc))
    {
      $reference = $doc->code;

      if($doc->status == 'C')
      {
        $sc = FALSE;
        $this->error = "{$data->refCode} already closed cannot be changed";        
      }      

      if($sc === TRUE && $this->return_order_model->is_received($doc->code))
      {
        $sc = FALSE;
        $this->error = "{$data->refCode} has been received cannot be cancel";
      }

      if($sc === FALSE)
      {
        $arr = array(
          'trans_id' => $trans_id,
          'code' => 200,
          'status' => 'failed',
          'message' => $this->error,
          'retry' => $retry,
          'return_code' => $reference
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $this->type,
            'code' => $code,
            'reference' => $reference,
            'action' => $action,
            'status' => 'failed',
            'message' => $this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr),
            'req_start' => $req_start,
            'req_end' => now(TRUE),
          );

          $this->api_logs_model->add_logs($logs);
        }

        $this->response($arr, 200);
      }

      if($sc === TRUE && $doc->status != 'D')
      {
        $this->db->trans_begin();

        $arr = array(
          'status' => 'D',
          'cancel_reason' => $data->cancel_reason,
          'cancel_user' => $data->cancel_user,
          'cancel_date' => now()
        );

        if( ! $this->return_order_model->update($doc->code, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update return order status";
          $retry = TRUE;
        }

        if($sc === TRUE)
        {
          $arr = array(
            'line_status' => 'D',
            'update_user' => $this->user
          );

          if( ! $this->return_order_model->update_details($doc->code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update return order details";
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
    }

    $arr = array(
      'trans_id' => $trans_id,
      'code' => 200,
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'retry' => $retry,
      'return_code' => $reference
    );

    if($this->logs_json)
    {
      $logs = array(
        'trans_id' => $trans_id,
        'api_path' => $this->api_path,
        'type' => $this->type,
        'code' => $code,
        'reference' => $reference,
        'action' => 'cancel',
        'status' => $sc === TRUE ? 'success' : 'failed',
        'message' => $sc === TRUE ? 'success' : $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr),
        'req_start' => $req_start,
        'req_end' => now(TRUE),
      );

      $this->api_logs_model->add_logs($logs);
    }

    $this->response($arr, 200);
  }


  public function get_new_code($date, $prefix = 'SM', $run_digit = 5)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $prefix = empty($prefix) ? getConfig('PREFIX_RETURN_ORDER') : $prefix;
    $run_digit = empty($run_digit) ? getConfig('RUN_DIGIT_RETURN_ORDER') : $run_digit;

    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix . '-' . $Y . $M;
    $code = $this->return_order_model->get_max_code($pre);

    if (! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit * -1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0' . $run_digit . 'd', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0' . $run_digit . 'd', '001');
    }

    return $new_code;
  }



  public function verify_data($data)
  {
    if (empty($data->returnAuthNumber))
    {
      $this->error = "returnAuthNumber is required";
      return FALSE;
    }

    if (empty($data->customer_code))
    {
      $this->error = "customer_code is required";
      return FALSE;
    }

    if (empty($data->customer_name))
    {
      $this->error = "customer_name is required";
      return FALSE;
    }

    if (empty($data->doc_date))
    {
      $this->error = "doc_date is required";
      return FALSE;
    }
    
    if (! empty($data->doc_date))
    {
      if (! is_valid_date($data->doc_date))
      {
        $this->error = "Invalid doc_date";
        return FALSE;
      }
    }
    
    if (! isset($data->items))
    {
      $this->error = "Items is required";
      return FALSE;
    }
    else if (! is_array($data->items))
    {
      $this->error = "Items must be array";
      return FALSE;
    }

    return TRUE;
  }
} //--- end class
