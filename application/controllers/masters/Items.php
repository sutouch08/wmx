<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Items extends PS_Controller
{
  public $menu_code = 'DBITEM';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข รายการสินค้า';
  public $error = '';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/items';

    //--- load model
    $this->load->model('masters/products_model');
    $this->load->model('masters/product_main_group_model');
    $this->load->model('masters/product_group_model');
    $this->load->model('masters/product_segment_model');
    $this->load->model('masters/product_class_model');
    $this->load->model('masters/product_family_model');
    $this->load->model('masters/product_type_model');
    $this->load->model('masters/product_kind_model');
    $this->load->model('masters/product_gender_model');
    $this->load->model('masters/product_sport_type_model');
    $this->load->model('masters/product_collection_model');
    $this->load->model('masters/product_model_model');
    $this->load->model('masters/product_brand_model');
    $this->load->model('masters/product_color_model');
    $this->load->model('masters/product_size_model');
    $this->load->model('masters/product_image_model');

    //---- load helper
    $this->load->helper('product');
    $this->load->helper('product_images');
    $this->load->helper('unit');

  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'item_code', ''),
      'name' => get_filter('name', 'item_name', ''),
      'barcode' => get_filter('barcode', 'item_barcode', ''),
      'color' => get_filter('color', 'color' ,''),
      'size' => get_filter('size', 'size', ''),
      'main_group' => get_filter('main_group', 'main_group', 'all'),
      'group' => get_filter('group', 'group', 'all'),
      'segment' => get_filter('segment', 'segment', 'all'),
      'class' => get_filter('class', 'class', 'all'),
      'family' => get_filter('family', 'family', 'all'),
      'kind' => get_filter('kind', 'kind', 'all'),
      'type' => get_filter('type', 'type', 'all'),
      'gender' => get_filter('gender', 'gender', 'all'),
      'sport_type' => get_filter('sport_type', 'sport_type', 'all'),
      'collection' => get_filter('collection', 'collection', 'all'),
      'brand' => get_filter('brand', 'brand', 'all'),
      'year' => get_filter('year', 'year', 'all'),
      'active' => get_filter('active', 'active', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
      exit();
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();
      $segment = 4; //-- url segment
      $rows = $this->products_model->count_rows($filter);
      //--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
      $filter['data'] = $this->products_model->get_list($filter, $perpage, $this->uri->segment($segment));

      $this->pagination->initialize($init);
      $this->load->view('masters/product_items/items_list', $filter);

    }
  }


  public function add_new()
  {
    $this->load->view('masters/product_items/items_add_view');
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $code = trim($ds->code);

      if($this->products_model->is_exists($code))
      {
        $sc = FALSE;
        $this->error = "{$code} already exists";
      }

      if($sc === TRUE)
      {
        $user = $this->_user->uname;

        $arr = array(
          'code' => $code,
          'name' => trim($ds->name),
          'barcode' => get_null(trim($ds->barcode)),
          'style_code' => get_null($ds->style),
          'color_code' => get_null($ds->color_code),
          'size_code' => get_null($ds->size_code),
          'group_code' => get_null($ds->group_code),
					'main_group_code' => get_null($ds->main_group_code),
          'sub_group_code' => get_null($ds->sub_group_code),
          'category_code' => get_null($ds->category_code),
          'kind_code' => get_null($ds->kind_code),
          'type_code' => get_null($ds->type_code),
          'brand_code' => get_null($ds->brand_code),
          'collection_code' => get_null($ds->collection_code),
          'year' => $ds->year,
          'cost' => round($ds->cost, 2),
          'price' => round($ds->price, 2),
          'unit_code' => $ds->unit_code,
          'count_stock' => $ds->count_stock == 0 ? 0 : 1,
          'can_sell' => $ds->can_sell == 0 ? 0 : 1,
          'active' => $ds->active == 0 ? 0 : 1,
          'is_api' => $ds->is_api == 0 ? 0 : 1,
          'update_user' => $user,
          'old_style' => get_null($ds->old_style),
          'old_code' => get_null($ds->old_code)
        );

        if( ! $this->products_model->add($arr))
        {
          $sc = FALSE;
          $this->error = "Failed to add new item";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function edit($id)
  {
    $item = $this->products_model->get_by_id($id);

    if( ! empty($item))
    {
      $this->load->view('masters/product_items/items_edit_view', $item);
    }
    else
    {
      $this->page_error();
    }
  }


  public function view_detail($id)
  {
    $item = $this->products_model->get_by_id($id);

    if( ! empty($item))
    {
      $this->load->view('masters/product_items/items_view_detail', $item);
    }
    else
    {
      $this->page_error();
    }
  }


	public function update()
  {
		$sc = TRUE;
		$ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{
      $id = $ds->id;
			$code = $ds->code;

      $arr = array(
        'name' => trim($ds->name),
        'barcode' => get_null(trim($ds->barcode)),
        'model_code' => get_null($ds->model_code),
        'color_code' => get_null($ds->color_code),
        'size_code' => get_null($ds->size_code),
        'main_group_code' => get_null($ds->main_group_code),
        'main_group_name' => empty($ds->main_group_name) ? NULL : get_null($ds->main_group_name),
        'group_code' => get_null($ds->group_code),
        'group_name' => empty($ds->group_name) ? NULL : get_null($ds->group_name),
        'segment_code' => get_null($ds->segment_code),
        'segment_name' => empty($ds->segment_name) ? NULL : get_null($ds->segment_name),
        'class_code' => get_null($ds->class_code),
        'class_name' => empty($ds->class_name) ? NULL : get_null($ds->class_name),
        'family_code' => get_null($ds->family_code),
        'family_name' => empty($ds->family_name) ? NULL : get_null($ds->family_name),
        'type_code' => get_null($ds->type_code),
        'type_name' => empty($ds->type_name) ? NULL : get_null($ds->type_name),
        'kind_code' => get_null($ds->kind_code),
        'kind_name' => empty($ds->kind_name) ? NULL : get_null($ds->kind_name),
        'gender_code' => get_null($ds->gender_code),
        'gender_name' => empty($ds->gender_name) ? NULL : get_null($ds->gender_name),
        'sport_type_code' => get_null($ds->sport_type_code),
        'sport_type_name' => empty($ds->sport_type_name) ? NULL : get_null($ds->sport_type_name),
        'collection_code' => get_null($ds->collection_code),
        'collection_name' => empty($ds->collection_name) ? NULL : get_null($ds->collection_name),
        'brand_code' => get_null($ds->brand_code),
        'brand_name' => empty($ds->brand_name) ? NULL : get_null($ds->brand_name),
        'year' => $ds->year,
        'cost' => round($ds->cost, 2),
        'price' => round($ds->price, 2),
        'unit_code' => $ds->unit_code,
        'count_stock' => empty($ds->count_stock) ? 0 : 1,
        'active' => empty($ds->active) ? 0 : 1,
        'is_api' => empty($ds->is_api) ? 0 : 1,
        'api_rate' => get_zero($ds->api_rate),
        'update_user' => $this->_user->uname
      );

      if( ! $this->products_model->update($code, $arr))
      {
  			$sc = FALSE;
  			set_error('update');
      }
		}
		else
		{
			$sc = FALSE;
		  set_error('required');
		}

		$this->_response($sc);
  }



  public function toggle_active($code)
  {
    $status = $this->products_model->get_status('active', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('active', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }



  public function toggle_api($code)
  {
    $status = $this->products_model->get_status('is_api', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('is_api', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }


  public function delete_item()
  {
    $sc = TRUE;
    $id = $this->input->get('id');
    $item = $this->products_model->get_by_id($id);

    if( ! empty($item))
    {
      if( ! $this->products_model->has_transection($item->code))
      {
        if( ! $this->products_model->delete_item_by_id($id))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถลบ {$item->code} ได้ เนื่องจากสินค้ามี Transcetion เกิดขึ้นแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function download_template($token)
  {
    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Items Master Template');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', 'Code');
    $this->excel->getActiveSheet()->setCellValue('B1', 'Name');
    $this->excel->getActiveSheet()->setCellValue('C1', 'Barcode');
    $this->excel->getActiveSheet()->setCellValue('D1', 'Model');
    $this->excel->getActiveSheet()->setCellValue('E1', 'Color');
    $this->excel->getActiveSheet()->setCellValue('F1', 'Size');
    $this->excel->getActiveSheet()->setCellValue('G1', 'Group');
    $this->excel->getActiveSheet()->setCellValue('H1', 'MainGroup');
    $this->excel->getActiveSheet()->setCellValue('I1', 'SubGroup');
    $this->excel->getActiveSheet()->setCellValue('J1', 'Category');
    $this->excel->getActiveSheet()->setCellValue('K1', 'Kind');
    $this->excel->getActiveSheet()->setCellValue('L1', 'Type');
    $this->excel->getActiveSheet()->setCellValue('M1', 'Brand');
    $this->excel->getActiveSheet()->setCellValue('N1', 'Collection');
    $this->excel->getActiveSheet()->setCellValue('O1', 'Year');
    $this->excel->getActiveSheet()->setCellValue('P1', 'Cost');
    $this->excel->getActiveSheet()->setCellValue('Q1', 'Price');
    $this->excel->getActiveSheet()->setCellValue('R1', 'Unit');
    $this->excel->getActiveSheet()->setCellValue('S1', 'CountStock');
    $this->excel->getActiveSheet()->setCellValue('T1', 'IsAPI');
    $this->excel->getActiveSheet()->setCellValue('U1', 'OldModel');
    $this->excel->getActiveSheet()->setCellValue('V1', 'OldCode');


    setToken($token);

    $file_name = "Items_master_template.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }

  public function clear_filter()
	{
    $filter = array(
      'item_code',
      'item_name',
      'item_barcode',
      'color',
      'size',
      'main_group',
      'group',
      'segment',
      'class',
      'family',
      'kind',
      'type',
      'gender',
      'sport_type',
      'brand',
      'collection',
      'year',
      'active'
    );

    return clear_filter($filter);
	}
}

?>
