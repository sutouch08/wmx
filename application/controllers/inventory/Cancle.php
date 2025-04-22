<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cancle extends PS_Controller
{
  public $menu_code = 'ICCKCN';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ CANCLE ZONE';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/cancle';
    $this->load->model('inventory/cancle_model');
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'order_code', ''),
      'zone_code' => get_filter('zone_code', 'zone_code', ''),
      'warehouse' => get_filter('warehouse', 'warehouse', 'all'),
      'pd_code' => get_filter('pd_code', 'pd_code', ''),
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
      $segment  = 4; //-- url segment
      $rows = $this->cancle_model->count_rows($filter);
      $filter['data'] = $this->cancle_model->get_data($filter, $perpage, $this->uri->segment($segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $this->pagination->initialize($init);
      $this->load->view('inventory/cancle/cancel_list', $filter);
    }
  }


  public function move_back()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    $this->load->model('stock/stock_model');

    $rs = $this->cancle_model->get($id);

    if( ! empty($rs))
    {
      $this->db->trans_begin();
      //---- add stock back to original zone
      if( ! $this->stock_model->update_stock_zone($rs->zone_code, $rs->product_code, $rs->qty))
      {
        $sc = FALSE;
        $this->error = 'เพิ่มสต็อกกลับโซนเดิมไม่สำเร็จ';
      }

      //--- delete cancle row
      if(! $this->cancle_model->delete($id))
      {
        $sc = FALSE;
        $this->error = 'ลบรายการไม่สำเร็จ';
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

    $this->_response($sc);
  }


  //--- Just delete
  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $id = $this->input->post('id');
      if( ! empty($id))
      {
        if( ! $this->cancle_model->delete($id))
        {
          $sc = FALSE;
          $this->error = 'ลบรายการไม่สำเร็จ';
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  function clear_filter(){
    $filter = array('order_code', 'pd_code', 'zone_code', 'warehouse', 'from_date', 'to_date');
    return clear_filter($filter);
  }


} //--- end class
?>
