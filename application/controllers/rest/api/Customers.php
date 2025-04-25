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
    $type = "CUSTOMER";

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
      $id = $this->customers_model->get_id(trim($ds->code));
      $active = isset($ds->active) ? ($ds->active == 0 ? 0 : 1) : 1;

      $arr = array(
        'name' => trim($ds->name),
        'group_code' => get_null(trim($ds->group_code)),
        'kind_code' => get_null(trim($ds->kind_code)),
        'type_code' => get_null(trim($ds->type_code)),
        'class_code' => get_null(trim($ds->class_code)),
        'area_code' => get_null(trim($ds->area_code)),
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


  public function group_post()
  {
    $sc = TRUE;
    $this->load->model('masters/customer_group_model');
    $this->api_path = "rest/api/customers/group";
    $type = "CUSTOMER GROUP";
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
      $cs = $this->customer_group_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->customer_group_model->add($arr))
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
        if( ! $this->customer_group_model->update($cs->code, $arr))
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
    $this->load->model('masters/customer_kind_model');
    $this->api_path = "rest/api/customers/kind";
    $type = "CUSTOMER KIND";
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
      $cs = $this->customer_kind_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->customer_kind_model->add($arr))
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
        if( ! $this->customer_kind_model->update($cs->code, $arr))
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
    $this->load->model('masters/customer_type_model');
    $this->api_path = "rest/api/customers/type";
    $type = "CUSTOMER TYPE";
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
      $cs = $this->customer_type_model->get(trim($ds->code));

      $arr = array(
        'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->customer_type_model->add($arr))
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
        if( ! $this->customer_type_model->update($cs->code, $arr))
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


  public function area_post()
  {
    $sc = TRUE;
    $this->load->model('masters/customer_area_model');
    $this->api_path = "rest/api/customers/area";
    $type = "CUSTOMER AREA";
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
      $cs = $this->customer_area_model->get(trim($ds->code));

      $arr = array(
      'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->customer_area_model->add($arr))
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
        if( ! $this->customer_area_model->update($cs->code, $arr))
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


  public function class_post()
  {
    $sc = TRUE;
    $this->load->model('masters/customer_class_model');
    $this->api_path = "rest/api/customers/class";
    $type = "CUSTOMER CLASS";
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
      $cs = $this->customer_class_model->get(trim($ds->code));

      $arr = array(
      'name' => trim($ds->name)
      );

      if(empty($cs))
      {
        $arr['code'] = trim($ds->code);

        if( ! $this->customer_class_model->add($arr))
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
        if( ! $this->customer_class_model->update($cs->code, $arr))
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
