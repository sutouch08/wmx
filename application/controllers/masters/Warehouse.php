<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Warehouse extends PS_Controller
{
  public $menu_code = 'DBWRHS';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข คลังสินค้า';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/warehouse';
    $this->load->model('masters/warehouse_model');
    $this->load->helper('warehouse');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'wh_code', ''),
      'role' => get_filter('role', 'wh_role', 'all'),
      'is_consignment' => get_filter('is_consignment', 'is_consignment', 'all'),
      'active' => get_filter('active', 'wh_active', 'all'),
      'sell' => get_filter('sell', 'wh_sell', 'all'),
      'prepare' => get_filter('prepare', 'wh_prepare', 'all'),
      'lend' => get_filter('lend', 'wh_lend', 'all'),
      'auz' => get_filter('auz', 'wh_auz', 'all'),
      'is_pos' => get_filter('is_pos', 'wh_is_pos', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
      exit();
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->warehouse_model->count_rows($filter);
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $list = $this->warehouse_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

      if( ! empty($list))
      {
        foreach($list as $rs)
        {
          $rs->zone_count = $this->warehouse_model->count_zone($rs->code);
          $rs->customer_count = $this->warehouse_model->count_customer($rs->code);
        }
      }

      $filter['list'] = $list;

      $this->pagination->initialize($init);
      $this->load->view('masters/warehouse/warehouse_list', $filter);
    }
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('masters/warehouse/warehouse_add');
    }
    else
    {
      $this->deny_page();
    }
  }


  function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->name))
    {
      if($this->pm->can_add)
      {
        if($this->warehouse_model->is_exists($ds->code))
        {
          $sc = FALSE;
          set_error('exists', $ds->code);
        }

        if($sc === TRUE && $this->warehouse_model->is_exists_name($ds->name))
        {
          $sc = FALSE;
          set_error('exists', $ds->name);
        }

        if($sc === TRUE)
        {
          $arr = array(
            'code' => $ds->code,
            'name' => $ds->name,
            'role' => $ds->role,
            'active' => $ds->active == 0 ? 0 : 1,
            'sell' => $ds->sell == 0 ? 0 : 1,
            'prepare' => $ds->prepare == 0 ? 0 : 1,
            'lend' => $ds->lend == 0 ? 0 : 1,
            'user' => $this->_user->uname
          );

          if( ! $this->warehouse_model->add($arr))
          {
            $sc = FALSE;
            set_error('insert');
          }
        }
      }
      else
      {
        $sc = FALSE;
        set_error('permission');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function edit($code)
  {
    if($this->pm->can_edit)
    {
      $ds['ds'] = $this->warehouse_model->get($code);
      $ds['customers'] = $this->warehouse_model->get_warehouse_customers($code);
      $this->load->view('masters/warehouse/warehouse_edit', $ds);
    }
    else
    {
      $this->deny_page();
    }
  }


  public function update()
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->name))
      {
        if($sc === TRUE && $this->warehouse_model->is_exists_name($ds->name, $ds->code))
        {
          $sc = FALSE;
          set_error('exists', $ds->name);
        }

        if($sc === TRUE)
        {
          $arr = array(
            'name' => $ds->name,
            'role' => $ds->role,
            'active' => $ds->active == 0 ? 0 : 1,
            'sell' => $ds->sell == 0 ? 0 : 1,
            'prepare' => $ds->prepare == 0 ? 0 : 1,
            'lend' => $ds->lend == 0 ? 0 : 1,
            'update_user' => $this->_user->uname
          );

          if( ! $this->warehouse_model->update($ds->code, $arr))
          {
            $sc = FALSE;
            set_error('update');
          }
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


  public function view_detail($code)
  {
    $ds['ds'] = $this->warehouse_model->get($code);
    $ds['customers'] = $this->warehouse_model->get_warehouse_customers($code);
    $this->load->view('masters/warehouse/warehouse_detail', $ds);
  }


  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $code = $this->input->post('code');

      if( ! empty($code))
      {
        if($this->warehouse_model->has_zone($code))
        {
          $sc = FALSE;
          $this->error = 'ไม่สามารถลบคลังได้เนื่องจากยังมีโซนอยู่';
        }

        if( ! $this->warehouse_model->delete($code))
        {
          $sc = FALSE;
          set_error('delete');
        }
        else
        {
          $this->warehouse_model->delete_all_customer($code);
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


  public function add_customer()
  {
    $sc = TRUE;
    $code = $this->input->post('warehouse_code');
    $customer_code = $this->input->post('customer_code');
    $customer_name = $this->input->post('customer_name');
    $ds = [];

    if( ! empty($code) && ! empty($customer_code) && ! empty($customer_name))
    {
      if( ! $this->warehouse_model->is_exists_customer($code, $customer_code))
      {
        $ds = array(
          'warehouse_code' => $code,
          'customer_code' => $customer_code,
          'customer_name' => $customer_name,
          'user' => $this->_user->uname
        );

        $id = $this->warehouse_model->add_customer($ds);

        if( ! empty($id))
        {
          $ds['id'] = $id;
        }
        else
        {
          $sc = FALSE;
          set_error('insert', $customer_code);
        }
      }
      else
      {
        $sc = FALSE;
        set_error('exists', $customer_code);
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function delete_customer()
  {
    $sc = TRUE;
    $id = $this->input->post('id');

    if( ! empty($id))
    {
      if( ! $this->warehouse_model->delete_customer($id))
      {
        $sc = FALSE;
        set_error('delete');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function export_filter()
  {
    $filter = array(
      'code' => get_filter('whCode', 'wh_code', ''),
      'role' => get_filter('whRole', 'wh_role', 'all'),
      'is_consignment' => get_filter('whIsConsignment', 'is_consignment', 'all'),
      'active' => get_filter('whActive', 'wh_active', 'all'),
      'sell' => get_filter('whSell', 'wh_sell', 'all'),
      'lend' => get_filter('whLend', 'wh_lend', 'all'),
      'prepare' => get_filter('whPrepare', 'wh_prepare', 'all'),
      'auz' => get_filter('whAuz', 'wh_auz', 'all'),
      'is_pos' => get_filter('whIsPos', 'wh_is_pos', 'all')
    );

    $token = $this->input->post('token');

    $list = $this->warehouse_model->get_list($filter);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Zone master data');

    //--- set Table header


    $this->excel->getActiveSheet()->setCellValue('A1', 'No.');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Code');
    $this->excel->getActiveSheet()->setCellValue('C1', 'Description');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Role');
    $this->excel->getActiveSheet()->setCellValue('E1', 'Bin Location');
    $this->excel->getActiveSheet()->setCellValue('F1', 'Sell');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Pick');
    $this->excel->getActiveSheet()->setCellValue('H1', 'Can be negative');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Active');
    $this->excel->getActiveSheet()->setCellValue('J1', 'Is Consignment');
    $this->excel->getActiveSheet()->setCellValue('K1', 'Limit Amount');


    //---- กำหนดความกว้างของคอลัมภ์
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);


    $row = 2;


    if(! empty($list))
    {
      $no = 1;

      foreach($list as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->code);
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->name);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->role_name);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $this->warehouse_model->count_zone($rs->code));
        $this->excel->getActiveSheet()->setCellValue('F'.$row, ($rs->sell ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('G'.$row, ($rs->prepare ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('H'.$row, ($rs->auz ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('I'.$row, ($rs->active ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('J'.$row, ($rs->is_consignment ? 'Y' : 'N'));
        $this->excel->getActiveSheet()->setCellValue('K'.$row, $rs->limit_amount);
        $no++;
        $row++;
      }

      setToken($token);
      $file_name = "Warehouse Master Data.xlsx";
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
      header('Content-Disposition: attachment;filename="'.$file_name.'"');
      $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
      $writer->save('php://output');
    }
  }


  public function clear_filter()
  {
    $filter = array('wh_code', 'wh_role', 'is_consignment', 'wh_active', 'wh_sell', 'wh_prepare', 'wh_auz', 'wh_lend', 'wh_is_pos');
    clear_filter($filter);
  }

} //--- end class

 ?>
