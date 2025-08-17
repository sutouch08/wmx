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
    $this->load->model('masters/customers_model');
    $trans_id = genUid();
    $action = "create";
    $type = "ADD122";

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
    $fields = ['uuid','code', 'name'];

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
    }

    if($sc === TRUE)
    {
      $this->update_customer_attribute($ds);
    }


    if($sc === TRUE)
    {
      $id = $this->customers_model->get_id(trim($ds->code));
      $active = empty($ds->active) ? 1 : ($ds->active == 'N' ? 0 : 1);

      $arr = array(
        'uuid' => trim($ds->uuid),
        'name' => trim($ds->name),
        'Tax_Id' => empty($ds->tax_id) ? NULL : get_null(trim($ds->tax_id)),
        'group_code' => empty($ds->group_code) ? NULL : get_null($ds->group_code),
        'group_name' => empty($ds->group_name) ? NULL : get_null($ds->group_name),
        'class_code' => empty($ds->grade_code) ? NULL : get_null($ds->grade_code),
        'class_name' => empty($ds->grade_name) ? NULL : get_null($ds->grade_name),
        'kind_code' => empty($ds->kind_code) ? NULL : get_null($ds->kind_code),
        'kind_name' => empty($ds->kind_name) ? NULL : get_null($ds->kind_name),
        'type_code' => empty($ds->type_code) ? NULL : get_null($ds->type_code),
        'type_name' => empty($ds->type_name) ? NULL : get_null($ds->type_name),
        'area_code' => empty($ds->area_code) ? NULL : get_null($ds->area_code),
        'area_name' => empty($ds->area_name) ? NULL : get_null($ds->area_name),
        'active' => $active
      );

      if(empty($id))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->customers_model->add($arr))
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

        if( ! $this->customers_model->update_by_id($id, $arr))
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
  }


  private function update_customer_attribute($ds)
  {
    if( ! empty($ds))
    {
      //--- customer_group
      if( ! empty($ds->group_code) && ! empty($ds->group_name))
      {
        $code = $ds->group_code;
        $name = $ds->group_name;

        $this->load->model('masters/customer_group_model');

        if( ! $this->customer_group_model->is_exists($code))
        {
          $this->customer_group_model->add(['code' => $code, 'name' => $name]);
        }
        else
        {
          $this->customer_group_model->update($code, ['name' => $name]);
        }
      }

      //--- customer_kind
      if( ! empty($ds->kind_code) && ! empty($ds->kind_name))
      {
        $code = $ds->kind_code;
        $name = $ds->kind_name;

        $this->load->model('masters/customer_kind_model');

        if( ! $this->customer_kind_model->is_exists($code))
        {
          $this->customer_kind_model->add(['code' => $code, 'name' => $name]);
        }
        else
        {
          $this->customer_kind_model->update($code, ['name' => $name]);
        }
      }

      //--- customer_type
      if( ! empty($ds->type_code) && ! empty($ds->type_name))
      {
        $code = $ds->type_code;
        $name = $ds->type_name;
        $this->load->model('masters/customer_type_model');

        if( ! $this->customer_type_model->is_exists($code))
        {
          $this->customer_type_model->add(['code' => $code, 'name' => $name]);
        }
        else
        {
          $this->customer_type_model->update($code, ['name' => $name]);
        }
      }

      //--- customer_grade
      if( ! empty($ds->grade_code) && ! empty($ds->grade_name))
      {
        $code = $ds->grade_code;
        $name = $ds->grade_name;

        $this->load->model('masters/customer_class_model');

        if( ! $this->customer_class_model->is_exists($code))
        {
          $this->customer_class_model->add(['code' => $code, 'name' => $name]);
        }
        else
        {
          $this->customer_class_model->update($code, ['name' => $name]);
        }
      }

      //--- customer_area
      if( ! empty($ds->area_code) && ! empty($ds->area_name))
      {
        $code = $ds->area_code;
        $name = $ds->area_name;

        $this->load->model('masters/customer_area_model');

        if( ! $this->customer_area_model->is_exists($code))
        {
          $this->customer_area_model->add(['code' => $code, 'name' => $name]);
        }
        else
        {
          $this->customer_area_model->update($code, ['name' => $name]);
        }
      }
    }

    return TRUE;
  }

} //-- end class

 ?>
