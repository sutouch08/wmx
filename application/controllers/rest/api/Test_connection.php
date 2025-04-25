<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Test_connection extends REST_Controller
{
  public $error;

  public function __construct()
  {
    parent::__construct();			
  }

  public function index_get()
  {
    $arr = array(
      'status' => true,
      'message' => "Connected"
    );

    $this->response($arr, 200);
  }
}
?>
