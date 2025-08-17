<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adjust_transform extends PS_Controller
{
  public $menu_code = 'ICTFAJ';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = '';
	public $title = 'ตัดยอดแปรสภาพ';
  public $filter;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/adjust_transform';
    $this->load->model('inventory/adjust_transform_model');
    $this->load->model('inventory/transform_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('inventory/invoice_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'tf_code', ''),
      'reference' => get_filter('reference', 'tf_reference', ''),
      'user' => get_filter('user', 'tf_user', 'all'),
      'from_date' => get_filter('from_date', 'tf_from_date', ''),
      'to_date' => get_filter('to_date', 'tf_to_date', ''),
      'remark' => get_filter('remark', 'tf_remark', ''),
      'status' => get_filter('status', 'tf_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		$segment  = 4; //-- url segment
		$rows     = $this->adjust_transform_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list   = $this->adjust_transform_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('inventory/adjust_transform/adjust_transform_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('inventory/adjust_transform/adjust_transform_add');
  }


  public function add()
  {
    $sc = TRUE;
    if($this->input->post('date_add') && $this->input->post('zone_code'))
    {
      if($this->pm->can_add)
      {
        $date_add = db_date($this->input->post('date_add'), TRUE);
        $zone = $this->zone_model->get($this->input->post('zone_code'));
        if( ! empty($zone))
        {
          $code = empty($this->input->post('code')) ? $this->get_new_code($date_add) : $this->input->post('code');

          $ds = array(
            'code' => $code,
            'bookcode' => getConfig('BOOK_CODE_ADJUST_TRANSFORM'),
            'reference' => NULL,
            'from_warehouse' => $zone->warehouse_code,
            'from_zone' => $zone->code,
            'date_add' => $date_add,
            'user' => get_cookie('uname'),
            'remark' => get_null(trim($this->input->post('remark')))
          );

          if(! $this->adjust_transform_model->add($ds))
          {
            $sc = FALSE;
            $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "รหัสโซนไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "คุณไม่มีสิทธิ์ในการเพิ่มเอกสารใหม่";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "No form data";
    }

    echo $sc === TRUE ? "success|{$code}" : $this->error;
  }


  public function edit($code)
  {
    $doc = $this->adjust_transform_model->get($code);
    if( ! empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->from_zone);
      $ds = array(
        'doc' => $doc,
        'details' => $this->adjust_transform_model->get_details($code)
      );

      $this->load->view('inventory/adjust_transform/adjust_transform_edit', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }
  }


  public function is_exists($code)
  {
    if($this->adjust_transform_model->is_exists_code($code))
    {
      echo "เลขที่เอกสารซ้ำ";
    }
    else
    {
      echo 'not_exists';
    }
  }


  //---- update doc header
  public function update()
  {
    $sc = TRUE;
    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $zone = $this->zone_model->get($this->input->post('zone_code'));
      $remark = get_null($this->input->post('remark'));

      if( ! empty($zone))
      {
        $doc = $this->adjust_transform_model->get($code);
        if( ! empty($doc))
        {
          $arr = array(
            'date_add' => $date_add,
            'from_warehouse' => $zone->warehouse_code,
            'from_zone' => $zone->code,
            'remark' => $remark
          );

          if(! $this->adjust_transform_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เลขที่เอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "โซนไม่ถูกต้อง";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  //---- save
  public function save()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $doc = $this->adjust_transform_model->get($code);
      if( ! empty($doc))
      {
        if($doc->status == 0)
        {
          $transform_code = $this->input->post('transform_code');

          if( ! empty($transform_code))
          {
            //--- get WQ
            $wq = $this->transform_model->get($transform_code);

            if( ! empty($doc))
            {
              //--- check WQ must be closed
              if($wq->is_closed == 1 OR $wq->is_closed == 0)
              {
                if(empty($wq->reference) OR !empty($wq->reference))
                {
                  $items = json_decode($this->input->post('items'));

                  $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

                  if( ! empty($items))
                  {
                    //--- update adjust reference
                    $this->db->trans_begin();

                    //--- add detail
                    foreach($items as $item)
                    {
                      if($sc === FALSE) { break; }

											if($item->qty > 0)
											{
                        if($sc === TRUE)
                        {
                          $arr = array(
                            'adjust_code' => $code,
                            'product_code' => $item->product_code,
                            'qty' => $item->qty
                          );

                          if(! $this->adjust_transform_model->add_detail($arr))
                          {
                            $sc = FALSE;
                            $this->error = "เพิ่มรายการไม่สำเร็จ {$item->product_code} : {$item->qty}";
                          }
                        }

                        if($sc === TRUE)
                        {
                          $stock = $this->stock_model->get_stock_zone($doc->from_zone, $item->product_code);

                          if($stock < $item->qty)
                          {
                            $sc = FALSE;
                            $this->error = "สต็อกคงเหลือในโซนไม่เพียงพอ {$item->product_code}<br/>Qty: {$item->qty} / {$stock}";
                          }
                          else
                          {
                            if( ! $this->stock_model->update_stock_zone($doc->from_zone, $item->product_code, ($item->qty * -1)))
                            {
                              $sc = FALSE;
                              $this->error = "ตัดสต็อกในโซนไม่สำเร็จ {$item->product_code}<br/>Qty: {$item->qty}";
                            }
                          }
                        }

                        if($sc === TRUE)
                        {
                          $arr = array(
                            'reference' => $doc->code,
                            'warehouse_code' => $doc->from_warehouse,
                            'zone_code' => $doc->from_zone,
                            'product_code' => $item->product_code,
                            'move_in' => 0,
                            'move_out' => $item->qty,
                            'date_add' => $date_add
                          );

                          if( ! $this->movement_model->add($arr))
                          {
                            $sc = FALSE;
                            $this->error = "เพิ่ม movement ไม่สำเร็จ";
                          }
                        }
											}
                    }

                    if($sc === TRUE)
                    {
                      //--- update reference on transform order
                      if($sc === TRUE)
                      {
                        if(! $this->transform_model->update_reference($transform_code, $code))
                        {
                          $sc = FALSE;
                          $this->error = "Update Transform Reference ไม่สำเร็จ";
                        }
                      }

                      //--- update status to 1
                      if($sc === TRUE)
                      {
                        $arr = array(
                          'status' => 1,
                          'reference' => $transform_code
                        );

                        if(!$this->adjust_transform_model->update($code, $arr))
                        {
                          $sc = FALSE;
                          $this->error = "Update Document Status Failed";
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
                    $this->error = "ไม่พบรายการสินค้า";
                  }
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "{$transform_code} ถูกต้ดยอดไปแล้ว ({$transform->reference})";
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "{$transform_code} ยังรับเข้าไม่ครบหรือยังไม่ถูกปิด";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "ไม่พบเอกสารเบิกแปรสภาพ : {{$transform_code}}";
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกบันทึกไปแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    $this->_response($sc);
  }


  public function view_detail($code)
  {
    $doc = $this->adjust_transform_model->get($code);

    if( ! empty($doc))
    {
      $doc->user_name = $this->user_model->get_name($doc->user);
      $doc->zone_name = $this->zone_model->get_name($doc->from_zone);
      $ds = array(
        'doc' => $doc,
        'details' => $this->adjust_transform_model->get_details($code)
      );

      $this->load->view('inventory/adjust_transform/adjust_transform_detail', $ds);
    }
    else
    {
      $this->load->view('page_error');
    }
  }


  public function cancle($code)
  {
    $sc = TRUE;

    if( ! empty($code))
    {
      $doc = $this->adjust_transform_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status != 2)
        {
          $this->db->trans_begin();

          $details = $this->adjust_transform_model->get_details($code);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              if($sc === FALSE) { break; }

              if($rs->is_cancle == 0)
              {
                if( ! $this->stock_model->update_stock_zone($doc->from_zone, $rs->product_code, $rs->qty))
                {
                  $sc = FALSE;
                  $this->error = "เพิ่มสต็อกเข้าโซนไม่สำเร็จ";
                }
              }
            }
          }

          if($sc === TRUE)
          {
            //---- set is_cancle = 1 in adjust_detail
            if(! $this->adjust_transform_model->cancle_details($code))
            {
              $sc = FALSE;
              $this->error = "ยกเลิกรายการไม่สำเร็จ";
            }
          }

          if($sc === TRUE)
          {
            if( ! $this->movement_model->drop_movement($code))
            {
              $sc = FALSE;
              $this->error = "ลบ movement ไม่สำเร็จ";
            }
          }

          if($sc === TRUE)
          {
            $arr = array(              
              'status' => 2,
              'cancle_reason' => trim($this->input->post('reason')),
              'cancle_user' => $this->_user->uname
            );

            if(! $this->adjust_transform_model->update($code, $arr))
            {
              $sc = FALSE;
              $this->error = "ยกเลิกเอกสารไม่สำเร็จ";
            }
          }

          //--- remove transform reference
          if($sc === TRUE)
          {
            $this->transform_model->update_reference($doc->reference, NULL);
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


//--- auto complete WQ Number
  public function get_closed_transform_order()
  {
    $code = trim($_REQUEST['term']);
    $limit = 20;

    echo $this->transform_model->get_closed_transform_order($code, $limit);
  }



  public function get_billed_details()
  {
    $sc = TRUE;
    $ds = array();
    $code = $this->input->get('code'); //-- WG
    $transform_code = $this->input->get('reference'); //--- WQ
    if( ! empty($code) && !empty($transform_code))
    {
      $doc = $this->adjust_transform_model->get($code);
      if( ! empty($doc))
      {
        $this->load->model('inventory/invoice_model');
        $details = $this->invoice_model->get_billed_detail_qty($transform_code);
        if( ! empty($details))
        {

          $no = 1;
					$total_issue = 0;
          $total_qty = 0;
          $total_in_zone = 0;
					$total_bill_qty = 0;
          foreach($details as $rs)
          {
						$issue_qty = $this->adjust_transform_model->get_sum_issued_qty($transform_code, $rs->product_code); //---จำนวนที่เคยตัดไปแล้วด้วย WQ นี้
            $in_zone = $this->stock_model->get_stock_zone($doc->from_zone, $rs->product_code);
						$qty = $rs->qty - $issue_qty;
						$qty = $qty > 0 ? $qty : 0;
						$qty = $in_zone < $qty ? $in_zone : $qty;

            $arr = array(
              'no' => $no,
              'pdCode' => $rs->product_code,
              'pdName' => $rs->product_name,
              'bill_qty' => number(round($rs->qty,2)),
							'issued_qty' => number(round($issue_qty, 2)),
							'qty' => round($qty,2),
              'in_zone_qty' => number(round($in_zone,2)),
              'hilight' => ($in_zone < $qty) ? 'red' : ''
            );

            $no++;
						$total_issue += $issue_qty;
            $total_qty += $qty;
            $total_in_zone += $in_zone;
						$total_bill_qty += $rs->qty;

            array_push($ds, $arr);
          }

          $arr = array(
						'total_bill_qty' => $total_bill_qty,
						'total_issue_qty' => $total_issue,
            'total_qty' => $total_qty,
            'total_in_zone' => $total_in_zone
          );

          array_push($ds, $arr);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบตัวแปรที่ร้องขอ";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }



  public function get_new_code($date = '')
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ADJUST_TRANSFORM');
    $run_digit = getConfig('RUN_DIGIT_ADJUST_TRANSFORM');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->adjust_transform_model->get_max_code($pre);
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
      'tf_code',
      'tf_reference',
      'tf_user',
      'tf_from_date',
      'tf_to_date',
      'tf_remark',
      'tf_status'
    );

    clear_filter($filter);

    echo 'done';
  }

} //---- End class
?>
