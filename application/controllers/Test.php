<?php
class Test extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $t = time();
    echo date('Y-m-d H:i:s', $t);
  }
}
 ?>
