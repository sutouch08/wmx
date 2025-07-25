<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Return_order extends REST_Controller
{
  public $error;
  public $user;
	public $api_path = "rest/api/return_order";
	public $log_json = FALSE;
	public $api = FALSE;
  public $checkBackorder = FALSE;
  private $type = 'ADD90';

  public function __construct()
  {
    parent::__construct();
		$this->api = is_true(getConfig('IX_API'));

		if($this->api)
		{
      $this->load->model('rest/api/api_logs_model');

	    $this->load->model('inventory/return_order_model');
	    $this->load->model('masters/products_model');
      $this->load->model('masters/warehouse_model');
      $this->load->model('masters/zone_model');

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
            'code' => $data->returnAuthNumber,
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

      if( ! empty($data->items) && ! $is_cancel)
      {
        $lineNum = [];

        $i = 0;

        foreach($data->items as $rs)
        {
          if($sc === FALSE) { break; }

          if(empty($rs->line_num))
          {
            $sc = FALSE;
            $this->error = "Missing required parameter : Items[{$i}].line_num";
          }

          if(empty($rs->sku))
          {
            $sc = FALSE;
            $this->error = "Missing required parameter : Items[{$i}].sku";
          }

          if(empty($rs->description))
          {
            $sc = FALSE;
            $this->error = "Missing required parameter : Items[{$i}].description";
          }

          if(empty($rs->qty) OR $rs->qty <= 0)
          {
            $sc = FALSE;
            $this->error = "Missing required parameter : Items[{$i}].qty OR qty <= 0";
          }

          if(empty($rs->unit))
          {
            $sc = FALSE;
            $this->error = "Missing required parameter : Items[{$i}].unit";
          }

          if($sc === TRUE)
          {
            if( ! isset($lineNum[$rs->line_num]))
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
            'code' => $data->returnAuthNumber,
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
        $doc = $this->return_order_model->get_by_active_reference($data->returnAuthNumber);

        if( ! empty($doc))
        {
          $code = $doc->code;
          
          if($sc === TRUE && $doc->status == 'C')
          {
            $sc = FALSE;
            $this->error = "{$data->returnAuthNumber} already closed cannot be change";
          }

          if($sc === TRUE && $doc->status == 'O')
          {
            $sc = FALSE;
            $this->error = "{$data->returnAuthNumber} already in progress cannot be change";
          }

          if($sc === TRUE && $is_cancel && $this->return_order_model->is_received($doc->code))
          {
            $sc = FALSE;
            $this->error = "This document has been received cannot be cancel";
          }

          if($sc === TRUE && $is_cancel)
          {
            $action = 'cancel';

            $arr = array(
              'status' => 'D',
              'cancel_reason' => NULL,
              'cancel_user' => $this->user,
              'cancel_date' => now()
            );

            if( ! $this->return_order_model->update($doc->code, $arr))
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

              if( ! $this->return_order_model->update_details($doc->code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update item rows status";
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
              'code' => $data->returnAuthNumber,
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
          $zone = $this->zone_model->get(getConfig('IX_RETURN_ZONE'));

          $this->db->trans_begin();

          if(empty($doc))
          {
            $code = $this->get_new_code($doc_date);
            //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
            $arr = array(
              'code' => $code,
              'reference' => $data->returnAuthNumber,
              'referenceId' => genUid(),
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

            if( ! $return_id)
            {
              $sc = FALSE;
              $this->error = "Failed to create new document";
            }

            if($sc === TRUE)
            {
              $totalQty = 0;

              foreach($data->items as $rs)
              {
                if($sc === FALSE) { break; }

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

                if( ! $this->return_order_model->add_detail($arr))
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
                  'total_qty' => $totalQty
                );

                $this->return_order_model->update($code, $arr);
              }
            }
          }
          else
          {
            $code = $doc->code;

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

            if( ! $this->return_order_model->update($doc->code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update document";
            }

            if($sc === TRUE)
            {
              //--- drop current details
              if( ! $this->return_order_model->drop_details($doc->code))
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

                  if( ! $this->return_order_model->add_detail($arr))
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
                    'total_qty' => $totalQty
                  );

                  $this->return_order_model->update($doc->code, $arr);
                }
              } //--- end if($sc === TRUE)
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
              'code' => $data->returnAuthNumber,
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
              'code' => $data->returnAuthNumber,
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


  public function get_new_code($date, $prefix = 'SM', $run_digit = 5)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $prefix = empty($prefix) ? getConfig('PREFIX_RETURN_ORDER') : $prefix;
    $run_digit = empty($run_digit) ? getConfig('RUN_DIGIT_RETURN_ORDER') : $run_digit;

    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_order_model->get_max_code($pre);

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
    if(empty($data->returnAuthNumber))
    {
      $this->error = "returnAuthNumber is required";
      return FALSE;
    }

    if(empty($data->customer_code))
    {
      $this->error = "customer_code is required";
      return FALSE;
    }

    if(empty($data->customer_name))
    {
      $this->error = "customer_name is required";
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

    if( ! isset($data->items))
    {
      $this->error = "Items is required";
      return FALSE;
    }
    else if( ! is_array($data->items))
    {
      $this->error = "Items must be array";
      return FALSE;
    }

		return TRUE;
	}
} //--- end class
