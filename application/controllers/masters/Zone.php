<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Zone extends PS_Controller
{
  public $menu_code = 'DBZONE';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'WAREHOUSE';
	public $title = 'เพิ่ม/แก้ไข โซน';
  public $segment = 4;

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
      'barcode' => get_filter('barcode', 'z_barcode', ''),
      'row' => get_filter('row', 'z_row', ''),
      'col' => get_filter('col', 'z_col', ''),
      'loc' => get_filter('loc', 'z_loc', ''),
      'warehouse' => get_filter('warehouse', 'z_warehouse', 'all'),
      'freeze' => get_filter('freeze', 'z_freeze', 'all'),
      'active' => get_filter('active', 'z_active', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->zone_model->count_rows($filter);
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $filter['list'] = $this->zone_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $this->pagination->initialize($init);
      $this->load->view('masters/zone/zone_list', $filter);
    }
  }


  public function generate()
  {
    if($this->pm->can_add)
    {
      $this->title = "Generate Location";
      $this->load->view('masters/zone/zone_generate');
    }
    else
    {
      $this->deny_page();
    }
  }


  function generate_location()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $codes = [];
    $gen = 0;
    $success = 0;
    $failed = 0;
    $skip = 0;

    if( ! empty($ds))
    {
      $rows = $ds->rows;
      $locs = $ds->locs;
      $digit = $ds->digit;
      $start = $ds->start;
      $end = $ds->end;

      foreach($rows as $row)
      {
        $i = $ds->start;
        $e = $ds->end;

        while($i <= $e) {
          $col = $ds->locs;

          foreach($col as $loc)
          {
            $col = sprintf('%0'.$ds->digit.'d', $i);
            $code = $row."-".$col."-".$loc;

            $codes[] = (object) array(
              'code' => $code,
              'row' => $row,
              'col' => $col,
              'loc' => $loc
            );
          }

          $i++;
        }
      }
    }

    if( ! empty($codes))
    {
      foreach($codes as $rs)
      {
        $full_code = $ds->warehouse_code."-".$rs->code;

        if( ! $this->zone_model->is_exists_code($full_code))
        {
          $arr = array(
            'code' => $full_code,
            'barcode' => $full_code,
            'name' => $rs->code,
            'warehouse_id' => $ds->warehouse_id,
            'warehouse_code' => $ds->warehouse_code,
            'row' => $rs->row,
            'col' => $rs->col,
            'loc' => $rs->loc,
            'active' => $ds->active,
            'freeze' => $ds->freeze,
            'create_by' => $this->_user->id
          );

          if( ! $this->zone_model->add($arr))
          {
            $failed++;
          }
          else
          {
            $success++;
          }
        }
        else
        {
          $skip++;
        }

        $gen++;
      }
    }

    $message  = "Generate ".number($gen)." locations.<br/>";
    $message .= "Success ".number($success)." locations.<br/>";
    $message .= "Failed ".number($failed)." locations.<br/>";
    $message .= "Skip ".number($skip)." locations";

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? $message : $this->error
    );

    echo json_encode($arr);
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

      if( ! empty($ds))
      {
        if( ! $this->zone_model->is_exists_code($ds->full_code))
        {
          if( ! $this->zone_model->is_exists_barcode($ds->barcode))
          {
            $arr = array(
              'code' => $ds->full_code,
              'barcode' => $ds->barcode,
              'name' => $ds->name,
              'warehouse_id' => $ds->whs_id,
              'warehouse_code' => $ds->whs_code,
              'row' => $ds->row,
              'col' => $ds->col,
              'loc' => $ds->loc,
              'freeze' => $ds->freeze,
              'active' => $ds->active,
              'create_by' => $this->_user->id
            );

            if( ! $this->zone_model->add($arr))
            {
              $sc = FALSE;
              set_error('insert');
            }
          }
          else
          {
            $sc = FALSE;
            set_error('exists', "barcode {$ds->barcode}");
          }
        }
        else
        {
          $sc = FALSE;
          set_error('exists', "Location {$ds->full_code}");
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

    $this->_json_response($sc);
  }


  public function update()
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        if( ! $this->zone_model->is_exists_code($ds->full_code, $ds->id))
        {
          if( ! $this->zone_model->is_exists_barcode($ds->barcode, $ds->id))
          {
            $arr = array(
              'code' => $ds->full_code,
              'barcode' => $ds->barcode,
              'name' => $ds->name,
              'warehouse_id' => $ds->whs_id,
              'warehouse_code' => $ds->whs_code,
              'row' => $ds->row,
              'col' => $ds->col,
              'loc' => $ds->loc,
              'freeze' => $ds->freeze,
              'active' => $ds->active,
              'date_update' => now(),
              'update_by' => $this->_user->id
            );

            if( ! $this->zone_model->update($ds->id, $arr))
            {
              $sc = FALSE;
              set_error('update');
            }
          }
          else
          {
            $sc = FALSE;
            set_error('exists', "barcode {$ds->barcode}");
          }
        }
        else
        {
          $sc = FALSE;
          set_error('exists', "Location {$ds->full_code}");
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

    $this->_json_response($sc);
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


  public function edit($id)
  {
    if($this->pm->can_edit)
    {
      $zone = $this->zone_model->get($id);

      if( ! empty($zone))
      {
        $ds['zone'] = $zone;
        $this->load->view('masters/zone/zone_edit', $ds);
      }
      else
      {
        $this->error_page();
      }
    }
    else
    {
      $this->deny_page();
    }
  }


  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $id = $this->input->post('id');

      if( ! empty($id))
      {
        if( ! $this->zone_model->has_transection($id))
        {
          if( ! $this->zone_model->delete($id))
          {
            $sc = FALSE;
            set_error('delete');
          }
        }
        else
        {
          $sc = FALSE;
          set_error('transection');
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

    echo $sc === TRUE ? 'success' : $this->error;
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
        if(!empty($customer))
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
          if(!empty($emp))
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


  public function syncData()
  {
    ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

    $last_sync = $this->zone_model->get_last_sync_date();
    $newData = $this->zone_model->get_new_data($last_sync);

    if(!empty($newData))
    {
      foreach($newData as $rs)
      {
        if($this->zone_model->is_exists_id($rs->id))
        {
          $ds = array(
            'code' => $rs->code,
            'name' => is_null($rs->name) ? '' : $rs->name,
						'warehouse_code' => $rs->warehouse_code,
            'old_code' => $rs->old_code,
            'active' => $rs->Disabled == 'N' ? 1 : 0,
            'last_sync' => date('Y-m-d H:i:s'),
          );

          $this->zone_model->update($rs->id, $ds);
        }
        else
        {
          $ds = array(
            'id' => $rs->id,
            'code' => $rs->code,
            'name' => is_null($rs->name) ? '' : $rs->name,
            'warehouse_code' => $rs->warehouse_code,
            'active' => $rs->Disabled == 'N' ? 1 : 0,
            'last_sync' => date('Y-m-d H:i:s'),
            'old_code' => $rs->old_code
          );

          $this->zone_model->add($ds);
        }
      }
    }

    echo 'done';
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
    if(!empty($code) && !empty($warehouse_code))
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


    if(!empty($list))
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
    $filter = array(
      'z_code',
      'z_barcode',
      'z_row',
      'z_col',
      'z_loc',
      'z_warehouse',
      'z_active',
      'z_freeze'
    );

    return clear_filter($filter);
  }

} //--- end class

 ?>
