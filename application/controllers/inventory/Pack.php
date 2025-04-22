<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pack extends PS_Controller
{
  public $menu_code = 'ICCKQC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ QC';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/pack';
    $this->load->model('inventory/pack_model');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'order_code', ''),
      'pd_code' => get_filter('pd_code', 'pd_code'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
      exit();
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();
      $segment  = 4; //-- url segment
      $rows = $this->pack_model->count_rows($filter);
      $filter['data'] = $this->pack_model->get_data($filter, $perpage, $this->uri->segment($segment));

      $init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $this->pagination->initialize($init);
      $this->load->view('inventory/pack/pack_list', $filter);

    }
  }


	public function delete_row()
	{
		$sc = TRUE;
		$id = trim($this->input->post('id'));

		if( ! empty($id))
		{
			$rs = $this->pack_model->delete($id);
			if(!$rs)
			{
				$sc = FALSE;
				$this->error = "ลบรายการไม่สำเร็จ";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : id";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  function clear_filter(){
    $filter = array('order_code', 'pd_code', 'from_date', 'to_date');
    return clear_filter($filter);
  }


} //--- end class
?>
