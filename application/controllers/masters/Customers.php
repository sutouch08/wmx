<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Customers extends PS_Controller
{
  public $menu_code = 'DBCUST';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'เพิ่ม/แก้ไข รายชื่อลูกค้า';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/customers';
    $this->load->model('masters/customers_model');
    $this->load->model('masters/customer_group_model');
    $this->load->model('masters/customer_kind_model');
    $this->load->model('masters/customer_type_model');
    $this->load->model('masters/customer_class_model');
    $this->load->model('masters/customer_area_model');
    $this->load->helper('customer');
    $this->load->helper('saleman');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'cu_code', ''),
      'group' => get_filter('group', 'cu_group', 'all'),
      'kind' => get_filter('kind', 'cu_kind', 'all'),
      'type' => get_filter('type', 'cu_type', 'all'),
      'class' => get_filter('class', 'cu_class', 'all'),
      'area' => get_filter('area', 'cu_area', 'all'),
      'status' => get_filter('status', 'cu_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows = $this->customers_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$customers = $this->customers_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

    $filter['data'] = $customers;

		$this->pagination->initialize($init);
    $this->load->view('masters/customers/customers_list', $filter);
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('masters/customers/customers_add');
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

      if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->name))
      {
        if($this->customers_model->is_exists($ds->code))
        {
          $sc = FALSE;
          set_error('exists', $ds->code);
        }

        if($sc === TRUE && $this->customers_model->is_exists_name($ds->name))
        {
          $sc = FALSE;
          set_error('exists', $ds->name);
        }

        if($sc === TRUE)
        {
          $arr = array(
            'code' => $ds->code,
            'name' => $ds->name,
            'Tax_Id' => get_null($ds->tax_id),
            'group_code' => get_null($ds->group),
            'kind_code' => get_null($ds->kind),
            'type_code' => get_null($ds->type),
            'class_code' => get_null($ds->class),
            'area_code' => get_null($ds->area),
            'sale_code' => get_null($ds->sale_code),
            'active' => $ds->active == 0 ? 0 : 1
          );

          if( ! $this->customers_model->add($arr))
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


  public function update()
  {
    $sc = TRUE;

    if($this->pm->can_edit)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->name))
      {
        if($sc === TRUE && $this->customers_model->is_exists_name($ds->name, $ds->id))
        {
          $sc = FALSE;
          set_error('exists', $ds->name);
        }

        if($sc === TRUE)
        {
          $arr = array(
            'name' => $ds->name,
            'Tax_Id' => get_null($ds->tax_id),
            'group_code' => get_null($ds->group),
            'kind_code' => get_null($ds->kind),
            'type_code' => get_null($ds->type),
            'class_code' => get_null($ds->class),
            'area_code' => get_null($ds->area),
            'sale_code' => get_null($ds->sale_code),
            'active' => $ds->active == 0 ? 0 : 1
          );

          if( ! $this->customers_model->update_by_id($ds->id, $arr))
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


  public function view_detail($id, $tab = 'infoTab')
  {
    $this->load->model('address/customer_address_model');
    $this->load->model('address/address_model');
    $customer = $this->customers_model->get_by_id($id);

    if( ! empty($customer))
    {
      $bill_to = $this->customer_address_model->get_customer_bill_to_address($customer->code);
      $ship_to = $this->customer_address_model->get_ship_to_address($customer->code);

      $data['ds'] = $customer;
      $data['tab'] = $tab;
      $data['bill'] = $bill_to;
      $data['addr'] = $ship_to;
      $data['view'] = TRUE;

      $this->load->view('masters/customers/customers_detail', $data);
    }
    else
    {
      $this->page_error();
    }
  }


  public function edit($id, $tab='infoTab')
  {
    $this->load->model('address/customer_address_model');
    $this->load->model('address/address_model');
    $customer = $this->customers_model->get_by_id($id);

    if( ! empty($customer))
    {
      $bill_to = $this->customer_address_model->get_customer_address_list($customer->code, 'B'); //$this->customer_address_model->get_customer_bill_to_address($customer->code);
      $ship_to = $this->customer_address_model->get_customer_address_list($customer->code, 'S'); //$this->customer_address_model->get_ship_to_address($customer->code);

      $data['ds'] = $customer;
      $data['tab'] = $tab;
      $data['bill'] = $bill_to;
      $data['addr'] = $ship_to;
      $data['view'] = FALSE;

      $this->load->view('masters/customers/customers_edit', $data);
    }
    else
    {
      $this->page_error();
    }
  }


  public function update_bill_to()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->address))
    {
      $this->load->model('address/customer_address_model');

      $arr = array(
        'customer_code' => $ds->customer_code,
        'branch_code' => empty($ds->branch_code) ? '000' : $ds->branch_code,
        'branch_name' => empty($ds->branch_name) ? 'สำนักงานใหญ่' : $ds->branch_name,
        'address' => $ds->address,
        'sub_district' => get_null($ds->sub_district),
        'district' => get_null($ds->district),
        'province' => get_null($ds->province),
        'postcode' => get_null($ds->postcode),
        'country' => empty($ds->country) ? 'TH' : $ds->country,
        'phone' => get_null($ds->phone)
      );

      $bill_to = $this->customer_address_model->get_customer_bill_to_address($ds->customer_code);

      if( ! empty($bill_to))
      {
        if( ! $this->customer_address_model->update_bill_to_by_id($bill_to->id, $arr))
        {
          $sc = FALSE;
          set_error('insert');
        }
      }
      else
      {
        if( ! $this->customer_address_model->add_bill_to($arr))
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

    $this->_response($sc);
  }


  public function add_ship_to()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $this->load->model('address/customer_address_model');

      $arr = array(
        'adrType' => 'S',
        'customer_code' => $ds->customer_code,
        'customer_name' => $ds->customer_name,
        'consignee' => $ds->consignee,
        'name' => $ds->name,
        'address' => $ds->address,
        'sub_district' => $ds->sub_district,
        'district' => $ds->district,
        'province' => $ds->province,
        'postcode' => $ds->postcode,
        'phone' => $ds->phone
      );

      if( ! empty($ds->id_address))
      {
        if( ! $this->customer_address_model->update($ds->id_address, $arr))
        {
          $sc = FALSE;
          set_error('update');
        }
      }
      else
      {
        if( ! $this->customer_address_model->add($arr))
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

    $this->_response($sc);
  }


  public function delete_ship_to()
  {
    $sc = TRUE;

    $id = $this->input->post('id_address');

    if( ! empty($id))
    {
      $this->load->model('address/customer_address_model');

      if( ! $this->customer_address_model->delete($id))
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


  public function get_ship_to_table()
  {
    $sc = TRUE;
    if($this->input->post('customer_code'))
    {
      $code = $this->input->post('customer_code');

      if(!empty($code))
      {
        $ds = array();
        $this->load->model('address/customer_address_model');
        $adrs = $this->customer_address_model->get_customer_address_list($code, 'S');

        if(!empty($adrs))
        {
          foreach($adrs as $rs)
          {
            $arr = array(
              'id' => $rs->id,
              'consignee' => $rs->consignee,
              'name' => $rs->name,
              'address' => $rs->address.' '.$rs->sub_district.' '.$rs->district.' '.$rs->province.' '.$rs->postcode,
              'phone' => $rs->phone
            );

            array_push($ds, $arr);
          }
        }
        else
        {
          $sc = FALSE;
        }
      }
      else
      {
        $sc = FALSE;
      }
    }

    echo $sc === TRUE ? json_encode($ds) : 'noaddress';
  }


  public function get_ship_to()
  {
    $this->load->model('address/customer_address_model');
    $id = $this->input->post('id_address');
    $rs = $this->customer_address_model->get($id);

    if( ! empty($rs))
    {
      echo json_encode($rs);
    }
    else
    {
      echo 'nodata';
    }
  }


  public function delete()
  {
    $sc = TRUE;

    if($this->pm->can_delete)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->code))
      {
        if($this->customers_model->has_transection($ds->code))
        {
          $sc = FALSE;
          set_error('transection');
        }

        if($sc === TRUE)
        {
          if( ! $this->customers_model->delete_by_id($ds->id))
          {
            $sc = FALSE;
            set_error('delete');
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


  public function clear_filter()
	{
    $filter = array(
      'cu_code',
      'cu_status',
      'cu_group',
      'cu_kind',
      'cu_type',
      'cu_class',
      'cu_area'
    );

    return clear_filter($filter);
	}


  public function get_new_code($code)
  {
    $max = $this->customer_address_model->get_max_code($code);
    $max++;
    return $max;
  }




} //---

?>
