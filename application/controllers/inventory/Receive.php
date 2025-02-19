<?php
class Receive extends PS_Controller
{
  public $menu_code = 'ICIBRC';
  public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
  public $title = 'รับสินค้าเข้า';
  public $filter;
  public $segment = 4;
  public $is_mobile = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive';
    $this->load->model('inventory/receive_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->helper('warehouse');
    $this->load->library('user_agent');

    $this->is_mobile = $this->agent->is_mobile();
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'ib_code', ''),
      'order_no' => get_filter('order_no', 'ib_order_no', ''),
      'vendor'  => get_filter('vendor', 'ib_vendor', ''),
      'ref_no1' => get_filter('ref_no1', 'ib_ref_no1', ''),
      'ref_no2' => get_filter('ref_no2', 'ib_ref_no2', ''),
      'from_date' => get_filter('from_date', 'ib_from_date', ''),
      'to_date'   => get_filter('to_date', 'ib_to_date', ''),
      'order_from_date' => get_filter('order_from_date', 'ib_order_from_date', ''),
      'order_to_date' => get_filter('order_to_date', 'ib_order_to_date', ''),
      'order_type' => get_filter('order_type', 'ib_order_type', 'all'),
      'warehouse' => get_filter('warehouse', 'ib_warehouse', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $status = 'P';
      $perpage = get_rows();
      $rows = $this->receive_model->count_rows($filter, $status);
      $filter['list'] = $this->receive_model->get_list($filter, $perpage, $this->uri->segment($this->segment), $status);
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);

      if($this->is_mobile)
      {
        $this->load->view('inventory/receive/mobile/receive_list_mobile', $filter);        
      }
      else
      {
        $this->load->view('inventory/receive/receive_list', $filter);
      }
    }
  }


  public function is_document_avalible()
  {
    $code = $this->input->get('code');
    $uuid = $this->input->get('uuid');
    if( ! $this->receive_model->is_document_avalible($code, $uuid))
    {
      echo "not_available";
    }
    else
    {
      echo "available";
    }
  }


  public function process($code, $uuid)
  {
    $sc = TRUE;

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $doc = $this->receive_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P' OR $doc->status == 'R')
        {
          if($doc->status == 'P')
          {
            $arr = array(
              'status' => 'R',
              'update_user' => $this->_user->uname
            );

            $this->receive_model->update($doc->id, $arr);

            $this->receive_model->add_logs($code, 'start', $this->_user->uname);
          }

          $this->receive_model->update_uuid($code, $uuid);

          $ds = array(
            'doc' => $doc,
            'uncomplete' => $this->receive_model->get_unvalid_details($doc->id),
            'complete' => $this->receive_model->get_valid_details($doc->id)
          );

          $this->load->view('inventory/receive/receive_process', $ds);
        }
        else
        {
          $this->load->view('inventory/receive/invalid_state');
        }
      }
      else
      {
        $this->page_error();
      }
    }
    else
    {
      $this->deny_page();
    }
  }


  public function view_detail($code)
  {
    $doc = $this->receive_model->get($code);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->receive_model->get_details($doc->id)
      );

      $this->load->view('inventory/receive/receive_details', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function clear_filter()
  {
    $filter = array(
      'ib_code',
      'ib_order_no',
      'ib_vendor',
      'ib_ref_no1',
      'ib_ref_no2',
      'ib_from_date',
      'ib_to_date',
      'ib_order_from_date',
      'ib_order_to_date',
      'ib_order_type',
      'ib_warehouse',
      'ib_status'
    );

    return clear_filter($filter);
  }

} //--- end class

 ?>
