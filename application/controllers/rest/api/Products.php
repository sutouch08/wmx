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
    $type = "INT10";
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

      if($sc === TRUE)
      {
        $this->update_product_attribute($ds);
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
          $action = "update";

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
            'sku' => $ds->code
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
    }
  }


  public function price_list_post()
  {
    $sc = TRUE;
    $this->load->model('masters/products_model');
    $type = "INT11";
    $trans_id = genUid();
    $action = "update";
    $this->api_path = $this->api_path."/price_list";

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

    //--- check required fields
    if( ! property_exists($ds, 'items') OR ! is_array($ds->items))
    {
      $sc = FALSE;

      if( ! property_exists($ds, 'items'))
      {
        $this->error = "Missing required parameter : items";
      }
      else
      {
        $this->error = "items must be array";
      }

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

    if($sc === TRUE)
    {
      foreach($ds->items as $item)
      {
        if($sc === FALSE) { break; }

        if(empty($item->code))
        {
          $sc = FALSE;
          $this->error = "Missing required parameter : items.code";
        }

        if($sc === TRUE)
        {
          if( ! isset($item->cost) OR ! is_numeric($item->cost))
          {
            $sc = FALSE;
            $this->error = "Cost must be number @{$item->code}";
          }
        }

        if($sc === TRUE)
        {
          if( ! isset($item->price) OR ! is_numeric($item->price))
          {
            $sc = FALSE;
            $this->error = "Price must be number @{$item->code}";
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
    }

    $failed_list = [];
    $count = 0;
    $success = 0;
    $failed = 0;

    if($sc === TRUE)
    {
      foreach($ds->items as $item)
      {
        $count++;

        $arr = array(
          'cost' => $item->cost,
          'price' => $item->price
        );

        if( ! $this->products_model->update($item->code, $arr))
        {
          $failed++;
          $failed_list[] = array('code' => $item->code, 'status' => 'failed');
        }
        else
        {
          $success++;
        }
      }
    }

    if($sc === TRUE)
    {
      $arr = array(
        'trans_id' => $trans_id,
        'status' => TRUE,
        'message' => 'success',
        'count' => $count,
        'success' => $success,
        'failed' => $failed,
        'failed_items' => empty($failed_list) ? NULL : $failed_list
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => $trans_id,
          'api_path' => $this->api_path,
          'type' => $type,
          'code' => NULL,
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
  } //-- end


  private function update_product_attribute($ds)
  {
    if( ! empty($ds))
    {
      //--- model
      if( ! empty($ds->model_code && ! empty($ds->model_name)))
      {
        $this->load->model('masters/product_model_model');

        if( ! $this->product_model_model->is_exists($ds->model_code))
        {
          $this->product_model_model->add(['code' => $ds->model_code, 'name' => $ds->model_name]);
        }
        else
        {
          $this->product_model_model->update($ds->model_code, ['name' => $ds->model_name]);
        }
      }

      //--- main group
      if( ! empty($ds->main_group_code) && ! empty($ds->main_group_name))
      {
        $this->load->model('masters/product_main_group_model');

        if( ! $this->product_main_group_model->is_exists($ds->main_group_code))
        {
          $this->product_main_group_model->add(['code' => $ds->main_group_code, 'name' => $ds->main_group_name]);
        }
        else
        {
          $this->product_main_group_model->update($ds->main_group_code, ['name' => $ds->main_group_name]);
        }
      }

      //--- group
      if( ! empty($ds->group_code) && ! empty($ds->group_code))
      {
        $this->load->model('masters/product_group_model');

        if( ! $this->product_group_model->is_exists($ds->group_code))
        {
          $this->product_group_model->add(['code' => $ds->group_code, 'name' => $ds->group_name]);
        }
        else
        {
          $this->product_group_model->update($ds->group_code, ['name' => $ds->group_name]);
        }
      }

      //--- segment
      if( ! empty($ds->segment_code) && ! empty($ds->segment_name))
      {
        $this->load->model('masters/product_segment_model');

        if( ! $this->product_segment_model->is_exists($ds->segment_code))
        {
          $this->product_segment_model->add(['code' => $ds->segment_code, 'name' => $ds->segment_name]);
        }
        else
        {
          $this->product_segment_model->update($ds->segment_code, ['name' => $ds->segment_name]);
        }
      }

      //-- class
      if( ! empty($ds->class_code) && ! empty($ds->class_name))
      {
        $this->load->model('masters/product_class_model');

        if( ! $this->product_class_model->is_exists($ds->class_code))
        {
          $this->product_class_model->add(['code' => $ds->class_code, 'name' => $ds->class_name]);
        }
        else
        {
          $this->product_class_model->update($ds->class_code, ['name' => $ds->class_name]);
        }
      }

      //--- family
      if( ! empty($ds->family_code) && ! empty($ds->family_name))
      {
        $this->load->model('masters/product_family_model');

        if( ! $this->product_family_model->is_exists($ds->family_code))
        {
          $this->product_family_model->add(['code' => $ds->family_code, 'name' => $ds->family_name]);
        }
        else
        {
          $this->product_family_model->update($ds->family_code, ['name' => $ds->family_name]);
        }
      }

      //--- type
      if( ! empty($ds->type_code) && ! empty($ds->type_name))
      {
        $this->load->model('masters/product_type_model');

        if( ! $this->product_type_model->is_exists($ds->type_code))
        {
          $this->product_type_model->add(['code' => $ds->type_code, 'name' => $ds->type_name]);
        }
        else
        {
          $this->product_type_model->update($ds->type_code, ['name' => $ds->type_name]);
        }
      }

      //--- kind
      if( ! empty($ds->kind_code) && ! empty($ds->kind_name))
      {
        $this->load->model('masters/product_kind_model');

        if( ! $this->product_kind_model->is_exists($ds->kind_code))
        {
          $this->product_kind_model->add(['code' => $ds->kind_code, 'name' => $ds->kind_name]);
        }
        else
        {
          $this->product_kind_model->update($ds->kind_code, ['name' => $ds->kind_name]);
        }
      }

      //--- gender
      if( ! empty($ds->gender_code) && ! empty($ds->gender_name))
      {
        $this->load->model('masters/product_gender_model');

        if( ! $this->product_gender_model->is_exists($ds->gender_code))
        {
          $this->product_gender_model->add(['code' => $ds->gender_code, 'name' => $ds->gender_name]);
        }
        else
        {
          $this->product_gender_model->update($ds->gender_code, ['name' => $ds->gender_name]);
        }
      }

      //--- sport type
      if( ! empty($ds->sport_type_code) && ! empty($ds->sport_type_name))
      {
        $this->load->model('masters/product_sport_type_model');

        if( ! $this->product_sport_type_model->is_exists($ds->sport_type_code))
        {
          $this->product_sport_type_model->add(['code' => $ds->sport_type_code, 'name' => $ds->sport_type_name]);
        }
        else
        {
          $this->product_sport_type_model->update($ds->sport_type_code, ['name' => $ds->sport_type_name]);
        }
      }

      //--- collection
      if( ! empty($ds->collection_code) && ! empty($ds->collection_name))
      {
        $this->load->model('masters/product_collection_model');

        if( ! $this->product_collection_model->is_exists($ds->collection_code))
        {
          $this->product_collection_model->add(['code' => $ds->collection_code, 'name' => $ds->collection_name]);
        }
        else
        {
          $this->product_collection_model->update($ds->collection_code, ['name' => $ds->collection_name]);
        }
      }

      //--- brand
      if( ! empty($ds->brand_code) && ! empty($ds->brand_name))
      {
        $this->load->model('masters/product_brand_model');

        if( ! $this->product_brand_model->is_exists($ds->brand_code))
        {
          $this->product_brand_model->add(['code' => $ds->brand_code, 'name' => $ds->brand_name]);
        }
        else
        {
          $this->product_brand_model->update($ds->brand_code, ['name' => $ds->brand_name]);
        }
      }
    }
  }

}


 ?>
