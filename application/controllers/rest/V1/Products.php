<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Products extends REST_Controller
{
  public $error;
  public $user;

  public function __construct()
  {
    parent::__construct();

    $this->load->model('masters/products_model');
    $this->user = 'api@warrix';
  }

  //--- for check stock
	public function countUpdateItem_get()
	{
		$json = file_get_contents("php://input");
		$data = json_decode($json);

		if(! empty($data))
		{
			$last_sync = empty($data->date) ? '2020-01-01 00:00:00' : $data->date;

			$rs = $this->db
      ->where('count_stock', 1)
      ->where('barcode IS NOT NULL', NULL, FALSE)
      ->where('barcode !=', '')
      ->group_start()
      ->where('date_add >', $last_sync)
      ->or_where('date_upd >', $last_sync)
      ->group_end()
      ->count_all_results('products');

			$arr = array(
				'status' => TRUE,
				'count' => $rs
			);

			$this->response($arr, 200);
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => 'Missing required parameter'
			);

			$this->response($arr, 400);
		}

	}

  //---- for check stock
	public function getUpdateItem_get()
	{
		$json = file_get_contents("php://input");
		$ds = json_decode($json);

		if(! empty($ds))
		{
			$date = $ds->date;
			$limit = $ds->limit;
			$offset = $ds->offset;

			$rs = $this->db
      ->select('id, code, name, barcode, style_code, cost, price')
      ->select('color_code, size_code, group_code, main_group_code')
      ->select('sub_group_code, category_code, kind_code, type_code')
      ->select('brand_code, year, unit_code, active')
      ->where('count_stock', 1)
      ->where('barcode IS NOT NULL', NULL, FALSE)
      ->where('barcode !=', '')
      ->group_start()
      ->where('date_add >', $date)
      ->or_where('date_upd >', $date)
      ->group_end()
			->limit($limit, $offset)
			->get('products');

			if($rs->num_rows() > 0)
			{
        $arr = array(
          'status' => TRUE,
          'rows' => $rs->num_rows(),
          'items' => $rs->result()
        );
			}
      else
      {
        $arr = array(
          'status' => TRUE,
          'rows' => 0,
          'items' => NULL
        );
      }

      $this->response($arr, 200);
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => 'Missing required parameter'
			);

			$this->response($arr, 400);
		}
	}  
} //--- end class
