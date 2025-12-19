<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Consignment_order extends PS_Controller
{
  public $menu_code = 'ACCMOD';
	public $menu_group_code = 'AC';
  public $menu_sub_group_code = '';
	public $title = 'ตัดยอดฝากขายเทียม (WD)';
  public $filter;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'mobile/consignment_order';
    $this->load->model('account/consignment_order_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->helper('warehouse');
    $this->load->helper('consign_order');
    $this->load->helper('discount');
    $this->load->helper('print');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'consign_code', ''),
      'customer' => get_filter('customer', 'consign_customer', ''),
      'warehouse' => get_filter('warehouse', 'consign_warehouse', 'all'),
      'user' => $this->_user->uname,
      'from_date' => get_filter('from_date', 'consign_from_date', ''),
      'to_date' => get_filter('to_date', 'consign_to_date', ''),
      'status' => get_filter('status', 'consign_status', 'all'),
      'is_exported' => get_filter('is_exported', 'consign_is_exported', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
      exit();
    }
    else
    {
      $perpage = get_rows();
      $rows = $this->consignment_order_model->count_rows($filter);
      $init	= mobile_pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $filter['data'] = $this->consignment_order_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $this->pagination->initialize($init);
      $this->load->view('mobile/consignment_order/consignment_order_list', $filter);
    }
  }


  public function add_new()
  {
    $this->load->view('mobile/consignment_order/consignment_order_add');
  }


  public function add()
  {
    $sc = TRUE;
    $code = NULL;

    if($this->pm->can_add)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds) && ! empty($ds->warehouse_code) && ! empty($ds->customer_code))
      {
        if($this->warehouse_model->is_exists_consignment_warehouse_customer($ds->warehouse_code, $ds->customer_code))
        {
          $date_add = db_date($ds->date_add, TRUE);
          $code = $this->get_new_code($date_add);
          $shipped_date = db_date($ds->posting_date, TRUE);

          $arr = array(
            'code' => $code,
            'customer_code' => $ds->customer_code,
            'customer_name' => $ds->customer_name,
            'warehouse_code' => $ds->warehouse_code,
            'gp' => empty($ds->gp) ? 0 : $ds->gp,
            'remark' => get_null($ds->remark),
            'date_add' => $date_add,
            'shipped_date' => $shipped_date,
            'user' => $this->_user->uname
          );

          if( ! $this->consignment_order_model->add($arr))
          {
            $sc = FALSE;
            set_error('insert');
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid warehouse and customer OR warehouse and customer missmatch";
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $code
    );

    echo json_encode($arr);
  }


  public function edit($code)
  {
    $doc = $this->consignment_order_model->get($code);

    $details = $this->consignment_order_model->get_details($code);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $rs->barcode = $this->products_model->get_barcode($rs->product_code);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('mobile/consignment_order/consignment_order_edit', $ds);
  }


  public function update()
  {
    $sc = TRUE;

    if($this->pm->can_add OR $this->pm->can_edit)
    {
      $ds = json_decode($this->input->post('data'));

      if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->warehouse_code) && ! empty($ds->customer_code))
      {
        $doc = $this->consignment_order_model->get($ds->code);

        if( ! empty($doc))
        {
          if($doc->status == 'P' OR $doc->status == 'A')
          {
            if($this->warehouse_model->is_exists_consignment_warehouse_customer($ds->warehouse_code, $ds->customer_code))
            {
              $date_add = db_date($ds->date_add, TRUE);
              $code = $ds->code;
              $shipped_date = db_date($ds->posting_date, TRUE);

              $arr = array(
                'customer_code' => $ds->customer_code,
                'customer_name' => $ds->customer_name,
                'warehouse_code' => $ds->warehouse_code,
                'gp' => empty($ds->gp) ? 0 : $ds->gp,
                'remark' => get_null($ds->remark),
                'date_add' => $date_add,
                'shipped_date' => $shipped_date,
                'status' => 'P',
                'update_user' => $this->_user->uname
              );

              if( ! $this->consignment_order_model->update($code, $arr))
              {
                $sc = FALSE;
                set_error('update');
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Invalid warehouse and customer OR warehouse and customer missmatch";
            }
          }
          else
          {
            $sc = FALSE;
            set_error('status');
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
    $this->load->model('approve_logs_model');

    $doc = $this->consignment_order_model->get($code);
    $details = $this->consignment_order_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'logs' => $this->approve_logs_model->get($code)
    );

    $this->load->view('mobile/consignment_order/consignment_order_detail', $ds);
  }


  public function get_detail($id)
  {
    $sc = TRUE;

    $warehouse_code = $this->input->post('warehouse_code');

		$detail = $this->consignment_order_model->get_detail($id);

		if(empty($detail))
		{
			$sc = FALSE;
			$this->error = "Item row not found !";
		}

    $this->load->library('wrx_consignment_api');
		$detail->barcode = $this->products_model->get_barcode($detail->product_code);
    $detail->stock = $detail->count_stock == 1 ? $this->wrx_consignment_api->get_onhand_stock($detail->product_code, $warehouse_code) : 100000;

		$arr = array(
			'status' => $sc === TRUE ? 'success' : 'failed',
			'message' => $sc === TRUE ? 'success' : $this->error,
			'data' => $sc === TRUE ? $detail : NULL
		);

		echo json_encode($arr);
  }


  public function get_item_by_barcode()
  {
    $sc = TRUE;
    $ds = [];
    $barcode = $this->input->post('barcode');
    $warehouse_code = $this->input->post('warehouse_code');

    if( ! empty($barcode) && ! empty($warehouse_code))
    {
      $item = $this->products_model->get_product_by_barcode($barcode);
      $warehouse = NULL;

      if(empty($item))
      {
        $sc = FALSE;
        $this->error = "Invalid item code";
      }

      if($sc === TRUE)
      {
        $warehouse = $this->warehouse_model->get($warehouse_code);

        if(empty($warehouse))
        {
          $sc = FALSE;
          $this->error = "Invalid warehouse";
        }
      }

      if($sc === TRUE)
      {
        $this->load->library('wrx_consignment_api');

        $stock = $item->count_stock ? $this->wrx_consignment_api->get_onhand_stock($item->code, $warehouse->code) : 100000;
        $commit = $item->count_stock ? $this->consignment_order_model->get_commit_qty($item->code, $warehouse->code) : 0;
        $available = $stock - $commit;

        $ds = array(
          'pdCode' => $item->code,
          'pdName' => $item->name,
          'barcode' => $item->barcode,
          'product' => $item->code,
          'price' => number($item->price, 2),
          'disc' => 0,
          'stock' => $available > 0 ? number($available) : 0,
          'count_stock' => $item->count_stock
        );
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


  public function get_item_by_code()
  {
    $sc = TRUE;
    $ds = [];
    $product_code = $this->input->post('product_code');
    $warehouse_code = $this->input->post('warehouse_code');

    if( ! empty($product_code) && ! empty($warehouse_code))
    {
      $item = $this->products_model->get($product_code);
      $warehouse = NULL;

      if(empty($item))
      {
        $sc = FALSE;
        $this->error = "Invalid item code";
      }

      if($sc === TRUE)
      {
        $warehouse = $this->warehouse_model->get($warehouse_code);

        if(empty($warehouse))
        {
          $sc = FALSE;
          $this->error = "Invalid warehouse";
        }
      }

      if($sc === TRUE)
      {
        $this->load->library('wrx_consignment_api');

        $stock = $item->count_stock ? $this->wrx_consignment_api->get_onhand_stock($item->code, $warehouse->code) : 100000;
        $commit = $item->count_stock ? $this->consignment_order_model->get_commit_qty($item->code, $warehouse->code) : 0;
        $available = $stock - $commit;

        $ds = array(
          'pdCode' => $item->code,
          'pdName' => $item->name,
          'barcode' => $item->barcode,
          'product' => $item->code,
          'price' => number($item->price, 2),
          'disc' => 0,
          'stock' => $available > 0 ? number($available) : 0,
          'count_stock' => $item->count_stock
        );
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


  public function add_detail()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code && ! empty($ds->product_code) && ! empty($ds->qty)))
    {
      $doc = $this->consignment_order_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'P' OR $doc->status == 'A')
        {
          $disc = parse_discount_text($ds->disc, $ds->price);
          $discount = $disc['discount_amount'];
          $sell_price = $ds->price - $discount;
          $amount = $sell_price * $ds->qty;

          $item = $this->products_model->get($ds->product_code);

          if(empty($item))
          {
            $sc = FALSE;
            $this->error = "Invalid item code";
          }

          if($sc === TRUE)
          {
            $this->load->library('wrx_consignment_api');
            $input_type = 1;
            $stock = $item->count_stock == 1 ? $this->wrx_consignment_api->get_onhand_stock($item->code, $doc->warehouse_code) : 100000;
            $commit = $item->count_stock ? $this->consignment_order_model->get_commit_qty($item->code, $doc->warehouse_code) : 0;
            $available = $stock - $commit;

            $id = NULL;

            if($available <= 0)
            {
              $sc = FALSE;
              $this->error = "Insufficient stock";
            }
          }

          if($sc === TRUE)
          {
            $detail = $this->consignment_order_model->get_exists_detail($doc->code, $item->code, $ds->price, $ds->disc, $input_type);

            if(empty($detail))
            {
              $arr = array(
                'consign_code' => $doc->code,
                'product_code' => $item->code,
                'product_name' => $item->name,
                'unit_code' => $item->unit_code,
                'count_stock' => $item->count_stock,
                'cost' => $item->cost,
                'price' => $ds->price,
                'sell_price' => $sell_price,
                'qty' => $ds->qty,
                'discount' => $ds->disc,
                'discount_amount' => $discount * $ds->qty,
                'amount' => $amount,
                'ref_code' => $doc->ref_code,
                'input_type' => $input_type
              );

              $id = $this->consignment_order_model->add_detail($arr);

              if( ! $id)
              {
                $sc = FALSE;
                $this->error = "Failed to insert item";
              }
            }
            else
            {
              $id = $detail->id;
              $new_qty = $ds->qty + $detail->qty;

              $arr = array(
                'qty' => $new_qty,
                'discount_amount' => $discount * $new_qty,
                'amount' => $sell_price * $new_qty
              );

              if( ! $this->consignment_order_model->update_detail($id, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update item row";
              }
            }
          }

          if($sc === TRUE)
          {
            $this->consignment_order_model->recal_summary($doc->code);

            if($doc->status == 'A')
            {
              $arr = array(
                'status' => 'P',
                'update_user' => $this->_user->uname
              );

              $this->consignment_order_model->update($doc->code, $arr);
            }
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
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

    if($sc === TRUE)
    {
      $rs = $this->consignment_order_model->get_detail($id);

      if( ! empty($rs))
      {
        $res = array(
          'id' => $rs->id,
          'product_code' => $rs->product_code,
          'product_name' => $rs->product_name,
          'price' => number($rs->price,2),
          'qty' => $rs->qty,
          'discount' => $rs->discount,
          'amount' => $rs->amount
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "Insert success but cannot get new data update please refresh page to load new data";
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $res : NULL
    );

    echo json_encode($arr);
  }


  public function update_detail()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->id) && ! empty($ds->qty))
    {
      $doc = $this->consignment_order_model->get($ds->code);

      if( ! empty($doc))
      {
        if($doc->status == 'P' OR $doc->status == 'A')
        {
          $rs = $this->consignment_order_model->get_detail($ds->id);

          if( ! empty($rs))
          {
            $this->load->library('wrx_consignment_api');
            $input_type = 1;

            $stock = $rs->count_stock == 1 ? $this->wrx_consignment_api->get_onhand_stock($rs->product_code, $doc->warehouse_code) : 100000;
            $c_qty = $rs->count_stock == 1 ? $this->consignment_order_model->get_unsave_qty($doc->code, $rs->product_code, $ds->price, $ds->disc) : 0;
            $c_qty = $c_qty - $rs->qty;
            $sum_qty = $ds->qty + $c_qty;

            if($sum_qty > $stock)
            {
              $sc = FALSE;
              $this->error = "Insufficient stock";
            }

            if($sc === TRUE)
            {
              $disc = parse_discount_text($ds->disc, $ds->price);
              $discount = $disc['discount_amount'];
              $sell_price = $ds->price - $discount;
              $amount = $sell_price * $ds->qty;

              $arr = array(
                'price' => $ds->price,
                'sell_price' => $sell_price,
                'qty' => $ds->qty,
                'discount' => $ds->disc,
                'discount_amount' => $discount * $ds->qty,
                'amount' => $amount,
                'input_type' => $input_type
              );

              if( ! $this->consignment_order_model->update_detail($ds->id, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update row item";
              }
              else
              {
                $this->consignment_order_model->recal_summary($ds->code);

                if($doc->status == 'A')
                {
                  $arr = array(
                    'status' => 'P',
                    'update_user' => $this->_user->uname
                  );

                  $this->consignment_order_model->update($doc->code, $arr);
                }
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Row item not found";
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
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


  public function delete_detail()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $id = $this->input->post('id');

    if( ! empty($code) && ! empty($id))
    {
      $doc = $this->consignment_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'P' OR $doc->status == 'A')
        {
          if( ! $this->consignment_order_model->delete_detail($id))
          {
            $sc = FALSE;
            $this->error = "Failed to delete row item";
          }
          else
          {
            $this->consignment_order_model->recal_summary($code);

            if($doc->status == 'A')
            {
              $arr = array(
                'status' => 'P',
                'update_user' => $this->_user->uname
              );

              $this->consignment_order_model->update($doc->code, $arr);
            }
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
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


  public function get_consignment_warehouse_by_customer()
  {
    $sc = TRUE;
    $ds = [];
    $customer_code = $this->input->post('customer_code');
    $warehouse_code = $this->input->post('warehouse_code');

    $options = $this->warehouse_model->get_consignment_warehouse_by_customer($customer_code);

    if( ! empty($options))
    {
      $count = count($options);

      foreach($options as $rs)
      {
        $selected = $count == 1 ? 'selected' : is_selected($warehouse_code, $rs->code);
        $ds[] = ['code' => $rs->code, 'name' => $rs->name, 'selected' => $selected];
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function get_consignment_customer_by_warehouse()
  {
    $sc = TRUE;
    $ds = [];
    $warehouse_code = $this->input->post('warehouse_code');
    $customer_code = $this->input->post('customer_code');
    $is_consignment = 1;
    $exists = FALSE;

    if($sc === TRUE)
    {
      $exists = empty($customer_code) ? FALSE : $this->warehouse_model->is_exists_customer($warehouse_code, $customer_code);
      $customer_code = $exists ? $customer_code : NULL;

      $ds = $this->warehouse_model->get_warehouse_customer($warehouse_code, $customer_code, $is_consignment);

      if( ! empty($ds))
      {
        $customer = $this->customers_model->get($ds->customer_code);

        $ds->customer_name = empty($customer) ? $ds->customer_name : $customer->name;
        $ds->gp = empty($customer) ? 0 : get_zero($customer->gp);
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }

  //-- auto complete
  public function get_customer_by_warehouse($whsCode = NULL)
  {
    $txt = trim($_REQUEST['term']);
    $sc = [];

    if(empty($whsCode))
    {
      $this->db
      ->distinct()
      ->select('code, name, gp')
      ->where('active', 1);

      if($txt != '*')
      {
        $this->db
        ->group_start()
        ->like('code', $txt)
        ->or_like('name', $txt)
        ->group_end();
      }

      $rs = $this->db->limit(50)->get('customers');
    }
    else
    {
      $this->db
      ->distinct()
      ->select('c.code, c.name, c.gp')
      ->from('warehouse_customer AS w')
      ->join('customers AS c', 'w.customer_code = c.code', 'left')
      ->where('w.warehouse_code', $whsCode);

      if($txt != '*')
      {
        $this->db
        ->group_start()
        ->like('c.code', $txt)
        ->or_like('c.name', $txt)
        ->group_end();
      }

      $rs = $this->db->limit(50)->get();
    }

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rd)
      {
        $sc[] = $rd->code.' | '.$rd->name.' | '.$rd->gp;
      }
    }

    echo json_encode($sc);
  }


  public function save()
  {
    $sc = TRUE;
    $this->load->library('wrx_consignment_api');
    $code = $this->input->post('code');
    $doc = $this->consignment_order_model->get($code);

    if($doc->status == 'P' OR $doc->status == 'A')
    {
      $details = $this->consignment_order_model->get_details($code);

      if( ! empty($details))
      {
        foreach($details as $row)
        {
          if(empty($items[$row->product_code]))
          {
            $items[$row->product_code] = (object) array(
              'code' => $row->product_code,
              'qty' => $row->qty,
              'stock' => $row->count_stock == 1 ? $this->wrx_consignment_api->get_onhand_stock($row->product_code, $doc->warehouse_code) : 1000000
            );
          }
          else
          {
            $items[$row->product_code]->qty += $row->qty;
          }
        }

        foreach($items as $item)
        {
          if($item->qty > $item->stock)
          {
            $sc = FALSE;
            $this->error .= "<span class='display-block'>Insufficient stock for {$item->code} ({$item->qty} / {$item->stock})</span>";
          }
        }

        $this->db->trans_begin();

        if($sc === TRUE)
        {
          $arr = array(
            'status' => 'A',
            'update_user' => $this->_user->uname
          );

          if( ! $this->consignment_order_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update document status";
          }
        }

        if($sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการสินค้า";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('status');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function approve()
  {
    $sc = TRUE;
    $ex = 0;
    $code = $this->input->post('code');

    if($this->pm->can_approve)
    {
      if( ! empty($code))
      {
        $doc = $this->consignment_order_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status == 'A')
          {
            $this->load->library('wrx_consignment_api');
            $details = $this->consignment_order_model->get_details($code);

            if( ! empty($details))
            {
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                $stock = $rs->count_stock == 1 ? $this->wrx_consignment_api->get_onhand_stock($rs->product_code, $doc->warehouse_code) : 1000000;
                $all_qty = $this->consignment_order_model->get_sum_order_qty($doc->code, $rs->product_code);

                if($all_qty > $stock)
                {
                  $sc = FALSE;
                  $this->error .= "<span>{$rs->product_code} ยอดในโซนไม่พอตัด  ในโซน: {$stock} ยอดตัด : {$all_qty} </span><br/>";
                }
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              $arr = array(
                'status' => 'C',
                'update_user' => $this->_user->uname
              );

              if( ! $this->consignment_order_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update document status";
              }

              if($sc === TRUE)
              {
                $arr = array('status' => 'C');

                if( ! $this->consignment_order_model->update_details($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update item rows status";
                }
              }

              if($sc === TRUE)
              {
                $this->load->model('approve_logs_model');
                $this->approve_logs_model->add($code, 1, $this->_user->uname);
              }

              if($sc === TRUE)
              {
                $this->db->trans_commit();
              }
              else
              {
                $this->db->trans_rollback();
              }

              if($sc === TRUE)
              {
                if(is_true(getConfig('WRX_API')) && is_true(getConfig('WRX_CONSIGNMENT_INTERFACE')))
                {
                  // INT12
                  if( ! $this->wrx_consignment_api->export_consignment($code))
                  {
                    $ex = 1;
                    $this->error = "อนุมัติเอกสารสำเร็จ แต่ส่งข้อมูลไป ERP ไม่สำเร็จ <br/> กรุณากดส่งข้อมูลไป ERP ใหม่อีกครั้งภายหลัง";
                  }
                }
              }
            }
          }
          else
          {
            $sc = FALSE;
            set_error('status');
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
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  public function do_export()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->consignment_order_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 'C')
        {
          if(is_true(getConfig('WRX_API')) && is_true(getConfig('WRX_CONSIGNMENT_INTERFACE')))
          {
            $this->load->library('wrx_consignment_api');

            if( ! $this->wrx_consignment_api->export_consignment($code))
            {
              $sc = FALSE;
              $this->error = "Send data to ERP failed : {$this->wrx_consignment_api->error}";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Service unavaliable <br/> Consign Interface is inactive";
          }
        }
        else
        {
          $sc = FALSE;
          set_error('status');
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


  public function cancel()
  {
    $sc = TRUE;
    $code = $this->input->post('code');
    $reason = trim($this->input->post('reason'));

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->consignment_order_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 'D' && $doc->is_exported != 'Y')
          {
            $this->db->trans_begin();

            $arr = array(
              'status' => 'D',
              'update_user' => $this->_user->uname,
              'cancel_user' => $this->_user->uname,
              'cancel_reason' => $reason
            );

            if( ! $this->consignment_order_model->update($code, $arr))
            {
              $sc = FALSE;
              set_error('cancel');
            }

            if($sc === TRUE)
            {
              if( ! $this->consignment_order_model->update_details($code, ['status' => 'D']))
              {
                $sc = FALSE;
                $this->error = "Failed to cancel item rows status";
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
          else
          {
            $sc = FALSE;
            set_error('status');
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
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function rollback()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if($this->pm->can_delete)
    {
      if( ! empty($code))
      {
        $doc = $this->consignment_order_model->get($code);

        if( ! empty($doc))
        {
          if($doc->status != 'P' && $doc->is_exported != 'Y')
          {
            $this->db->trans_begin();

            $arr = array(
              'status' => 'P',
              'update_user' => $this->_user->uname
            );

            if( ! $this->consignment_order_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "Failed to rollback document status";
            }

            if($sc === TRUE)
            {
              if( ! $this->consignment_order_model->update_details($code, ['status' => 'O']))
              {
                $sc = FALSE;
                $this->error = "Failed to rollback item rows status";
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
          else
          {
            $sc = FALSE;
            set_error('status');
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
    }
    else
    {
      $sc = FALSE;
      set_error('permission');
    }

    $this->_response($sc);
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_CONSIGNMENT_SOLD');
    $run_digit = getConfig('RUN_DIGIT_CONSIGNMENT_SOLD');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->consignment_order_model->get_max_code($pre);
    if( ! empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function clear_filter()
  {
    $filter = array(
      'consign_code',
      'consign_customer',
      'consign_warehouse',
      'consign_user',
      'consign_from_date',
      'consign_to_date',
      'consign_status',
      'consign_is_exported'
    );

    return clear_filter($filter);
  }


} //---- end class
 ?>
