<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends PS_Controller
{
  public $menu_code = 'ICCKST';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบสต็อกคงเหลือ';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/stock';
    $this->load->model('stock/stock_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');
    $this->load->helper('buffer');
    $this->load->helper('cancle');
    $this->load->helper('zone');
  }


  public function index()
  {
    $filter = array(
      'item_code' => get_filter('item_code', 'item_code', ''),
      'zone_code' => get_filter('zone_code', 'zone_code', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment  = 4; //-- url segment
		$rows     = $this->stock_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);

    $filter['data'] = $this->stock_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$this->pagination->initialize($init);
    $this->load->view('inventory/stock/stock_view', $filter);
  }


  public function export()
  {
    $arr = array(
      'item_code' => $this->input->post('item'),
      'zone_code' => $this->input->post('zone')
    );

    $token = $this->input->post('token');

    $data = $this->stock_model->get_export_list($arr);

    if(!empty($data))
    {
      //--- load excel library
      $this->load->library('excel');

      $this->excel->setActiveSheetIndex(0);
      $this->excel->getActiveSheet()->setTitle('Stock Zone');

      $this->excel->getActiveSheet()->setCellValue('A1', 'No.');
      $this->excel->getActiveSheet()->setCellValue('B1', 'Item Code');
      $this->excel->getActiveSheet()->setCellValue('C1', 'Zone Code');
      $this->excel->getActiveSheet()->setCellValue('D1', 'In Zone');
      $this->excel->getActiveSheet()->setCellValue('E1', 'Buffer');
      $this->excel->getActiveSheet()->setCellValue('F1', 'Cancel');
      $this->excel->getActiveSheet()->setCellValue('G1', 'Total');

      $no = 1;
      $row = 2;

      foreach($data as $rs)
      {
        $bQty = get_buffer_qty_by_product_and_zone($rs->product_code, $rs->zone_code); // buffer
        $cQty = get_cancle_qty_by_product_and_zone($rs->product_code, $rs->zone_code); // cancle
        $total = $rs->qty + $bQty + $cQty;

        if($total != 0)
        {
          $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
          $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->product_code);
          $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->zone_code);
          $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->qty);
          $this->excel->getActiveSheet()->setCellValue('E'.$row, $bQty);
          $this->excel->getActiveSheet()->setCellValue('F'.$row, $cQty);
          $this->excel->getActiveSheet()->setCellValue('G'.$row, "=SUM(D{$row}:F{$row})");
          $no++;
          $row++;
        }
      }
    }

    setToken($token);
    $file_name = "StockZone.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


  function clear_filter(){
    $filter = array('item_code', 'zone_code');
    clear_filter($filter);
    echo 'done';
  }

} //--- end class
?>
