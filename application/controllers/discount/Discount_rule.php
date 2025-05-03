<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Discount_rule extends PS_Controller
{
  public $menu_code = 'SCRULE';
	public $menu_group_code = 'SC';
	public $title = 'เพิ่ม/แก้ไข เงือนไขส่วนลด';
  public $error;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'discount/discount_rule';
    $this->load->model('discount/discount_policy_model');
    $this->load->model('discount/discount_rule_model');
  }


  public function index()
  {
    $this->load->helper('discount_policy');
    $this->load->helper('discount_rule');

    $filter = array(
      'code' => get_filter('code', 'rule_code', ''),
      'name' => get_filter('name', 'rule_name', ''),
      'active' => get_filter('active', 'rule_active', 'all'),
      'type' => get_filter('type', 'rule_type', 'all'),
      'policy' => get_filter('policy', 'rule_policy', ''),
      'priority' => get_filter('priority', 'rule_priority', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$rows = $this->discount_rule_model->count_rows($filter);

		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);

    $filter['data'] = $this->discount_rule_model->get_list($filter, $perpage, $this->uri->segment($this->segment));

		$this->pagination->initialize($init);

    $this->load->view('discount/rule/rule_list', $filter);
  }


  public function add_new()
  {
    if($this->pm->can_add)
    {
      $this->load->view('discount/rule/rule_add');
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
      if($this->input->post('name'))
      {
        $code = $this->get_new_code();
        $name = $this->input->post('name');

        $arr = array(
          'code' => $code,
          'name' => $name,
          'user' => get_cookie('uname')
        );

        $id = $this->discount_rule_model->add($arr);

        if( ! $id)
        {
          $sc = FALSE;
          set_error('insert');
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
      'id' => $sc === TRUE ? $id : NULL
    );

    echo json_encode($arr);
  }


  public function edit($id, $tab = "discount")
  {
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customer_group_model');
    $this->load->model('masters/customer_type_model');
    $this->load->model('masters/customer_kind_model');
    $this->load->model('masters/customer_area_model');
    $this->load->model('masters/customer_class_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/product_group_model');
    $this->load->model('masters/product_sub_group_model');
    $this->load->model('masters/product_kind_model');
    $this->load->model('masters/product_type_model');
    $this->load->model('masters/product_category_model');
    $this->load->model('masters/product_brand_model');

    $data = array(
      "rule" => $this->discount_rule_model->get($id),
      "channels" => $this->discount_rule_model->getRuleChannels($id),
      "channels_list" => $this->channels_model->get_all(),
      "payments" => $this->discount_rule_model->getRulePayment($id),
      "payment_list" => $this->payment_methods_model->get_all(),
      "cusList" => $this->discount_rule_model->getRuleCustomerId($id),
      "custGroup" => $this->discount_rule_model->getRuleCustomerGroup($id),
      "custType" => $this->discount_rule_model->getRuleCustomerType($id),
      "custKind" => $this->discount_rule_model->getRuleCustomerKind($id),
      "custArea" => $this->discount_rule_model->getRuleCustomerArea($id),
      "custGrade" => $this->discount_rule_model->getRuleCustomerClass($id),
      "customer_groups" => $this->customer_group_model->get_all(),
      "customer_types" => $this->customer_type_model->get_all(),
      "customer_kinds" => $this->customer_kind_model->get_all(),
      "customer_areas" => $this->customer_area_model->get_all(),
      "customer_grades" => $this->customer_class_model->get_all(),
      "pdList" => $this->discount_rule_model->getRuleProduct($id),
      "pdModel" => $this->discount_rule_model->getRuleProductStyle($id),
      "pdGroup" => $this->discount_rule_model->getRuleProductGroup($id),
      "pdSubGroup" => $this->discount_rule_model->getRuleProductSubGroup($id),
      "pdKind" => $this->discount_rule_model->getRuleProductKind($id),
      "pdType" => $this->discount_rule_model->getRuleProductType($id),
      "pdCategory" => $this->discount_rule_model->getRuleProductCategory($id),
      "pdBrand" => $this->discount_rule_model->getRuleProductBrand($id),
      "pdYear" => $this->discount_rule_model->getRuleProductYear($id),
      "product_groups" => $this->product_group_model->get_all(),
      "product_sub_groups" => $this->product_sub_group_model->get_all(),
      "product_categorys" => $this->product_category_model->get_all(),
      "product_kinds" => $this->product_kind_model->get_all(),
      "product_types" => $this->product_type_model->get_all(),
      "product_brands" => $this->product_brand_model->get_all(),
      "product_years" => $this->products_model->get_all_year(),
      "free_items" => $this->discount_rule_model->getRuleFreeProduct($id),
      "tab" => $tab
    );


    $this->load->view('discount/rule/rule_edit', $data);
  }


  public function update_rule($id)
  {
    $sc = TRUE;

    $arr = array(
      'name' => $this->input->post('name'),
      'active' => $this->input->post('active')
    );

    if( ! $this->discount_rule_model->update($id, $arr))
    {
      $sc = FALSE;
      $this->error = "Failed to update data";
    }

    $this->_response($sc);
  }


  public function set_discount()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->discType))
    {
      $id = $ds->id;

      $this->db->trans_begin();

      if( ! $this->discount_rule_model->drop_free_product($id))
      {
        $sc = FALSE;
        $this->error = "Failed to delete previous free items";
      }

      if($sc === TRUE)
      {
        if($ds->discType == 'F')
        {
          if( ! empty($ds->freeItemList))
          {
            foreach($ds->freeItemList as $rs)
            {
              $arr = array(
                'id_rule' => $id,
                'product_id' => $rs->id,
                'product_code' => $rs->code
              );

              if( ! $this->discount_rule_model->add_free_product($arr))
              {
                $sc = FALSE;
                $this->error = "Failed  to add new free product";
                break;
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Free items not found !";
          }
        }
      }

      if($sc === TRUE)
      {
        $arr = array(
  				"minQty" => $ds->minQty,
  				"minAmount" => $ds->minAmount,
  				"canGroup" => $ds->canGroup,
          "canRepeat" => $ds->canRepeat,
  				"type" => $ds->discType,
  				"price" => $ds->discType == 'N' ? $ds->price : 0.00,
  				"freeQty" => $ds->freeQty,
  				"disc1" => $ds->discType == 'D' ? $ds->disc1 : 0.00,
  				"disc2" => $ds->discType == 'D' ? $ds->disc2 : 0.00,
  				"disc3" => $ds->discType == 'D' ? $ds->disc3 : 0.00,
  				"priority" => $ds->priority,
  				"update_user" => $this->_user->uname
  			);

        if( ! $this->discount_rule_model->update($id, $arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update discount setting";
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
      set_error('required');
    }

    $this->_response($sc);
  }


  public function set_customer_rule()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->all))
    {
      $this->db->trans_begin();
      $id = $ds->id;

      if( ! $this->reset_customer_attribute($id))
      {
        $sc = FALSE;
        $this->error = "Failed to delete previous setting";
      }

      if($sc === TRUE && $ds->all == 'Y')
      {
        if( ! $this->discount_rule_model->update($id, ['all_customer' => 1]))
        {
          $sc = FALSE;
          $this->error = "Failed to update all customer setting";
        }
      }
      else
      {
        if( ! $this->discount_rule_model->update($id, ['all_customer' => 0]))
        {
          $sc = FALSE;
          $this->error = "Failed to update all customer setting";
        }

        if($sc === TRUE && $ds->customer == 'Y')
        {
          if( ! empty($ds->customerList))
          {
            foreach($ds->customerList as $rs)
            {
              $arr = array(
                'id_rule' => $id,
                'customer_id' => $rs->id,
                'customer_code' => $rs->code
              );

              if( ! $this->discount_rule_model->add_customer($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to add new customer list";
                break;
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Customer list not found";
          }
        }
        else
        {
          if($sc === TRUE && $ds->group == 'Y' && ! empty($ds->groupList))
          {
            foreach($ds->groupList as $code)
            {
              $arr = array(
                'id_rule' => $id,
                'group_code' => $code
              );

              if( ! $this->discount_rule_model->add_customer_group($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to ad new customer group";
                break;
              }
            }
          }

          if($sc === TRUE && $ds->kind == 'Y' && ! empty($ds->kindList))
          {
            foreach($ds->kindList as $code)
            {
              $arr = array(
                'id_rule' => $id,
                'kind_code' => $code
              );

              if( ! $this->discount_rule_model->add_customer_kind($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to ad new customer kind";
                break;
              }
            }
          }

          if($sc === TRUE && $ds->type == 'Y' && ! empty($ds->typeList))
          {
            foreach($ds->typeList as $code)
            {
              $arr = array(
                'id_rule' => $id,
                'type_code' => $code
              );

              if( ! $this->discount_rule_model->add_customer_type($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to ad new customer type";
                break;
              }
            }
          }

          if($sc === TRUE && $ds->area == 'Y' && ! empty($ds->areaList))
          {
            foreach($ds->areaList as $code)
            {
              $arr = array(
                'id_rule' => $id,
                'area_code' => $code
              );

              if( ! $this->discount_rule_model->add_customer_area($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to ad new customer area";
                break;
              }
            }
          }

          if($sc === TRUE && $ds->grade == 'Y' && ! empty($ds->gradeList))
          {
            foreach($ds->gradeList as $code)
            {
              $arr = array(
                'id_rule' => $id,
                'class_code' => $code
              );

              if( ! $this->discount_rule_model->add_customer_class($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to ad new customer grade";
                break;
              }
            }
          }
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
      set_error('required');
    }

    $this->_response($sc);
  }


  public function set_product_rule()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->id))
    {
      $id = $ds->id;

      $this->db->trans_begin();

      if( ! $this->reset_product_attribute($id))
      {
        $sc = FALSE;
      }

      if($sc === TRUE)
      {
        if($ds->all == 'Y')
        {
          if( ! $this->discount_rule_model->update($id, ['all_product' => 1]))
          {
            $sc = FALSE;
            $this->error = "Failed to update discount rule product";
          }
        }
        else
        {
          if( ! $this->discount_rule_model->update($id, ['all_product' => 0]))
          {
            $sc = FALSE;
            $this->error = "Failed to update discount rule product";
          }

          if($sc === TRUE)
          {
            if($ds->sku == 'Y')
            {
              if( ! empty($ds->skuList))
              {
                foreach($ds->skuList as $rs)
                {
                  if($sc === FALSE) { break; }

                  $arr = array(
                    'id_rule' => $id,
                    'product_id' => $rs->id,
                    'product_code' => $rs->code
                  );

                  if( ! $this->discount_rule_model->add_sku($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert sku setting";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "SKU List Not Found !";
              }
            }
            else if($ds->model == 'Y')
            {
              if( ! empty($ds->modelList))
              {
                foreach($ds->modelList as $rs)
                {
                  if($sc === FALSE) { break; }

                  $arr = array(
                    'id_rule' => $id,
                    'style_id' => $rs->id,
                    'style_code' => $rs->code
                  );

                  if( ! $this->discount_rule_model->add_style($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert Model setting";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "Model List Not Found !";
              }
            }
            else
            {
              if($ds->group == 'Y' && ! empty($ds->groupList))
              {
                foreach($ds->groupList as $code)
                {
                  if($sc === FALSE) { break; }

                  $arr = array(
                    'id_rule' => $id,
                    'group_code' => $code
                  );

                  if( ! $this->discount_rule_model->add_product_group($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert product group setting";
                  }
                }
              }

              if($ds->sub_group == 'Y' && ! empty($ds->subGroupList))
              {
                foreach($ds->subGroupList as $code)
                {
                  if($sc === FALSE) { break; }

                  $arr = array(
                    'id_rule' => $id,
                    'sub_group_code' => $code
                  );

                  if( ! $this->discount_rule_model->add_product_sub_group($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert product sub group setting";
                  }
                }
              }

              if($ds->kind == 'Y' && ! empty($ds->kindList))
              {
                foreach($ds->kindList as $code)
                {
                  if($sc === FALSE) { break; }

                  $arr = array(
                    'id_rule' => $id,
                    'kind_code' => $code
                  );

                  if( ! $this->discount_rule_model->add_product_kind($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert product kind setting";
                  }
                }
              }

              if($ds->type == 'Y' && ! empty($ds->typeList))
              {
                foreach($ds->typeList as $code)
                {
                  if($sc === FALSE) { break; }

                  $arr = array(
                    'id_rule' => $id,
                    'type_code' => $code
                  );

                  if( ! $this->discount_rule_model->add_product_type($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert product type setting";
                  }
                }
              }

              if($ds->category == 'Y' && ! empty($ds->categoryList))
              {
                foreach($ds->categoryList as $code)
                {
                  if($sc === FALSE) { break; }

                  $arr = array(
                    'id_rule' => $id,
                    'category_code' => $code
                  );

                  if( ! $this->discount_rule_model->add_product_category($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert product category setting";
                  }
                }
              }

              if($ds->brand == 'Y' && ! empty($ds->brandList))
              {
                foreach($ds->brandList as $code)
                {
                  if($sc === FALSE) { break; }

                  $arr = array(
                    'id_rule' => $id,
                    'brand_code' => $code
                  );

                  if( ! $this->discount_rule_model->add_product_brand($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert product brand setting";
                  }
                }
              }

              if($ds->year == 'Y' && ! empty($ds->yearList))
              {
                foreach($ds->yearList as $year)
                {
                  if($sc === FALSE) { break; }

                  $arr = array(
                    'id_rule' => $id,
                    'year' => $year
                  );

                  if( ! $this->discount_rule_model->add_product_year($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert product year setting";
                  }
                }
              }
            }
          }
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
      set_error('required');
    }

    $this->_response($sc);
  }


  public function set_channels_rule()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->all))
    {
      $id = $ds->id;

      $this->db->trans_begin();

      if( ! $this->discount_rule_model->drop_channels($id))
      {
        $sc = FALSE;
        $this->error = "Failed to reset channels setting";
      }

      if($sc === TRUE && $ds->all == 'Y')
      {
        if( ! $this->discount_rule_model->update($id, ['all_channels' => 1]))
        {
          $sc = FALSE;
          $this->error = "Failed to update channels setting";
        }
      }

      if($sc === TRUE && $ds->all == 'N')
      {
        if( ! $this->discount_rule_model->update($id, ['all_channels' => 0]))
        {
          $sc = FALSE;
          $this->error = "Failed to update channels setting";
        }

        if($sc === TRUE && ! empty($ds->channelsList))
        {
          foreach($ds->channelsList as $code)
          {
            $arr = array(
              'id_rule' => $id,
              'channels_code' => $code
            );

            if( ! $this->discount_rule_model->add_channels($arr))
            {
              $sc = FALSE;
              $this->error = "Faiiled to add new channels setting";
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Channels List not found !";
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
      set_error('required');
    }

    $this->_response($sc);
  }


  public function set_payment_rule()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->id) && ! empty($ds->all))
    {
      $id = $ds->id;

      $this->db->trans_begin();

      if( ! $this->discount_rule_model->drop_payment($id))
      {
        $sc = FALSE;
        $this->error = "Failed to reset payments setting";
      }

      if($sc === TRUE && $ds->all == 'Y')
      {
        if( ! $this->discount_rule_model->update($id, ['all_payment' => 1]))
        {
          $sc = FALSE;
          $this->error = "Failed to update payments setting";
        }
      }

      if($sc === TRUE && $ds->all == 'N')
      {
        if( ! $this->discount_rule_model->update($id, ['all_payment' => 0]))
        {
          $sc = FALSE;
          $this->error = "Failed to update payments setting";
        }

        if($sc === TRUE && ! empty($ds->paymentList))
        {
          foreach($ds->paymentList as $code)
          {
            $arr = array(
              'id_rule' => $id,
              'payment_code' => $code
            );

            if( ! $this->discount_rule_model->add_payment($arr))
            {
              $sc = FALSE;
              $this->error = "Faiiled to add new payments setting";
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Payments List not found !";
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
      set_error('required');
    }

    $this->_response($sc);

  }




  public function add_policy_rule()
  {
    $sc = TRUE;

    $id_policy = $this->input->post('id_policy');
  	$rule = $this->input->post('rule');

  	if(!empty($rule))
  	{
  		foreach($rule as $id_rule)
  		{
  			if($this->discount_rule_model->update_policy($id_rule, $id_policy) === FALSE)
  			{
  				$sc = FALSE;
  				$message = 'เพิ่มกฏไม่สำเร็จ';
  			}
  		}	//--- end foreach
  	}	//--- end if empty

  	echo $sc === TRUE ? 'success' : $message;
  }



  public function unlink_rule()
  {
    $sc = TRUE;
    $id_rule = $this->input->post('id_rule');
    if($this->discount_rule_model->update_policy($id_rule, NULL) === FALSE)
    {
      $sc = FALSE;
      $message = 'ลบกฏไม่สำเร็จ';
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function delete_rule()
  {
    $sc = TRUE;
    //--- check before delete
    $id = $this->input->post('id_rule');
    $rule = $this->discount_rule_model->get($id);
    if(!empty($rule))
    {
      if(!empty($rule->id_policy))
      {
        $policy_code = $this->discount_policy_model->get_code($rule->id_policy);
        $sc = FALSE;
        $this->error = "มีการเชื่อมโยงเงื่อนไขไว้กับนโยบายเลขที่ : {$policy_code} กรุณาลบการเชื่อมโยงก่อนลบเงื่อนไขนี้";
      }
      else
      {
        if(! $this->discount_rule_model->delete_rule($id))
        {
          $sc = FALSE;
          $this->error = "ลบรายการไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Not found";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function view_rule_detail($id)
  {
    $this->load->library('printer');
    $rule = $this->discount_rule_model->get($id);
    $policy = $this->discount_policy_model->get($rule->id_policy);
    $ds['id_rule'] = $id;
    $ds['rule'] = $rule;
    $ds['policy'] = $policy;
    $this->load->view('discount/policy/view_rule_detail', $ds);
  }


  private function reset_customer_attribute($id)
  {
    $sc = TRUE;

    if($sc === TRUE && ! $this->discount_rule_model->drop_customer($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous customer list setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_customer_group($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous customer group setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_customer_kind($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous customer kind setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_customer_type($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous customer type setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_customer_area($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous customer area setting";
    }


    if($sc === TRUE && ! $this->discount_rule_model->drop_customer_class($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous customer grade setting";
    }

    return $sc;
  }


  private function reset_product_attribute($id)
  {
    $sc = TRUE;

    if($sc === TRUE && ! $this->discount_rule_model->drop_sku($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous SKU setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_model($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous Model setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_product_group($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous product group setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_product_sub_group($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous product sub group setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_product_kind($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous product kind setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_product_type($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous product type setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_product_category($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous product category setting";
    }

    if($sc === TRUE &&! $this->discount_rule_model->drop_product_brand($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous product brand setting";
    }

    if($sc === TRUE && ! $this->discount_rule_model->drop_product_year($id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete previous product year setting";
    }

    return $sc;
  }


  public function get_new_code()
  {
    $date = date('Y-m-d');
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RULE');
    $run_digit = getConfig('RUN_DIGIT_RULE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->discount_rule_model->get_max_code($pre);
    if(! is_null($code))
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
      'rule_code',
      'rule_name',
      'rule_active',
      'rule_type',
      'rule_policy',
      'rule_priority'
    );

    return clear_filter($filter);
  }
} //--- end class
?>
