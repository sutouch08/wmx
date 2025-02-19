<?php
class Inbound extends PS_Controller
{
  public $menu_code = 'ICGRIB';
  public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
  public $title = 'Inbound';
  public $filter;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/inbound';
    $this->load->model('inventory/inbound_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->helper('warehouse');
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
      'warehouse' => get_filter('warehouse', 'ib_warehouse', 'all'),
      'status' => get_filter('status', 'ib_status', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();
      $rows = $this->inbound_model->count_rows($filter);
      $filter['list'] = $this->inbound_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('inventory/inbound/inbound_list', $filter);
    }
  }


  public function view_detail($code)
  {
    $doc = $this->inbound_model->get($code);

    if( ! empty($doc))
    {
      $ds = array(
        'doc' => $doc,
        'details' => $this->inbound_model->get_details($doc->id)
      );

      $this->load->view('inventory/inbound/inbound_details', $ds);
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
