<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Zone extends PS_Controller
{
  public $menu_code = 'DBZONE';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข โซน';
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/zone';
    $this->load->model('masters/zone_model');
    $this->load->helper('zone');
    $this->load->helper('warehouse');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'z_code', ''),
      'uname' => get_filter('uname', 'z_uname', ''),
      'warehouse' => get_filter('warehouse', 'z_warehouse', ''),
      'customer' => get_filter('customer', 'z_customer', ''),
      'active' => get_filter('active', 'z_active', 'all'),
      'is_pos_api' => get_filter('is_pos_api', 'z_pos_api', 'all'),
      'is_pickface' => get_filter('is_pickface', 'z_pickface', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->zone_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list = $this->zone_model->get_list($filter, $perpage, $this->uri->segment($segment));

    if( ! empty($list))
    {
      foreach($list as $rs)
      {
        $rs->customer_count = $this->zone_model->count_customer($rs->code);
      }
    }

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/zone/zone_list', $filter);
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('masters/zone/zone_add');
    }
    else
    {
      $this->deny_page();
    }
  }


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->name) && ! empty($ds->warehouse_code))
      {
        if($this->zone_model->is_exists($ds->code))
        {
          $sc = FALSE;
          set_error('exists', $ds->code);
        }

        if($sc === TRUE && $this->zone_model->is_exists_name($ds->name))
        {
          $sc = FALSE;
          set_error('exists', $ds->name);
        }

        if($sc === TRUE)
        {
          $arr = array(
            'code' => $ds->code,
            'name' => $ds->name,
            'warehouse_code' => $ds->warehouse_code,
            'active' => $ds->active == 0 ? 0 : 1,
            'is_pickface' => $ds->is_pickface == 1 ? 1 : 0,
            'user' => $this->_user->uname
          );

          if( ! $this->zone_model->add($arr))
          {
            $sc = FALSE;
            set_error('insert');
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


  public function generate_qrcode()
  {
    $ds = json_decode($this->input->post('data'));
    $list = [];

    if( ! empty($ds))
    {
      $this->load->library('ixqrcode');

      foreach($ds as $rs)
      {
        $path = $this->config->item('qrcode_path').$rs->code.'.png';

        $qr = array(
          'data' => $rs->code,
          'size' => 8,
          'level' => 'H',
          'savename' => NULL
        );

        ob_start();
        $this->ixqrcode->generate($qr);
        $qr = base64_encode(ob_get_contents());
        ob_end_clean();

        $list[] = (object)['file' => $qr, 'code' => $rs->code, 'name' => $rs->name];
      }

      $this->load->library('printer');
      $ds = array(
        'list' => $list
      );

      $this->load->view('print/print_qr_code', $ds);
    }
  }


  public function edit($code)
  {
    if($this->pm->can_edit)
    {
      $zone = $this->zone_model->get($code);
      $ds['ds'] = $zone;
      $ds['customers'] = $this->zone_model->get_customers($code);
      $ds['employees'] = NULL;

      if($zone->role == 8)
      {
        $this->load->helper('employee');
        $ds['employees'] = $this->zone_model->get_employee($code);
      }

      $this->load->view('masters/zone/zone_edit', $ds);
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

      if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->name) && ! empty($ds->warehouse_code))
      {
        if($this->zone_model->is_exists_name($ds->name, $ds->code))
        {
          $sc = FALSE;
          set_error('exists', $ds->code);
        }

        if($sc === TRUE)
        {
          $arr = array(
            'name' => $ds->name,
            'warehouse_code' => $ds->warehouse_code,
            'active' => $ds->active == 0 ? 0 : 1,
            'is_pickface' => $ds->is_pickface == 1 ? 1 : 0,
            'update_user' => $this->_user->uname
          );

          if( ! $this->zone_model->update($ds->id, $arr))
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


  public function update_pos_api()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $is_pos_api = $this->input->post('is_api') == 1 ? 1 : 0;

    $arr = array('is_pos_api' => $is_pos_api);

    if( ! $this->zone_model->update($id, $arr))
    {
      $sc = FALSE;
      $this->error = "Update failed";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function update_pickface()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $is_pickface = $this->input->post('is_pickface') == 1 ? 1 : 0;

    $arr = array('is_pickface' => $is_pickface);

    if( ! $this->zone_model->update($id, $arr))
    {
      $sc = FALSE;
      $this->error = "Update failed";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function delete($code)
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      if($sc === TRUE && $this->zone_model->has_stock($code))
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถลบโซนได้เนื่องจากมีสต็อกคงเหลือในโซน";
      }

      if($sc === TRUE && $this->zone_model->has_customer($code))
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถลบโซนได้เนื่องจากมีการเชื่อมโยงลูกค้าไว้";
      }

      if($sc === TRUE && $this->zone_model->has_employee($code))
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถลบโซนได้เนื่องจากมีการเชื่อมโยงพนักงานไว้";
      }

      if($sc === TRUE && $this->zone_model->has_transection($code))
      {
        $sc = FALSE;
        set_error('transection');
      }

      if($sc === TRUE)
      {
        if( ! $this->zone_model->delete($code))
        {
          $sc = FALSE;
          set_error('delete');
        }
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
    if($this->pm->can_edit)
    {
      if($this->input->post('zone_code') && $this->input->post('customer_code'))
      {
        $this->load->model('masters/customers_model');
        $code = $this->input->post('zone_code');
        $customer_code = $this->input->post('customer_code');
        $customer = $this->customers_model->get($customer_code);
        if( ! empty($customer))
        {
          if($this->zone_model->is_exists_customer($code, $customer->code))
          {
            $sc = FALSE;
            $this->error = "มีลูกค้าในโซนนี้อยู่แล้ว";
          }
          else
          {
            $arr = array(
              'zone_code' => $code,
              'customer_code' => $customer->code,
              'customer_name' => $customer->name
            );

            if( ! $this->zone_model->add_customer($arr))
            {
              $sc = FALSE;
              $this->error = "เพิ่มลูกค้าไม่สำเร็จ";
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "รหัสลูกค้าไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบข้อมูล";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ในการเพิ่มข้อมูล";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function delete_customer($id)
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      if( ! $this->zone_model->delete_customer($id))
      {
        $sc = FALSE;
        $this->error = "ลบรายการไม่สำเร็จ";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ลบข้อมูล";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function add_employee()
  {
    $sc = TRUE;
    if($this->pm->can_edit)
    {
      if($this->input->post('zone_code') && $this->input->post('empID'))
      {
        $this->load->model('masters/employee_model');
        $code = $this->input->post('zone_code');
        $empName = $this->input->post('empName');
        $empID = $this->input->post('empID');
        $emp = $this->employee_model->get($empID);
        $zone = $this->zone_model->get($code);

        if($zone->role != 8)
        {
          $sc = FALSE;
          $this->error = "โซนนี้ไม่อยู่ในประเภทคลังยืมสินค้า";
        }

        if($sc === TRUE)
        {
          if( ! empty($emp))
          {
            if($this->zone_model->is_exists_employee($code, $empID))
            {
              $sc = FALSE;
              $this->error = "มีพนักงานนี้ในโซนอยู่แล้ว";
            }
            else
            {
              $arr = array(
                'zone_code' => $code,
                'empID' => $empID,
                'empName' => $empName
              );

              if( ! $this->zone_model->add_employee($arr))
              {
                $sc = FALSE;
                $this->error = "เพิ่มพนักงานไม่สำเร็จ";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ชื่อพนักงานไม่ถูกต้อง";
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบข้อมูล";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ในการเพิ่มข้อมูล";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function delete_employee($id)
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      if( ! $this->zone_model->delete_employee($id))
      {
        $sc = FALSE;
        $this->error = "ลบรายการไม่สำเร็จ";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "คุณไม่มีสิทธิ์ลบข้อมูล";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  //---- for prepare product
  public function get_zone()
  {
    $sc = TRUE;
    $code = trim($this->input->get('code'));
    $whsCode = get_null(trim($this->input->get('warehouse_code')));
    $whsName = get_null(trim($this->input->get('warehouse_name')));
    $zone = $this->zone_model->get_zone($code, $whsCode);

    if(empty($zone))
    {
      $sc = FALSE;
      $this->error = "Invalid zone or zone not belong to warehouse {$whsCode} : {$whsName}";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $zone->code : NULL,
      'name' => $sc === TRUE ? $zone->name : NULL,
      'warehouse_code' => $sc === TRUE ? $zone->warehouse_code : NULL
    );

    echo json_encode($arr);
  }


  //--- check zone
  public function get_zone_code()
  {
    $sc = TRUE;
    if($this->input->get('barcode'))
    {
      $barcode = trim($this->input->get('barcode'));
      $code = $this->zone_model->get_zone_code($barcode);

      if($code === FALSE)
      {
        $sc = FALSE;
      }
    }

    echo $sc === TRUE ? $code : 'not_exists';
  }


  public function get_warehouse_zone()
  {
    $sc = TRUE;
    $code = trim($this->input->get('barcode'));
    $warehouse_code = trim($this->input->get('warehouse_code'));
    if( ! empty($code) && !empty($warehouse_code))
    {
      $zone = $this->zone_model->get_zone_detail_in_warehouse($code, $warehouse_code);
      if($zone === FALSE)
      {
        $sc = FALSE;
        $this->error = "ไม่พบโซน";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "รหัสโซนหรือรหัสคลังไม่ถูกต้อง : {$code} | {$warehouse_code}";
    }

    echo $sc === TRUE ? json_encode($zone) : 'not_exists';
  }




  public function export_filter()
  {
    $ds = array(
      'code' => $this->input->post('zone_code'),
      'uname' => $this->input->post('zone_uname'),
      'customer' => $this->input->post('zone_customer'),
      'warehouse' => $this->input->post('zone_warehouse')
    );

    $token = $this->input->post('token');

    $list = $this->zone_model->get_list($ds);

    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Zone master data');

    //--- set Table header


    $this->excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
    $this->excel->getActiveSheet()->setCellValue('B1', 'รหัสโซน');
    $this->excel->getActiveSheet()->setCellValue('C1', 'ชื่อโซน');
    $this->excel->getActiveSheet()->setCellValue('D1', 'รหัสคลัง');
    $this->excel->getActiveSheet()->setCellValue('E1', 'คลังสินค้า');
    $this->excel->getActiveSheet()->setCellValue('F1', 'รหัสเก่า');
    $this->excel->getActiveSheet()->setCellValue('G1', 'เจ้าของโซน');
    $this->excel->getActiveSheet()->setCellValue('H1', 'ผู้รับผิดชอบ');
    $this->excel->getActiveSheet()->setCellValue('I1', 'Active');


    //---- กำหนดความกว้างของคอลัมภ์
    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);


    $row = 2;


    if( ! empty($list))
    {
      $no = 1;

      foreach($list as $rs)
      {
        //--- ลำดับ
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);

        //--- zone code
        $this->excel->getActiveSheet()->setCellValue('B'.$row, $rs->code);

        //--- zone name
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->name);

        //--- warehouse code
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->warehouse_code);

        //---- waehouser name
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->warehouse_name);
        //--- old code
        $this->excel->getActiveSheet()->setCellValue('F'.$row, "{$rs->old_code}");

        //--- user name
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->uname);

        $this->excel->getActiveSheet()->setCellValue('H'.$row, $rs->display_name);

        $this->excel->getActiveSheet()->setCellValue('I'.$row, ($rs->active ? 'Y' : 'N'));



        $no++;
        $row++;
      }

      setToken($token);
      $file_name = "Zone Master Data.xlsx";
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
      header('Content-Disposition: attachment;filename="'.$file_name.'"');
      $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
      $writer->save('php://output');
    }
  }



  public function clear_filter()
  {
    $filter = array('z_code', 'z_uname', 'z_customer', 'z_warehouse', 'z_active', 'z_pos_api', 'z_pickface');
    clear_filter($filter);
  }

} //--- end class

 ?>
