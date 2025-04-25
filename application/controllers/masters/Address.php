<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Address extends PS_Controller
{
  public $menu_code = 'DBADDR';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'ที่อยู่จัดส่ง';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/address';
    $this->load->model('address/address_model');
    $this->load->model('address/transport_model');
    $this->load->model('masters/customers_model');
  }


  public function get_online_address($customer_ref)
  {
    $rs = $this->address_model->get_default_address($customer_ref);
    if(!empty($rs))
    {
      echo $rs->id;
    }
    else
    {
      echo 'noaddress';
    }
  }


  public function print_address_sheet($code)
  {
    $this->load->library('printer');
    $this->load->library('ixqrcode');
    $this->load->model('inventory/qc_model');
    $this->load->model('orders/orders_model');
    $order = $this->orders_model->get($code);
    $ad = $this->address_model->get_shipping_address($code);
    $id_sender = empty($order->id_sender) ? 1 : $order->id_sender;

    $qr = array(
      'data' => $code,
      'size' => 8,
      'level' => 'H',
      'savename' => NULL
    );

    ob_start();
    $this->ixqrcode->generate($qr);
    $qr = base64_encode(ob_get_contents());
    ob_end_clean();

    $ds = array(
      'reference' => $code,
      'boxes' => $this->qc_model->count_box($code),
      'ad' => $ad,
      'sd' => $this->transport_model->get_sender($id_sender),
      'cName' => getConfig('COMPANY_FULL_NAME'),
      'cAddress' => getConfig('COMPANY_ADDRESS1').'<br>'.getConfig('COMPANY_ADDRESS2'),
      'cPostCode' => getConfig('COMPANY_POST_CODE'),
      'cPhone' => getConfig('COMPANY_PHONE'),
      'qrcode' => $qr
    );

    $this->load->view('print/print_address_sheet', $ds);
  }

} //--- end class

?>
