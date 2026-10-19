<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Netsuite_test extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }
    
  public function int03()
  {
    $this->load->library('ns_consign_api');
    $item = 'WA-PLAN15-AA-L';
    $whs = 'AFG-0001';
    $stock = $this->ns_consign_api->get_onhand_stock($item, $whs);
    echo $stock;
  }

  public function add16()
  {
    $this->load->library('ns_consignment_api');
    $item = 'WA-PLAN15-AA-Lx';
    $whs = 'AFG-0001';
    $stock = $this->ns_consignment_api->get_onhand_stock($item, $whs);
    echo $stock;
  }

  public function export_consignment(string $code)
  {
    $this->load->library('ns_consignment_api');
    $sc = $this->ns_consignment_api->export_consignment($code);
    echo $sc ? 'Export successful' : 'Export failed: ' . $this->ns_consignment_api->error;
  }

  public function export_consign(string $code)
  {
    $this->load->library('ns_consign_api');
    $sc = $this->ns_consign_api->export_consign($code);
    echo $sc ? 'Export successful' : 'Export failed: ' . $this->ns_consign_api->error;
  }

  public function export_adjust(string $code)
  {
    $this->load->library('ns_adjust_api');
    $sc = $this->ns_adjust_api->export_adjust($code);
    echo $sc ? 'Export successful' : 'Export failed: ' . $this->ns_adjust_api->error;
  }

  public function export_receive(string $code)
  {
    $this->load->library('ns_ib_api');
    $sc = $this->ns_ib_api->export_receive($code);
    echo $sc ? 'Export successful' : 'Export failed: ' . $this->ns_ib_api->error;
  }

  public function export_receive_po(string $code)
  {
    $this->load->library('ns_ib_api');
    $sc = $this->ns_ib_api->export_receive_po($code);
    echo $sc ? 'Export successful' : 'Export failed: ' . $this->ns_ib_api->error;
  }

  public function export_return(string $code)
  {
    $this->load->library('ns_ib_api');
    $sc = $this->ns_ib_api->export_return($code);
    echo $sc ? 'Export successful' : 'Export failed: ' . $this->ns_ib_api->error;
  }

}
