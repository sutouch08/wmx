<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Customers extends REST_Controller
{
  public $error;
  private $user;
	private $api_path = "rest/api/customers";
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
      $this->load->model('masters/customers_model');
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
          'type' =>'ITEM',
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
    $fields = ['sku', 'barcode', 'name'];

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
            'type' =>'ITEM',
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
    } //--- end forech check required fields

    if($sc === TRUE)
    {
      $id = $this->products_model->get_id(trim($ds->sku));

      if($this->products_model->is_exists_barcode(trim($ds->barcode), $id))
      {
        $sc = FALSE;

        $this->error = "Barcode {$ds->barcode} already exists with another SKU";

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
            'type' =>'ITEM',
            'code' => $ds->sku,
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

      if($sc === TRUE)
      {
        $arr = array(
          'code' => trim($ds->sku),
          'barcode' => trim($ds->barcode),
          'name' => trim($ds->name),
          'model_code' => get_null(trim($ds->model_code)),
          'color_code' => get_null(trim($ds->color_code)),
          'size_code' => get_null(trim($ds->size_code)),
          'main_group_code' => get_null(trim($ds->main_group_code)),
          'group_code' => get_null(trim($ds->group_code)),
          'sub_group_code' => get_null(trim($ds->sub_group_code)),
          'category_code' => get_null(trim($ds->category_code)),
          'kind_code' => get_null(trim($ds->kind_code)),
          'type_code' => get_null(trim($ds->type_code)),
          'brand_code' => get_null(trim($ds->brand_code)),
          'collection_code' => get_null(trim($ds->collection_code)),
          'year' => get_null(trim($ds->year)),
          'unit_code' => get_null(trim($ds->unit_code)),
          'cost' => get_null(trim($ds->cost)),
          'price' => get_null(trim($ds->price))
        );

        if(empty($id))
        {
          $id = $this->products_model->add($arr);

          if( ! $id)
          {
            $sc = FALSE;
            $this->error = "Insert failed";

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
                'type' =>'ITEM',
                'code' => $ds->sku,
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
        else
        {
          if( ! $this->products_model->update($id, $arr))
          {
            $sc = FALSE;
            $this->error = "Update failed";

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
                'type' =>'ITEM',
                'code' => $ds->sku,
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

        //--- if insert or update success
        if($sc === TRUE)
        {
          $arr = array(
            'trans_id' => $trans_id,
            'status' => TRUE,
            'message' => 'success',
            'id' => $id
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => $trans_id,
              'api_path' => $this->api_path,
              'type' =>'ITEM',
              'code' => $ds->sku,
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
      }
    }
  }
}


 ?>
