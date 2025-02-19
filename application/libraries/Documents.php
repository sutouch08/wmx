<?php
class Documents
{
  protected $ci;
  public $error;

	public function __construct()
	{
    // Assign the CodeIgniter super-object
    $this->ci =& get_instance();
	}


  public function get_new_code($type, $date = NULL)
  {
    if( ! empty($type))
    {
      $code = NULL;

      switch($type)
      {
        case 'IB' :
          $code = $this->new_ib_code($date);
          break;
        case 'ADJ' :
          $code = $this->new_adjust_code($date);
          break;
      }

      return $code;
    }

    return NULL;
  }


  private function get_prefix($prefix, $date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));

    return $prefix .'-'.$Y.$M;
  }


  public function new_ib_code($date = NULL)
  {
    $this->ci->load->model('inventory/receive_model');

    $prefix = $this->get_prefix('IB', $date);
    $code = $this->ci->receive_model->get_max_code($prefix);
    $run_digit = 5;

    if( ! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit * -1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function new_adjust_code($date = NULL)
  {
    $this->ci->load->model('inventory/adjust_model');

    $prefix = $this->get_prefix('AJ', $date);
    $code = $this->ci->adjust_model->get_max_code($prefix);
    $run_digit = 5;

    if( ! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit * -1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }
} //--- end class
 ?>
