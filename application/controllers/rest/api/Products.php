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
    $fields = ['code', 'name', 'item_type'];

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

      if( ! empty($ds->barcode))
      {
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
      }


      if($sc === TRUE)
      {
        $arr = array(
          'code' => trim($ds->code),
          'name' => trim($ds->name),
          'barcode' => empty($ds->barcode) ? NULL : get_null(trim($ds->barcode)),
          'model_code' => empty($ds->model_code) ? NULL : get_null($ds->model_code),
          'color_code' => empty($ds->color_code) ? NULL : get_null($ds->color_code),
          'size_code' => empty($ds->size_code) ? NULL : get_null($ds->size_code),
          'main_group_code' => empty($ds->main_group_code) ? NULL : get_null($ds->main_group_code),
          'main_group_name' => empty($ds->main_group_name) ? NULL : get_null($ds->main_group_name),
          'group_code' => empty($ds->group_code) ? NULL : get_null($ds->group_code),
          'group_name' => empty($ds->model_name) ? NULL : get_null($ds->group_name),
          'segment_code' => empty($ds->segment_code) ? NULL : get_null($ds->segment_code),
          'segment_name' => empty($ds->segment_name) ? NULL : get_null($ds->segment_name),
          'class_code' => empty($ds->class_code) ? NULL : get_null($ds->class_code),
          'class_name' => empty($ds->class_name) ? NULL : get_null($ds->class_name),
          'family_code' => empty($ds->family_code) ? NULL : get_null($ds->family_code),
          'family_name' => empty($ds->family_name) ? NULL : get_null($ds->family_name),
          'type_code' => empty($ds->type_code) ? NULL : get_null($ds->type_code),
          'type_name' => empty($ds->type_name) ? NULL : get_null($ds->type_name),
          'kind_code' => empty($ds->kind_code) ? NULL : get_null($ds->kind_code),
          'kind_name' => empty($ds->kind_name) ? NULL : get_null($ds->kind_name),
          'gender_code' => empty($ds->gender_code) ? NULL : get_null($ds->gender_code),
          'gender_name' => empty($ds->gender_name) ? NULL : get_null($ds->gender_name),
          'sport_type_code' => empty($ds->sport_type_code) ? NULL : get_null($ds->sport_type_code),
          'sport_type_name' => empty($ds->sport_type_name) ? NULL : get_null($ds->sport_type_name),
          'collection_code' => empty($ds->collection_code) ? NULL : get_null($ds->collection_code),
          'collection_name' => empty($ds->collection_name) ? NULL : get_null($ds->collection_name),
          'brand_code' => empty($ds->brand_code) ? NULL : get_null($ds->brand_code),
          'brand_name' => empty($ds->brand_name) ? NULL : get_null($ds->brand_name),
          'year' => empty($ds->year) ? NULL : $ds->year,
          'cost' => empty($ds->cost) ? 0.00 : round($ds->cost, 2),
          'price' => empty($ds->price) ? 0.00 : round($ds->price, 2),
          'unit_code' => empty($ds->unit_code) ? NULL : $ds->unit_code,
          'item_type' => empty($ds->item_type) ? 'I' : $ds->item_type,
          'count_stock' => empty($ds->item_type) ? 1 : ($ds->item_type == 'S' ? 0 : 1),
          'active' => empty($ds->active) ? 0 : 1,
          'is_api' => empty($ds->is_api) ? 0 : 1,
          'api_rate' => empty($ds->api_rate) ? 0.00 : get_zero($ds->api_rate)
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
