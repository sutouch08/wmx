<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Senders extends REST_Controller
{
  public $error;  
	private $api_path = "rest/api/senders";
	public $logs;
	private $logs_json = FALSE;
	private $api = FALSE;

  public function __construct()
  {
    parent::__construct();

    $this->api = is_true(getConfig('IX_API'));

    if(!$this->api)
    {
      $arr = array(
        'code' => 503,
        'status' => 'error',
        'message' => 'Service Unavailable',
        'retry' => FALSE
      );

      $this->response($arr, 503);
    }

    $this->logs = $this->load->database('logs', TRUE);
    $this->load->model('rest/api/api_logs_model');    
    $this->logs_json = is_true(getConfig('IX_LOG_JSON'));    
  }

  public function index_post()
  {
    $req_start = now(TRUE);
    $sc = TRUE;
    $retry = FALSE;
    $this->load->model('masters/sender_model');
    $action = "create";
    $type = "ADD125";
    $json = file_get_contents('php://input');
    $ds = json_decode($json);
    $trans_id = genUid();
    $code = empty($ds->code) ? NULL : trim($ds->code);

    if(empty($ds))
    {
      $arr = array(
        'code' => 400,
        'status' => 'error',
        'message' => 'Empty request data',
        'retry' => FALSE,
        'trans_id' => $trans_id
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
          'message' => 'Empty request data',
          'request_json' => $json,
          'response_json' => json_encode($arr),
          'req_start' => $req_start,
          'req_end' => now(TRUE),
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
          'code' => 400,
          'status' => 'error',
          'message' => $this->error,
          'retry' => FALSE
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
            'response_json' => json_encode($arr),
            'req_start' => $req_start,
            'req_end' => now(TRUE)
          );

          $this->api_logs_model->add_logs($logs);
        }

        $this->response($arr, 400);
      }
    }
    

    if($sc === TRUE)
    {
      $id = $this->sender_model->get_id(trim($ds->code));
      $arr = array(
        'code' => trim($ds->code),
        'name' => trim($ds->name)
      );
      
      if(empty($id))
      {        
        if( ! $this->sender_model->add($arr))
        {
          $sc = FALSE;
          $this->error = "Insert failed";
          $retry = TRUE;
        }
      }
      else
      {
        $action = "update";

        if( ! $this->sender_model->update_by_id($id, $arr))
        {
          $sc = FALSE;
          $this->error = "Update failed";          
          $retry = TRUE;
        }
      }
    }

    $arr = array(
      'trans_id' => $trans_id,
      'code' => 200,
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'retry' => $retry
    );

    if ($this->logs_json)
    {
      $logs = array(
        'trans_id' => $trans_id,
        'api_path' => $this->api_path,
        'type' => $type,
        'code' => $ds->code,        
        'action' => $action,
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
}

