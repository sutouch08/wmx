<?php
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Po extends REST_Controller
{
  public $error = "";
  public $api_path = "rest/api/po";
  public $logs_json = FALSE;
  public $api = FALSE;
  public $checkBackorder = FALSE;
  public $user = 'api@warrix';
  private $type = 'ADD23';

  public function __construct()
  {
    parent::__construct();
    $this->api = is_true(getConfig('IX_API'));

    if (! $this->api)
    {
      $arr = array(
        'code' => 503,
        'status' => 'error',
        'message' => "Service Unavailable",
        'retry' => FALSE
      );

      $this->response($arr, 503);
    }

    $this->load->model('rest/api/api_logs_model');
    $this->load->model('purchase/po_model');
    $this->load->model('inventory/receive_po_model');
    $this->load->model('masters/products_model');
    $this->logs_json = is_true(getConfig('IX_LOG_JSON'));
  }


  public function create_post()
  {
    $req_start = now(TRUE);
    $retry = FALSE;
    $action = 'create';
    $this->api_path = $this->api_path . "/create";
    $json = file_get_contents("php://input");
    $data = json_decode($json);

    if (empty($data))
    {
      $arr = array(
        'code' => 400,
        'status' => 'error',
        'message' => 'Empty request data',
        'retry' => FALSE
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
          'message' => 'Empty request data',
          'request_json' => $json,
          'response_json' => json_encode($arr),
          'req_start' => $req_start,
          'req_end' => now(TRUE)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $sc = $this->verify_data($data);

    //---- if any error return
    if ($sc === FALSE)
    {
      $arr = array(
        'code' => 400,
        'status' => 'error',
        'message' => $this->error,
        'retry' => FALSE
      );

      if ($this->logs_json)
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
          'response_json' => json_encode($arr),
          'req_start' => $req_start,
          'req_end' => now(TRUE)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $lineNum = [];

    if ($sc === TRUE)
    {
      foreach ($data->items as $rs)
      {
        if ($sc === FALSE)
        {
          break;
        }

        if (empty($rs->line_num) || empty($rs->sku) || empty($rs->description) || empty($rs->qty) || empty($rs->unit))
        {
          $sc = FALSE;
          $this->error = "Missing required parameter for items";
        }

        if ($sc === TRUE)
        {
          $item = $this->products_model->get($rs->sku);

          if (empty($item))
          {
            $sc = FALSE;
            $this->error = "Invalid SKU {$rs->sku}";
          }

          if (! isset($lineNum[$rs->line_num]))
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
    if ($sc === FALSE)
    {
      $arr = array(
        'code' => 400,
        'status' => 'error',
        'message' => $this->error,
        'retry' => FALSE
      );

      if ($this->logs_json)
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
          'response_json' => json_encode($arr),
          'req_start' => $req_start,
          'req_end' => now(TRUE)
        );

        $this->api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $code = NULL;    
    $doc = $this->po_model->get_by_reference($data->po_no, 'NOT_CANCEL');    
    $doc_date = empty($data->doc_date) ? date('Y-m-d') : db_date($data->doc_date, FALSE);
    $due_date = empty($data->due_date) ? date('Y-m-d') : db_date($data->due_date, FALSE);

    $this->db->trans_begin();

    if (empty($doc))
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

      if (! $po_id)
      {
        $sc = FALSE;
        $this->error = "Failed to create new PO";
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

          if (! $this->po_model->add_detail($arr))
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
            'total_qty' => $totalQty,
            'total_open_qty' => $totalQty
          );

          $this->po_model->update($code, $arr);
        }
      }
    }

    if( ! empty($doc))
    {
      $code = $doc->code;
      $action =  "update";      

      if ($doc->status == 'C')
      {
        $sc = FALSE;
        $this->error = "This document has been closed cannot be change";
        $retry = FALSE;
      }

      if($sc === TRUE)
      {
        $arr = array(
          'vender_code' => $data->vender_code,
          'vender_name' => $data->vender_name,
          'doc_date' => $doc_date,
          'due_date' => $due_date,
          'remark' => empty($data->remark) ? NULL : get_null($data->remark),
          'is_api' => 1,
          'update_user' => $this->user
        );

        if (! $this->po_model->update($doc->code, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update PO";
          $retry = TRUE;
        }
      }      

      if ($sc === TRUE)
      {
        if ($doc->status == 'O' && $this->po_model->is_all_open($doc->code) && $this->receive_po_model->is_all_not_received($doc->code))
        {
          //--- if all details is open
          //--- drop current details
          if (! $this->po_model->delete_all_details($doc->code))
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

              if ($rs->qty > 0)
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

                if (! $this->po_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to insert item row at line num {$rs->line_num}";
                  $retry = TRUE;
                }

                $totalQty += $rs->qty;
              }
            }

            if ($sc === TRUE)
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

          foreach ($data->items  as $rs)
          {
            if ($sc === FALSE)
            {
              break;
            }

            $row = $this->po_model->get_detail_by_product_and_line_num($doc->code, $rs->sku, $rs->line_num);

            if (! empty($row))
            {
              $is_received = $this->receive_po_model->is_exists_po_detail($row->id);

              if ($rs->qty > 0)
              {
                if ($row->line_status == 'O')
                {
                  if ($rs->qty != $row->qty)
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
                  $retry = FALSE;
                }
              }
              else
              {
                //--- to cancel
                if (($row->line_status == 'O' or $row->line_status == 'D') && ! $is_received)
                {
                  $del_rows_id[] = $row->id;
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Po line number {$rs->line_num} has been received cannot be change";
                  $retry = FALSE;
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
          if ($sc === TRUE && ! empty($update_rows))
          {
            foreach ($update_rows as $ru)
            {
              if ($sc === FALSE)
              {
                break;
              }

              $arr = (array) $ru;

              if (! $this->po_model->update_detail($ru->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update item row at line number {$ru->line_num}";
                $retry = TRUE;
              }
            }
          } //-- end update rows

          //--- add new rows
          if ($sc === TRUE && ! empty($add_rows))
          {
            foreach ($add_rows as $ra)
            {
              if ($sc === FALSE)
              {
                break;
              }

              $arr = (array) $ra;

              if (! $this->po_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to insert new item row at line number {$ra->line_num}";
                $retry = TRUE;
              }
            }
          } //--end add new rows

          //--- delete rows
          if ($sc === TRUE && ! empty($del_rows_id))
          {
            if (! $this->po_model->delete_rows_id($del_rows_id))
            {
              $sc = FALSE;
              $this->error = "Failed to delete item rows";
              $retry = TRUE;
            }
          } //--- end delete rows

          if ($sc === TRUE)
          {
            $this->po_model->recal_total($doc->code);
          }
        } //-- end doc->status == 'O'
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

    $arr = array(
      'code' => 200,
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'retry' => $retry,
      'doc_code' => isset($code) ? $code : NULL
    );

    if($this->logs_json)
    {
      $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' => $this->type,
        'code' => $data->po_no,
        'reference' => isset($code) ? $code : NULL,
        'action' => isset($action) ? $action : NULL,
        'status' => $sc === TRUE ? 'success' : 'failed',
        'message' => $sc === TRUE ? 'success' : $this->error,
        'request_json' => isset($json) ? $json : NULL,
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
    $action = 'cancel';
    $code = NULL;
    $retry = FALSE;
    $this->api_path = $this->api_path . '/cancel';
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    if (empty($data) || empty($data->po_no))
    {
      $this->error = "Empty request data";
      $arr = array(
        'code' => 400,
        'status' => 'error',
        'message' => $this->error,
        'retry' => FALSE
      );

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => empty($data->po_no) ? '' : $data->po_no,
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

    if (empty($data->cancel_reason) || empty($data->cancel_user))
    {
      $this->error = "cancel_reason and cancel_user are required";
      $arr = array(
        'code' => 400,
        'status' => 'error',
        'message' => $this->error,
        'retry' => FALSE
      );

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => empty($data->po_no) ? '' : $data->po_no,
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


    $doc = $this->po_model->get_by_reference($data->po_no, 'NOT_CANCEL');

    if (! empty($doc))
    {
      $code = $doc->code;

      if ($doc->status === 'C')
      {
        $sc = FALSE;
        $this->error = "PO {$data->po_no} already closed cannot be canceled";
        $retry = FALSE;
      }

      if ($sc === TRUE && $this->po_model->is_received($doc->code))
      {
        $sc = FALSE;
        $this->error = "PO {$data->po_no} has been received and cannot be canceled";
        $retry = FALSE;
      }

      if ($sc === TRUE && $this->receive_po_model->is_exists_po_ref($data->po_no))
      {
        $sc = FALSE;
        $this->error = "PO {$data->po_no} has associated received items and cannot be canceled";
        $retry = FALSE;
      }

      if ($sc === TRUE)
      {
        $arr = array(
          'status' => 'D',
          'cancel_reason' => $data->cancel_reason,
          'cancel_user' => $data->cancel_user,
          'cancel_date' => now(),
        );

        $this->db->trans_begin();
        if (! $this->po_model->update($doc->code, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to cancel PO {$data->po_no}";
          $retry = TRUE;
        }

        if ($sc === TRUE)
        {
          $arr = array(
            'line_status' => 'D',
            'update_user' => $this->user
          );

          if (! $this->po_model->update_details($doc->code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update PO details for {$data->po_no}";
            $retry = TRUE;
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
      }
    }

    $arr = array(
      'code' => 200,
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'retry' => $retry,
      'doc_code' => $code
    );

    if ($this->logs_json)
    {
      $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' => $this->type,
        'action' => $action,
        'code' => $data->po_no,
        'reference' => $code,
        'status' => $sc === TRUE ? 'success' : 'failed',
        'message' => $sc === TRUE ? 'success' : $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr),
        'req_start' => $req_start,
        'req_end' => now(TRUE)
      );

      $this->api_logs_model->add_logs($logs);
    }

    $this->response($arr, 200);
  }

  public function get_new_code($date, $prefix = 'PO', $run_digit = 5)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $prefix = empty($prefix) ? getConfig('PREFIX_PO') : $prefix;
    $run_digit = empty($run_digit) ? getConfig('RUN_DIGIT_PO') : $run_digit;

    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $pre = $prefix . '-' . $Y . $M;
    $code = $this->po_model->get_max_code($pre);

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
    if (empty($data->po_no))
    {
      $this->error = "po_no is required";
      return FALSE;
    }

    if (! empty($data->po_no))
    {
      $option = 'NOT_CANCEL'; //-- NOT_CANCEL = Only not cancel po, ALL = all document releate to reference
      $doc = $this->po_model->get_by_reference($data->po_no, $option);

      if (! empty($doc))
      {
        if ($doc->status == 'C')
        {
          $this->error = "PO {$data->po_no} already closed cannot be change";
          return FALSE;
        }
      }
    }

    if (empty($data->vender_code))
    {
      $this->error = "vender_code is required";
      return FALSE;
    }

    if (empty($data->vender_name))
    {
      $this->error = "vender_name is required";
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

    if (empty($data->items))
    {
      $this->error = "Items is required";
      return FALSE;
    }

    return TRUE;
  }

} //--- end class
