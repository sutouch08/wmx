<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Inbound extends REST_Controller
{
  public $error;
  private $user;
	private $api_path = "rest/api/inbound";
	public $logs;
	private $log_json = FALSE;
	private $api = FALSE;

  public function __construct()
  {
    parent::__construct();

    $this->api = is_true(getConfig('WMS_API'));

    if($this->api)
    {
      $this->logs = $this->load->database('logs', TRUE);
      $this->load->model('rest/api/api_logs_model');
      $this->load->model('masters/products_model');
      $this->load->model('masters/warehouse_model');
      $this->load->model('inventory/inbound_model');
      $this->load->library('documents');
      $this->user = 'api@warrix';
      $this->logs_json = is_true(getConfig('WMS_LOGS_JSON'));
    }
    else
    {
      $arr = array(
				'status' => FALSE,
				'error' => "Api is disabled"
			);

			$this->response($arr, 400);
    }
  }


  public function index_post()
  {
    $sc = TRUE;

    $trans_id = genUid();
    $action = "create";
    $type = "INBOUND";

    $json = file_get_contents('php://input');

    $ds = json_decode($json);

    if(empty($ds))
    {
      if($this->logs_json)
      {
        $arr = array(
          'trans_id' => $trans_id,
          'status' => FALSE,
          'error' => 'empty data'
        );

        $logs = array(
          'trans_id' => $trans_id,
          'api_path' => $this->api_path,
          'type' => $type,
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

    //--- required fields
    $fields = ['order_no', 'order_date', 'warehouse_code', 'vendor_code', 'vendor_name', 'items'];

    $order_no = empty($ds->order_no) ? NULL : $ds->order_no;

    //--- check required fields
    foreach($fields as $field)
    {
      if( ! property_exists($ds, $field) OR $ds->$field == '')
      {
        $sc = FALSE;

        $this->error = "Missing required parameter : '{$field}'";

        $arr = array(
          'trans_id' => $trans_id,
          'status' => FALSE,
          'error' => $this->error
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $order_no,
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
    } //--- end forech check required fields

    if($sc === TRUE)
    {
      $warehouse = $this->warehouse_model->get_by_code($ds->warehouse_code);

      if( ! empty($warehouse))
      {
        $ds->warehouse_id = $warehouse->id;
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid warehouse_code : {$ds->warehouse_code}";

        $arr = array(
          'trans_id' => $trans_id,
          'status' => FALSE,
          'error' => $this->error
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $order_no,
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
    }

    $details = [];

    //--- check items field
    if($sc === TRUE)
    {
      if( ! empty($ds->items))
      {
        $line = 1;

        foreach($ds->items as $row)
        {
          if($sc === FALSE)
          {
            break;
          }

          if( ! empty($row->sku) && ! empty($row->qty))
          {
            $item = $this->products_model->get_by_code(trim($row->sku));

            if( ! empty($item))
            {
              $details[] = (object) array(
                'line_no' => $line,
                'product_id' => $item->id,
                'product_code' => $item->code,
                'product_name' => $item->name,
                'unit_code' => $item->unit_code,
                'qty' => $row->qty
              );

              $line++;
            }
            else
            {
              $sc = FALSE;
              $this->error = "Item SKU not found or invalid SKU : {$row->sku}";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Missing required parameter : ".(empty($row->sku) ? "items.sku @ line {$line}" : "qty or qty less or equal to 0");
          }
        }
      }

      if($sc === FALSE)
      {
        $arr = array(
          'trans_id' => $trans_id,
          'status' => FALSE,
          'error' => $this->error
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $order_no,
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
    }


    if($sc === TRUE)
    {
      if( ! empty($details))
      {
        $code = NULL;
        $id = NULL;
        //--- check exists document
        $doc = $this->inbound_model->get_active_order_no($order_no);

        $this->db->trans_begin();

        if(empty($doc))
        {
          $code = $this->documents->get_new_code('IB');

          if(empty($code))
          {
            //--- retry when code is null
            $code = $this->documents->get_new_code('IB');
          }

          if( ! empty($code))
          {
            $arr = array(
              'code' => $code,
              'order_no' => $ds->order_no,
              'order_type' => $ds->order_type,
              'order_date' => $ds->order_date,
              'vendor_code' => trim($ds->vendor_code),
              'vendor_name' => trim($ds->vendor_name),
              'ref_no1' => get_null(trim($ds->ref_no1)),
              'ref_no2' => get_null(trim($ds->ref_no2)),
              'warehouse_id' => $ds->warehouse_id,
              'warehouse_code' => $ds->warehouse_code,
              'remark' => empty($ds->remark) ? NULL : get_null(trim($ds->remark))
            );

            $id = $this->inbound_model->add($arr);

            if( ! $id)
            {
              $sc = FALSE;
              $this->error = "Failed to create document";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Failed to generate document number";
          }
        }
        else
        {
          $id = $doc->id;
          $code = $doc->code;
          $action = "update";

          if($doc->status == 'P')
          {
            $arr = array(
              'order_date' => $ds->order_date,
              'vendor_code' => trim($ds->vendor_code),
              'vendor_name' => trim($ds->vendor_name),
              'ref_no1' => get_null(trim($ds->ref_no1)),
              'ref_no2' => get_null(trim($ds->ref_no2)),
              'warehouse_id' => $ds->warehouse_id,
              'remark' => empty($ds->remark) ? NULL : get_null(trim($ds->remark))
            );

            if( ! $this->inbound_model->update($doc->id, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to update document : Cannot update document header";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Failed to updte document : Document already ". ($doc->status == 'D' ? 'canceled.' : ($doc->status == 'C' ? 'completed.' : 'in progress.'));
          }
        }

        if($sc === TRUE)
        {
          if( ! empty($doc))
          {
            //---- remove current details
            if( ! $this->inbound_model->delete_details($doc->id))
            {
              $sc = FALSE;
              $this->error = "Cannot delete previous document rows";
            }
          }

          //--- process new rows
          if($sc === TRUE)
          {
            foreach($details as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              $arr = array(
                'receive_id' => $id,
                'receive_code' => $code,
                'line_no' => $rs->line_no,
                'product_id' => $rs->product_id,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'unit_code' => $rs->unit_code,
                'qty' => $rs->qty
              );

              if( ! $this->inbound_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to insert document row";
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
        $sc = FALSE;
        $this->error = "Items rows not found";
      }

      //--- if insert or update success
      if($sc === TRUE)
      {
        $arr = array(
          'trans_id' => $trans_id,
          'status' => TRUE,
          'message' => 'success',
          'code' => $code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
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
        $arr = array(
          'trans_id' => $trans_id,
          'status' => FALSE,
          'message' => $this->error,
          'code' => $code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
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
    }
  }
}

 ?>
