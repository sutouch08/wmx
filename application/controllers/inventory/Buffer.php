<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buffer extends PS_Controller
{
  public $menu_code = 'ICCKBF';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ BUFFER';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/buffer';
    $this->load->model('inventory/buffer_model');
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'order_code', ''),
      'zone_code' => get_filter('zone_code', 'zone_code', ''),
      'warehouse' => get_filter('warehouse', 'warehouse', 'all'),
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
      $perpage = get_rows();
      $segment = 4; 
      $rows = $this->buffer_model->count_rows($filter);
      $filter['data'] = $this->buffer_model->get_data($filter, $perpage, $this->uri->segment($segment));

      $init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $this->pagination->initialize($init);
      $this->load->view('inventory/buffer/buffer_list', $filter);
    }
  }


	public function delete_buffer()
	{
		$sc = TRUE;

		$id = trim($this->input->post('id'));

		if( ! empty($id))
		{
      $this->load->model('stock/stock_model');
      $this->load->model('inventory/prepare_model');
      $this->load->model('orders/orders_model');

      $rs = $this->buffer_model->get($id);

      if( ! empty($rs))
      {
        $this->db->trans_begin();

        if( ! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, $rs->qty))
        {
          $sc = FALSE;
          $this->error = "ย้ายสต็อกกลับโซนไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          if( ! $this->buffer_model->delete($id))
          {
            $sc = FALSE;
            $this->error = "ลบ buffer ไม่สำเร็จ";
          }
        }

        if($sc === TRUE)
        {
          if( ! $this->prepare_model->remove_prepare($rs->order_code, $rs->product_code, $rs->order_detail_id))
          {
            $sc = FALSE;
            $this->error = "ลบข้อมูลการจัดไม่สำเร็จ";
          }
        }

        if($sc === TRUE)
        {
          if( ! $this->orders_model->unvalid_detail($rs->order_detail_id))
          {
            $sc = FALSE;
            $this->error = "Failed to rollback item status (unvalid)";
          }
        }

        if($sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
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
    $filter = array('order_code', 'pd_code', 'zone_code', 'warehouse', 'from_date', 'to_date');
    clear_filter($filter);
  }


} //--- end class
?>
