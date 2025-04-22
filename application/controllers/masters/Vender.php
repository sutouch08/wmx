<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Vender extends PS_Controller
{
  public $menu_code = 'DBVEND';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = '';
	public $title = 'เพิ่ม/แก้ไข ผู้ผลิต';
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/vender';
    $this->load->model('masters/vender_model');
  }


  public function index()
  {
		$filter = array(
			'code' => get_filter('code', 'vd_code', ''),
			'name' => get_filter('name', 'vd_name', ''),
			'phone' => get_filter('phone', 'vd_phone', ''),
			'status' => get_filter('status', 'vd_status', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows = $this->vender_model->count_rows($filter);
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
		$vender = $this->vender_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

		$filter['data'] = $vender;

		$this->pagination->initialize($init);
    $this->load->view('masters/vender/vender_list', $filter);
  }


	public function add_new()
	{
    $autorun = is_true(getConfig('AUTO_RUN_VENDER_CODE'));
    $no = 0;
    $code = NULL;

    if($autorun)
    {
      $no = $this->vender_model->get_max_no();
      $no = $no === NULL ? 0 : $no;
      $code = $this->get_new_code($no);
    }

    $this->load->view('masters/vender/vender_add', ['autorun' => $autorun, 'code' => $code, 'run_no' => $no]);
	}


	public function add()
	{
		$sc = TRUE;

    $ds = json_decode($this->input->post('data'));

		if( ! empty($ds))
		{

			if($this->vender_model->is_exists_code($ds->code))
			{
				$sc = FALSE;
				$this->error = "รหัสซ้ำ กรุณากำหนดรหัสใหม่";
			}

      if($sc === TRUE)
      {
        if($this->vender_model->is_exists_name($ds->name))
        {
          $sc = FALSE;
          $this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
        }
      }

      if($sc === TRUE)
			{
        $run_no = $ds->run_no + 1;

				$arr = array(
					'code' => $ds->code,
					'name' => $ds->name,
          'credit_term' => get_null($ds->credit_term),
          'tax_id' => get_null($ds->tax_id),
          'branch_code' => get_null($ds->branch_code),
          'branch_name' => get_null($ds->branch_name),
					'address' => get_null($ds->address),
					'phone' => get_null($ds->phone),
					'status' => $ds->active == 0 ? 0 : 1,
          'run_no' => $run_no
				);

				if( ! $this->vender_model->add($arr))
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


  public function edit($id)
	{
		$vender = $this->vender_model->get($id);

    if( ! empty($vender))
    {
      $this->load->view('masters/vender/vender_edit', $vender);
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
      $vender = $this->vender_model->get($ds->id);

      if( ! empty($vender))
      {
        if($this->vender_model->is_exists_name($ds->name, $vender->code))
        {
          $sc = FALSE;
          $this->error = "ชื่อซ้ำ กรุณากำหนดชื่อใหม่";
        }

        if($sc === TRUE)
  			{
  				$arr = array(
  					'name' => $ds->name,
            'credit_term' => get_null($ds->credit_term),
            'tax_id' => get_null($ds->tax_id),
            'branch_code' => get_null($ds->branch_code),
            'branch_name' => get_null($ds->branch_name),
  					'address' => get_null($ds->address),
  					'phone' => get_null($ds->phone),
  					'status' => $ds->active == 0 ? 0 : 1
  				);

  				if( ! $this->vender_model->update($vender->id, $arr))
  				{
  					$sc = FALSE;
  					set_error('insert');
  				}
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

		$this->_response($sc);
	}


  public function view_detail($id)
  {
    $vender = $this->vender_model->get($id);

    if( ! empty($vender))
    {
      $this->load->view('masters/vender/vender_view', $vender);
    }
    else
    {
      $this->page_error();
    }
  }


	public function delete()
	{
		$sc = TRUE;

		if($this->input->post('id'))
		{
			$id = $this->input->post('id');

      $vender = $this->vender_model->get($id);

      if( ! empty($vender))
      {
        if( ! $this->vender_model->has_transection($vender->code))
        {
          if( ! $this->vender_model->delete($id))
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
        set_error('notfound');
      }
		}
		else
		{
			$sc = FALSE;
			set_error('required');
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function get_new_code($no)
  {
    $prefix = getConfig('PREFIX_VENDER_CODE');
    $run_digit = getConfig('RUN_DIGIT_VENDER_CODE');

    if( ! is_null($no))
    {
      $run_no = intval($no) + 1;
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function clear_filter()
	{
		$filter = array('vd_code', 'vd_name', 'vd_phone', 'vd_status');
		clear_filter($filter);
		echo 'done';
	}
}

?>
