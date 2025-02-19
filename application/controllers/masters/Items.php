<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Items extends PS_Controller
{
  public $menu_code = 'DBITEM';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข รายการสินค้า';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/items';
    $this->load->model('masters/products_model');
    $this->load->model('master_logs_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'item_code', ''),
      'name' => get_filter('name', 'item_name', ''),
      'barcode' => get_filter('barcode', 'item_barcode', ''),
      'model' => get_filter('model', 'item_model', ''),
      'active' => get_filter('active', 'active', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->products_model->count_rows($filter);
      $filter['data'] = $this->products_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $this->load->view('masters/items/items_list', $filter);
    }
  }

  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('masters/items/items_add');
    }
    else
    {
      $this->deny_page();
    }
  }


  public function add()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if(! empty($ds) && ! empty($ds->code) && ! empty($ds->name) && ! empty($ds->barcode))
    {
      if($this->products_model->is_exists_code($ds->code))
      {
        $sc = FALSE;
        set_error('exists', $ds->code);
      }

      if($sc === TRUE)
      {
        if($this->products_model->is_exists_barcode($ds->barcode))
        {
          $sc = FALSE;
          set_error('exists', $ds->barcode);
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
          'code' => trim($ds->code),
          'name' => trim($ds->name),
          'barcode' => trim($ds->barcode),
          'model_code' => get_null(trim($ds->model)),
          'color_code' => get_null(trim($ds->color)),
          'size_code' => get_null(trim($ds->size)),
          'group_code' => get_null(trim($ds->group)),
          'main_group_code' => get_null(trim($ds->main_group)),
          'category_code' => get_null(trim($ds->category)),
          'kind_code' => get_null(trim($ds->kind)),
          'type_code' => get_null(trim($ds->type)),
          'brand_code' => get_null(trim($ds->brand)),
          'collection_code' => get_null(trim($ds->collection)),
          'year' => get_null($ds->year),
          'cost' => round($ds->cost, 2),
          'price' => round($ds->price, 2),
          'unit_code' => empty($ds->unit) ? 'PCS': trim($ds->unit),
          'count_stock' => empty($ds->count) ? 0 : 1,
          'active' => empty($ds->active) ? 0 : 1,
          'create_user' => $this->_user->uname
        );

        if( ! $this->products_model->add($arr))
        {
          $sc = FALSE;
          set_error('insert');
        }

        if($sc === TRUE)
        {
          $logs = array(
            'code' => $ds->code,
            'type' => 'item',
            'action' => 'add',
            'user' => $this->_user->uname,
            'before_json' => NULL,
            'update_json' => json_encode($arr)
          );

          $this->master_logs_model->add_logs($logs);
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_json_response($sc);
  }


  public function edit($id)
  {
    if($this->pm->can_edit)
    {
      $item = $this->products_model->get_by_id($id);

      if(! empty($item))
      {
        $this->load->view('masters/items/items_edit', ['item' => $item]);
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

    $ds = json_decode($this->input->post('data'));

    if(! empty($ds) && ! empty($ds->id) && ! empty($ds->code))
    {
      $item = $this->products_model->get($ds->id);

      if( ! empty($item))
      {
        if( ! $this->products_model->is_exists_barcode($ds->barcode, $ds->id))
        {
          $arr = array(
            'name' => trim($ds->name),
            'barcode' => trim($ds->barcode),
            'model_code' => get_null(trim($ds->model)),
            'color_code' => get_null(trim($ds->color)),
            'size_code' => get_null(trim($ds->size)),
            'group_code' => get_null(trim($ds->group)),
            'main_group_code' => get_null(trim($ds->main_group)),
            'category_code' => get_null(trim($ds->category)),
            'kind_code' => get_null(trim($ds->kind)),
            'type_code' => get_null(trim($ds->type)),
            'brand_code' => get_null(trim($ds->brand)),
            'collection_code' => get_null(trim($ds->collection)),
            'year' => $ds->year,
            'cost' => round($ds->cost, 2),
            'price' => round($ds->price, 2),
            'unit_code' => empty($ds->unit) ? 'PCS': trim($ds->unit),
            'count_stock' => empty($ds->count) ? 0 : 1,
            'active' => empty($ds->active) ? 0 : 1,
            'update_user' => $this->_user->uname,
            'date_upd' => now()
          );

          if( ! $this->products_model->update($ds->id, $arr))
          {
            $sc = FALSE;
            set_error('update');
          }

          if($sc === TRUE)
          {
            $logs = array(
              'code' => $ds->code,
              'type' => 'item',
              'action' => 'update',
              'user' => $this->_user->uname,
              'before_json' => json_encode($item),
              'update_json' => json_encode($arr)
            );

            $this->master_logs_model->add_logs($logs);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Barcode {$ds->barcode} already exists";
        }
      }
      else
      {
        $sc = FALSE;
        set_error('notfound');
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_json_response($sc);
  }


  public function view_detail($id)
  {
    $item = $this->products_model->get_by_id($id);

    if(! empty($item))
    {
      $this->load->view('masters/items/items_detail', ['item' => $item]);
    }
    else
    {
      $this->page_error();
    }
  }


  public function delete_item()
  {
    $sc = TRUE;
    $id = $this->input->get('id');
    $item = $this->products_model->get_by_id($id);

    if( ! empty($item))
    {
      if(! $this->products_model->has_transection($item->code))
      {
        if(! $this->products_model->delete_item_by_id($id))
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


  public function clear_filter()
  {
    $filter = array('item_code','item_name','item_barcode','item_model','active');
    clear_filter($filter);
  }

}

?>
