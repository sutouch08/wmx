<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Products extends REST_Controller
{
  public $error;
  private $user;
	private $api_path = "rest/api/products";
	public $logs;
	private $log_json = FALSE;
	private $api = FALSE;

  public function __construct()
  {
    parent::__construct();

    $this->api = is_true(getConfig('IX_API'));

    if($this->api)
    {
      $this->logs = $this->load->database('logs', TRUE);
      $this->load->model('rest/api/api_logs_model');
      $this->user = 'api@warrix';
      $this->logs_json = is_true(getConfig('IX_LOG_JSON'));
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
    $this->load->model('masters/products_model');
    $type = "ITEM";
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
    $fields = ['code', 'barcode', 'name'];

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
      $id = $this->products_model->get_id(trim($ds->code));

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
            'code' => $ds->code,
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
          'code' => trim($ds->code),
          'barcode' => trim($ds->barcode),
          'name' => trim($ds->name),
          'style_code' => get_null(trim($ds->model_code)),
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
          'unit_code' => empty($ds->unit_code) ? 'PCS': trim($ds->unit_code),
          'cost' => round(trim($ds->cost), 2),
          'price' => round(trim($ds->price), 2)
        );

        if(empty($id))
        {
          if( ! $this->products_model->add($arr))
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
                'code' => $ds->code,
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
          if( ! $this->products_model->update_by_id($id, $arr))
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
                'code' => $ds->code,
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
            'sku' => $ds->code
          );

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => $trans_id,
              'api_path' => $this->api_path,
              'type' =>'ITEM',
              'code' => $ds->code,
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


  public function model_post()
  {
    $sc = TRUE;
    $this->load->model('masters/product_style_model');
    $this->api_path = "rest/api/products/model";
    $type = "ITEM MODEL";
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
    $fields = ['code', 'name'];

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
      $cs = $this->product_style_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->product_style_model->add($arr))
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
              'type' => $type,
              'code' => $ds->code,
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
        if( ! $this->product_style_model->update($cs->code, $arr))
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
              'type' => $type,
              'code' => $ds->code,
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
          'code' => $ds->code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $ds->code,
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
  } //--- end method


  public function brand_post()
  {
    $sc = TRUE;
    $this->load->model('masters/product_brand_model');
    $this->api_path = "rest/api/products/brand";
    $type = "ITEM BRAND";
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
    $fields = ['code', 'name'];

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
      $cs = $this->product_brand_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->product_brand_model->add($arr))
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
              'type' => $type,
              'code' => $ds->code,
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
        if( ! $this->product_brand_model->update($cs->code, $arr))
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
              'type' => $type,
              'code' => $ds->code,
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
          'code' => $ds->code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $ds->code,
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
  } //--- end method


  public function category_post()
  {
    $sc = TRUE;
    $this->load->model('masters/product_category_model');
    $this->api_path = "rest/api/products/category";
    $type = "ITEM CATEGORY";
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
    $fields = ['code', 'name'];

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
      $cs = $this->product_category_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->product_category_model->add($arr))
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
              'type' => $type,
              'code' => $ds->code,
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
        if( ! $this->product_category_model->update($cs->code, $arr))
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
              'type' => $type,
              'code' => $ds->code,
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
          'code' => $ds->code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $ds->code,
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
  } //--- end method


  public function collection_post()
  {
    $sc = TRUE;
    $this->load->model('masters/product_collection_model');
    $this->api_path = "rest/api/products/collection";
    $type = "ITEM COLLECTION";
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
    $fields = ['code', 'name'];

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
      $cs = $this->product_collection_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->product_collection_model->add($arr))
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
              'type' => $type,
              'code' => $ds->code,
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
        if( ! $this->product_collection_model->update($cs->code, $arr))
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
              'type' => $type,
              'code' => $ds->code,
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
          'code' => $ds->code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $ds->code,
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
  } //--- end method


  public function group_post()
  {
    $sc = TRUE;
    $this->load->model('masters/product_group_model');
    $this->api_path = "rest/api/products/group";
    $type = "ITEM GROUP";
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
    $fields = ['code', 'name'];

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
      $cs = $this->product_group_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->product_group_model->add($arr))
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
              'type' => $type,
              'code' => $ds->code,
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
        if( ! $this->product_group_model->update($cs->code, $arr))
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
              'type' => $type,
              'code' => $ds->code,
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
          'code' => $ds->code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $ds->code,
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
  } //--- end method


  public function main_group_post()
  {
    $sc = TRUE;
    $this->load->model('masters/product_main_group_model');
    $this->api_path = "rest/api/products/main_group";
    $type = "ITEM MAIN GROUP";
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
    $fields = ['code', 'name'];

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
      $cs = $this->product_main_group_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->product_main_group_model->add($arr))
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
              'type' => $type,
              'code' => $ds->code,
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
        if( ! $this->product_main_group_model->update($cs->code, $arr))
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
              'type' => $type,
              'code' => $ds->code,
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
          'code' => $ds->code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $ds->code,
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
  } //--- end method


  public function sub_group_post()
  {
    $sc = TRUE;
    $this->load->model('masters/product_sub_group_model');
    $this->api_path = "rest/api/products/sub_group";
    $type = "ITEM SUB GROUP";
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
    $fields = ['code', 'name'];

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
      $cs = $this->product_sub_group_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->product_sub_group_model->add($arr))
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
              'type' => $type,
              'code' => $ds->code,
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
        if( ! $this->product_sub_group_model->update($cs->code, $arr))
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
              'type' => $type,
              'code' => $ds->code,
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
          'code' => $ds->code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $ds->code,
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
  } //--- end method


  public function kind_post()
  {
    $sc = TRUE;
    $this->load->model('masters/product_kind_model');
    $this->api_path = "rest/api/products/kind";
    $type = "ITEM KIND";
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
    $fields = ['code', 'name'];

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
      $cs = $this->product_kind_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->product_kind_model->add($arr))
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
              'type' => $type,
              'code' => $ds->code,
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
        if( ! $this->product_kind_model->update($cs->code, $arr))
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
              'type' => $type,
              'code' => $ds->code,
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
          'code' => $ds->code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $ds->code,
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
  } //--- end method


  public function type_post()
  {
    $sc = TRUE;
    $this->load->model('masters/product_type_model');
    $this->api_path = "rest/api/products/type";
    $type = "ITEM TYPE";
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
    $fields = ['code', 'name'];

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
      $cs = $this->product_type_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->product_type_model->add($arr))
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
              'type' => $type,
              'code' => $ds->code,
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
        if( ! $this->product_type_model->update($cs->code, $arr))
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
              'type' => $type,
              'code' => $ds->code,
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
          'code' => $ds->code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => $trans_id,
            'api_path' => $this->api_path,
            'type' => $type,
            'code' => $ds->code,
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
  } //--- end method


}


 ?>
