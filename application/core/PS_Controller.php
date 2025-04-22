<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PS_Controller extends CI_Controller
{
  public $pm;
  public $home;
  public $close_system;
  public $isViewer;
	public $_user;
	public $_SuperAdmin = FALSE;
  public $error;

  public function __construct()
  {
    parent::__construct();

    $uid = get_cookie('uid');

    //--- check is user has logged in ?
    if(empty($uid) OR ! $this->user_model->verify_uid($uid))
    {
      redirect(base_url().'users/authentication');
      exit();
    }
    else
    {

      $this->_user = $this->user_model->get_user_by_uid($uid);
      $this->isViewer = $this->_user->is_viewer == 1 ? TRUE : FALSE;
      $this->_SuperAdmin = $this->_user->id_profile == -987654321 ? TRUE : FALSE;

      $this->close_system   = getConfig('CLOSE_SYSTEM'); //--- ปิดระบบทั้งหมดหรือไม่

      if($this->close_system == 1 && $this->_SuperAdmin === FALSE)
      {
        redirect(base_url().'setting/maintenance');
        exit();
      }

      if(!$this->isViewer && $this->is_expire_password($this->_user->last_pass_change))
      {
        redirect(base_url().'change_password');
        exit();
      }

      //--- get permission for user
      $this->pm = get_permission($this->menu_code, $uid, get_cookie('id_profile'));
    }
  }


  public function _response($sc = TRUE)
  {
    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function deny_page()
  {
    return $this->load->view('deny_page');
  }


  public function error_page($err = NULL)
  {
		$error = array('error_message' => $err);
    return $this->load->view('page_error', $error);
  }


	public function page_error($err = NULL)
  {
		$error = array('error_message' => $err);
    return $this->load->view('page_error', $error);
  }


	public function is_expire_password($last_pass_change)
	{
		$today = date('Y-m-d');
		$last_change = empty($last_pass_change) ? date('2021-01-01') : $last_pass_change;

		$expire_days = intval(getConfig('USER_PASSWORD_AGE'));

		$expire_date = date('Y-m-d', strtotime("+{$expire_days} days", strtotime($last_change)));

		if($today > $expire_date)
		{
			return true;
		}

		return FALSE;
	}
}

?>
