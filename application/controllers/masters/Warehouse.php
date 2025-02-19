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
      'active' => get_filter('active', 'wh_active', 'all'),
      'freeze' => get_filter('freeze', 'wh_freeze', 'all'),
      'auz' => get_filter('auz', 'wh_auz', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
  		$rows = $this->warehouse_model->count_rows($filter);
  		$list = $this->warehouse_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);

      if( ! empty($list))
      {
        foreach($list as $rs)
        {
          $rs->zone_count = $this->warehouse_model->count_zone($rs->id);
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


  public function add()
  {
    $sc = TRUE;

    if($this->pm->can_add)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        if( ! $this->warehouse_model->is_exists_code(trim($ds->code)))
        {
          if( ! $this->warehouse_model->is_exists_name(trim($ds->name)))
          {
            $arr = array(
              'code' => trim($ds->code),
              'name' => trim($ds->name),
              'active' => $ds->active,
              'freeze' => $ds->freeze,
              'auz' => $ds->auz,
              'create_by' => $this->_user->id
            );

            if( ! $this->warehouse_model->add($arr))
            {
              $sc = FALSE;
              set_error('insert');
            }
          }
          else
          {
            $sc = FALSE;
            set_error('exists', $ds->name);
          }
        }
        else
        {
          $sc = FALSE;
          set_error('exists', $ds->code);
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


  public function edit($id)
  {
    if($this->pm->can_edit)
    {
      $wh = $this->warehouse_model->get($id);

      if( ! empty($wh))
      {
        $ds['wh'] = $wh;
        $this->load->view('masters/warehouse/warehouse_edit', $ds);
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


  public function update()
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds))
      {
        $wh = $this->warehouse_model->get($ds->id);

        if( ! empty($wh))
        {
          if( ! $this->warehouse_model->is_exists_name(trim($ds->name), $ds->id))
          {
            $arr = array(
              'name' => trim($ds->name),
              'freeze' => $ds->freeze,
              'auz' => $ds->auz,
              'active' => $ds->active,
              'date_update' => now(),
              'update_by' => $this->_user->id
            );

            if( ! $this->warehouse_model->update($ds->id, $arr))
            {
              $sc = FALSE;
              set_error('update');
            }
          }
          else
          {
            $sc = FALSE;
            set_error('exists', $ds->name);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Warehouse not found";
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


  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $id = $this->input->post('id');

      if($id)
      {
        $wh = $this->warehouse_model->get($id);

        if( ! empty($wh))
        {
          if( ! $this->warehouse_model->has_transection($id))
          {
            if( ! $this->warehouse_model->delete($id))
            {
              $sc = FALSE;
              set_error('delete');
            }
          }
          else
          {
            $sc = FALSE;
            set_error('transection', $wh->code);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Warehouse not found";
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


  public function view_detail($id)
  {
    $wh = $this->warehouse_model->get($id);

    if( ! empty($wh))
    {
      $ds['wh'] = $wh;
      $this->load->view('masters/warehouse/warehouse_view_detail', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function import_location_file($warehouse_id, $warehouse_code)
	{
    ini_set('max_execution_time', 1200);
    ini_set('memory_limit','1000M');

    $this->load->library('excel');
    $this->load->model('masters/zone_model');

    $sc = TRUE;
    $import = 0;
    $failed = 0;
    $success = 0;
    $duplicate = 0;
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
  	$path = $this->config->item('upload_path').'zone/';
    $file	= 'uploadFile';
    $config = array(
      "allowed_types" => "xlsx",
      "upload_path" => $path,
      "file_name"	=> "Zone-import",
      "max_size" => 5120,
      "overwrite" => TRUE
    );

    $this->load->library("upload", $config);

    if(! $this->upload->do_upload($file))
    {
      $sc = FALSE;
      $this->error = $this->upload->display_errors();
    }

    if($sc === TRUE)
    {
      //---- checking data
      $info = $this->upload->data();
      /// read file
      $excel = PHPExcel_IOFactory::load($info['full_path']);
      //get only the Cell Collection
      $collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

      $i = 1;
      $count = count($collection);
      $limit = 1000; //intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;

      if($count <= $limit)
      {
        foreach($collection as $cs)
        {
          if($sc === FALSE)
          {
            break;
          }

          if($i === 1)
          {
            $i++;

            $headCol = array(
              'A' => 'Code',
              'B' => 'Barcode',
              'C' => 'Name'
            );

            foreach($headCol as $col => $field)
            {
              if($cs[$col] !== $field)
              {
                $sc = FALSE;
                $this->error = 'Column '.$col.' Should be '.$field;
                break;
              }
            }
          }
          else
          {
            if( ! empty(trim($cs['A'])))
            {
              $code = trim($cs['A']);
              $barcode = empty(trim($cs['B'])) ? $code : trim($cs['B']);
              $name = empty(trim($cs['C'])) ? $code : trim($cs['C']);
              $full_code = $warehouse_code."-".$code;

              $zone = $this->zone_model->get_by_code($full_code);

              if( empty($zone))
              {
                $arr = array(
                  'code' => $full_code,
                  'barcode' => $barcode,
                  'name' => $name,
                  'warehouse_id' => $warehouse_id,
                  'warehouse_code' => $warehouse_code,
                  'create_by' => $this->_user->id
                );

                if($this->zone_model->add($arr))
                {
                  $success++;
                }
                else {
                  $failed++;
                }
              }
              else
              {
                $duplicate++;
              }

              $import++;
              $i++;
            }
          }  //--- end if $i === 1
        } //--- foreach collection
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไฟล์มีรายการเกิน {$limit} บรรทัด";
      }
    }

    $message = "Import ".number($import)." locations<br/>
      Success ".number($success)." locations<br/>
      Skip ".number($duplicate)." locations<br/>
      Failed ".number($failed)." locations";

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? $message : $this->error
    );

    echo json_encode($arr);
	}


  public function clear_filter()
  {
    $filter = array('wh_code', 'wh_active', 'wh_freeze', 'wh_auz');
    return clear_filter($filter);
  }

} //--- end class

 ?>
