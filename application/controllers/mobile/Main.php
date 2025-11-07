<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends PS_Controller
{
	public $title = 'Welcome';
	public $menu_code = '';
	public $menu_group_code = '';
	public $error;

	public function __construct()
	{
		parent::__construct();
		_check_login('m');
		$this->pm = new stdClass();
		$this->pm->can_view = 1;
	}


	public function index()
	{
		$tabs = ['tab' => 'home'];
		$this->load->view('mobile/main_view', $tabs);
	}

} //--- end class
?>
