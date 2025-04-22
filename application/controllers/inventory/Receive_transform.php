<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_transform extends PS_Controller
{
  public $menu_code = 'ICTRRC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title = 'รับสินค้าจากการแปรสภาพ';
  public $filter;
  public $error;
  public $required_remark = TRUE; //--- บังคับใส่หมายเหตุ

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_transform';
    $this->load->model('inventory/receive_transform_model');
    $this->load->model('inventory/transform_model');
  }


  public function index()
  {
    $this->load->helper('channels');
    $this->load->helper('warehouse');

    $filter = array(
      'code'    => get_filter('code', 'trans_code', ''),
      'invoice' => get_filter('invoice', 'trans_invoice', ''),
      'order_code' => get_filter('order_code', 'trans_order_code', ''),
      'from_date' => get_filter('from_date', 'trans_from_date', ''),
      'to_date' => get_filter('to_date', 'trans_to_date', ''),
      'must_accept' => get_filter('must_accept', 'trans_must_accept', 'all'),
      'status' => get_filter('status', 'trans_status', 'all'),
      'warehouse' => get_filter('warehouse', 'trans_warehouse', 'all'),
      'zone' => get_filter('zone', 'trans_zone', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->receive_transform_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->receive_transform_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if( ! empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->receive_transform_model->get_sum_qty($rs->code);
      }
    }

    $filter['document'] = $document;

		$this->pagination->initialize($init);
    $this->load->view('inventory/receive_transform/receive_transform_list', $filter);
  }


  public function view_detail($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');

    $doc = $this->receive_transform_model->get($code);

    if( ! empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    }

    $details = $this->receive_transform_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('inventory/receive_transform/receive_transform_detail', $ds);
  }


  public function rollback_status()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->receive_transform_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 4)
        {
          $arr = array(
            'status' => 0
          );

          if( ! $this->receive_transform_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "สถานะเอกสารไม่ถูกต้อง";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function print_detail($code)
  {
    $this->load->library('printer');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/orders_model');

    $doc = $this->receive_transform_model->get($code);
    //$order = $this->orders_model->get($doc->order_code);
    if( ! empty($doc))
    {
      $zone = $this->zone_model->get($doc->zone_code);
      $doc->zone_name = $zone->name;
      $doc->warehouse_name = $zone->warehouse_name;
      //$doc->requester = $this->user_model->get_name($order->user);
      $doc->user_name = $this->user_model->get_name($doc->user);
    }

    $details = $this->receive_transform_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_received_transform', $ds);
  }


  public function save()
  {
    $sc = TRUE;
    $ex = 0;
    $data = json_decode($this->input->post('data'));

    if(! empty($data))
    {
      $this->load->model('masters/products_model');
      $this->load->model('masters/zone_model');
			$this->load->model('masters/warehouse_model');
			$this->load->model('inventory/movement_model');
      $this->load->model('stock/stock_model');

      $code = $data->receive_code;
      $doc = $this->receive_transform_model->get($code);

			if( ! empty($doc))
			{
				$zone = $this->zone_model->get($data->zone_code);

				$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

        if($sc === TRUE)
        {
          $zone_code = $zone->code;
          $warehouse_code = $zone->warehouse_code;
  	      $receive = $data->items;

  	      $arr = array(
  	        'order_code' => $data->order_code,
  	        'invoice_code' => $data->invoice,
  	        'zone_code' => $zone_code,
  	        'warehouse_code' => $warehouse_code,
  	        'update_user' => $this->_user->uname,
            'must_accept' => 0,
            'status' => 1,
            'shipped_date' => $date_add,
            'is_accept' => 0,
            'accept_by' => NULL,
            'accept_on' => NULL
  	      );

  	      $this->db->trans_begin();

  	      if($this->receive_transform_model->update($code, $arr) === FALSE)
  	      {
  	        $sc = FALSE;
  	        $this->error = 'Update Document Failed';
  	      }

  	      //--- If update success
  	      if($sc === TRUE)
  	      {
  	        if( ! empty($receive))
  	        {
  	          //--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
  	          $this->receive_transform_model->drop_details($code);

  						$details = array();

  	          foreach($receive as $rs)
  	          {
  							if($sc === FALSE)
  							{
  								break;
  							}

  	            if($rs->qty > 0)
  	            {
  	              $pd = $this->products_model->get($rs->product_code);

  								if( ! empty($pd))
  								{
  									$cost = $rs->price == 0 ? $pd->cost : $rs->price;

  		              $ds = array(
  		                'receive_code' => $code,
  		                'style_code' => $pd->style_code,
  		                'product_code' => $pd->code,
  		                'product_name' => $pd->name,
  		                'price' => $cost,
  		                'qty' => $rs->qty,
                      'receive_qty' => $rs->qty,
  		                'amount' => $rs->qty * $cost
  		              );

  		              if($this->receive_transform_model->add_detail($ds) === FALSE)
  		              {
  		                $sc = FALSE;
  		                $this->error = 'Add Receive Row Fail';
  		                break;
  		              }

                    if($sc === TRUE)
                    {
                      if( ! $this->stock_model->update_stock_zone($zone_code, $rs->product_code, $rs->qty))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to update stock";
                      }
                    }

                    if($sc === TRUE)
                    {
                      $ds = array(
                        'reference' => $code,
                        'warehouse_code' => $warehouse_code,
                        'zone_code' => $zone_code,
                        'product_code' => $pd->code,
                        'move_in' => $rs->qty,
                        'date_add' => db_date($date_add, TRUE)
                      );

                      if( ! $this->movement_model->add($ds))
                      {
                        $sc = FALSE;
                        $this->error = 'บันทึก movement ไม่สำเร็จ';
                      }

                      //--- update receive_qty in order_transform_detail
                      if($sc === TRUE)
                      {
                        $this->update_transform_receive_qty($data->order_code, $pd->code, $rs->qty);
                      }
                    }
  								}
  								else
  								{
  									$sc = FALSE;
  									$this->error = "ไม่พบรหัสสินค้า : {$rs->product_code}";
  								}

  	            }//--- end if qty > 0
  	          } //--- end foreach

              //--- หากไม่ต้องกดรับ
              if($sc === TRUE)
              {
                if($this->transform_model->is_complete($doc->order_code))
                {
                  $this->transform_model->close_transform($data->order_code);
                }
              }
  	        } //--- end if !empty($receive)
  	      } //--- if $sc === TRUE

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
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบข้อมูล';
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  //--- update receive_qty in order_transform_detail
  public function update_transform_receive_qty($order_code, $product_code, $qty)
  {
    $list = $this->transform_model->get_transform_product_by_code($order_code, $product_code);

    if( ! empty($list))
    {
      foreach($list as $rs)
      {
        if($qty > 0)
        {
          $diff = $rs->sold_qty - $rs->receive_qty;
          if($diff > 0 )
          {
            //--- ถ้า dif มากกว่ายอดที่รับมาให้ใช้ยอดรับ
            //--- หากยอดค้าง มี 2 แถว แถวแรก 5 แถวที่ 2 อีก 5 รวมเป็น 10
            //--- แต่รับเข้ามา 8
            //--- รอบแรก ยอด diff = 5 ซึ่งน้อยกว่า ยอดรับ ให้ใช้ยอด diff (ยอดค้างรับของแถวนั้น)
            //--- รอบสอง ยอด diff = 5 แต่ยอดรับจะเหลือ 3 เพราะถูกตัดออกไปรอบแรก 5 (จากยอดรับ 8)
            //--- รอบสองจึงต้องใช้ยอดรับที่เหลือในการ update
            $valid = $qty >= $diff ? TRUE : FALSE;
            $diff = $diff > $qty ? $qty : $diff;
            $this->transform_model->update_receive_qty($rs->id, $diff);
            $qty -= $diff;
            //--- เมื่อลบยอดค้างรับออกแล้วยังเหลือยอดอีกแสดงว่าแถวนี้รับครบแล้ว ให้ update valid เป็น 1
            if($valid)
            {
              $this->transform_model->valid_detail($rs->id);
            }
          }
        } //--- end if qty > 0
      } //--- endforeach
    }
  }


  //--- update receive_qty in order_transform_detail
  public function unreceive_product($order_code, $product_code, $qty)
  {
    $sc = TRUE;

    $list = $this->transform_model->get_transform_product_by_code($order_code, $product_code);

    if( ! empty($list))
    {
      foreach($list as $rs)
      {
        if($qty > 0 && $rs->receive_qty > 0)
        {
          $diff = $rs->receive_qty - $qty;

          if($diff >= 0 )
          {
            //--- ถ้า dif มากกว่ายอดที่รับมาให้ใช้ยอดรับ
            //--- หากยอดค้าง มี 2 แถว แถวแรก 5 แถวที่ 2 อีก 5 รวมเป็น 10
            //--- แต่รับเข้ามา 8
            //--- รอบแรก ยอด diff = 5 ซึ่งน้อยกว่า ยอดรับ ให้ใช้ยอด diff (ยอดค้างรับของแถวนั้น)
            //--- รอบสอง ยอด diff = 5 แต่ยอดรับจะเหลือ 3 เพราะถูกตัดออกไปรอบแรก 5 (จากยอดรับ 8)
            //--- รอบสองจึงต้องใช้ยอดรับที่เหลือในการ update
            if( ! $this->transform_model->update_receive_qty($rs->id, (-1) * $qty))
            {
              $sc = FALSE;
            }

            //--- เมื่อลบยอดค้างรับออกแล้วยังเหลือยอดอีกแสดงว่าแถวนี้รับครบแล้ว ให้ update valid เป็น 1
            if( ! $this->transform_model->unvalid_detail($rs->id))
            {
              $sc = FALSE;
            }

            $qty -= $diff;
          }
        } //--- end if qty > 0
      } //--- endforeach
    }

    return $sc;
  }


  public function cancle_received()
  {
    $sc = TRUE;

    if($this->input->post('receive_code'))
    {
      if($this->input->post('reason'))
      {
        $this->load->model('inventory/movement_model');
        $this->load->model('stock/stock_model');
        $code = $this->input->post('receive_code');
        $reason = trim($this->input->post('reason'));
        $force_cancel = $this->input->post('force_cancel') == 1 ? TRUE : FALSE;

        $doc = $this->receive_transform_model->get($code);

        if( ! empty($doc))
        {
          if($this->pm->can_delete)
          {
            if($doc->status != 2)
            {
              if($doc->status == 1)
              {
                $details = $this->receive_transform_model->get_details($code);

                if( ! empty($details))
                {
                  foreach($details as $rs)
                  {
                    if($sc === FALSE) { break; }

                    $stock = $this->stock_model->get_stock_zone($doc->zone_code, $rs->product_code);

                    if($stock < $rs->qty)
                    {
                      $sc = FALSE;
                      $this->error = "สต็อกคงเหลือไม่เพียงพอให้ยกเลิก";
                    }
                  }
                }
              }

              if($sc === TRUE)
              {
                $this->db->trans_begin();

                if($doc->status == 1)
                {
                  if( ! empty($details))
                  {
                    foreach($details as $rs)
                    {
                      if($sc === FALSE) { break; }

                      if( ! $this->stock_model->update_stock_zone($doc->zone_code, $rs->product_code, ($rs->qty * -1)))
                      {
                        $sc = FALSE;
                        $this->error = "Failed to update stock";
                      }

                      if($sc === TRUE)
                      {
                        if( ! $this->unreceive_product($doc->order_code, $rs->product_code, $rs->qty))
                        {
                          $sc = FALSE;
                          $this->error = "Update ยอดค้างรับไม่สำเร็จ";
                        }
                      }
                    }
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
                  if( ! $this->receive_transform_model->cancle_details($code) )
                  {
                    $sc = FALSE;
                    $this->error = "ยกเลิกรายการไม่สำเร็จ";
                  }
                }

                if($sc === TRUE)
                {
                  $arr = array(
                    'status' => 2,
                    'cancle_reason' => $reason
                  );

                  if(! $this->receive_transform_model->update($code, $arr)) //--- 0 = ยังไม่บันทึก 1 = บันทึกแล้ว 2 = ยกเลิก
                  {
                    $sc = FALSE;
                    $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
                  }
                }

                if($sc === TRUE)
                {
                  if($doc->status == 1)
                  {
                    //--- unclose WQ
                    $this->transform_model->unclose_transform($doc->order_code);
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
            }
            else
            {
              $sc = FALSE;
              $this->error = "เอกสารถูกยกเลิกไปแล้ว";
            }
          }
          else
          {
            $sc = FALSE;
            set_error('permission');
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบเลขที่เอกสาร";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขทีเอกสาร';
    }

    $this->_response($sc);
  }


  public function get_transform_detail()
  {
    $sc = '';
    $receive_code = $this->input->get('receive_code');
    $code = $this->input->get('order_code');
    $details = $this->receive_transform_model->get_transform_details($code);
    $pm = get_permission('ICRTCOST', $this->_user->uid, $this->_user->id_profile);
    $can_edit_price = ($pm->can_view + $pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0 ? TRUE : FALSE;
    $ds = array();
    if( ! empty($details))
    {
      $no = 1;
      $totalQty = 0;
      $totalReceive = 0;
      $totalUncomplete = 0;
      $totalBacklog = 0;

      foreach($details as $rs)
      {
        $uncomplete_qty = $this->receive_transform_model->get_sum_uncomplete_qty($code, $rs->product_code, $receive_code);
        $diff = $rs->sold_qty - ($rs->receive_qty + $uncomplete_qty);
        $diff = $diff < 0 ? 0 : $diff;

        $arr = array(
          'no' => $no,
          'barcode' => $rs->barcode,
          'pdCode' => $rs->product_code,
          'pdName' => $rs->name,
          'qty' => round($rs->sold_qty,2),
          'received' => round($rs->receive_qty,2),
          'uncomplete' => round($uncomplete_qty, 2),
          'price' => round($rs->price,2),
          'limit' => $diff,
          'backlog' => number($diff),
          'disabled' => $can_edit_price ? "" : "disabled"
        );

        array_push($ds, $arr);
        $no++;
        $totalQty += $rs->sold_qty;
        $totalReceive += $rs->receive_qty;
        $totalUncomplete += $uncomplete_qty;
        $totalBacklog += $diff;
      }

      $arr = array(
        'qty' => number($totalQty),
        'received' => number($totalReceive),
        'uncomplete' => number($totalUncomplete),
        'backlog' => number($totalBacklog)
      );
      array_push($ds, $arr);

      $sc = json_encode($ds);
    }
    else
    {
      $sc = 'ใบเบิกสินค้าไม่ถูกต้องหรือถูกปิดไปแล้ว';
    }

    echo $sc;
  }


  public function edit($code)
  {
    $doc = $this->receive_transform_model->get($code);

    if( ! empty($doc))
    {
      $pm = get_permission('ICRTCOST', $this->_user->uid, $this->_user->id_profile);
      $can_edit_price = ($pm->can_view + $pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0 ? TRUE : FALSE;

      $ds = array(
        'doc' => $doc,
        'details' => array(),
      );

      $zone_code = $doc->zone_code;
      $zone_name = "";
      $zone = NULL;

      if($zone_code != "" && $zone_code != NULL)
      {
        $this->load->model('masters/zone_model');
        $zone = $this->zone_model->get($zone_code);
        $zone_code = empty($zone) ? "" : $zone->code;
        $zone_name = empty($zone) ? "" : $zone->name;
      }

      $doc->zone_code = $zone_code;
      $doc->zone_name = $zone_name;

      $details = $this->receive_transform_model->get_transform_details($doc->order_code);

      if( ! empty($details))
      {
        $no = 1;
        $totalQty = 0;
        $totalReceive = 0;
        $totalUncomplete = 0;
        $totalBacklog = 0;
        $totalInputQty = 0;
        $totalAmount = 0;

        foreach($details as $rs)
        {
          $row = $this->receive_transform_model->get_detail_row($doc->code, $rs->product_code);

          $uncomplete_qty = $this->receive_transform_model->get_sum_uncomplete_qty($rs->order_code, $rs->product_code, $doc->code);
          $diff = $rs->sold_qty - ($rs->receive_qty + $uncomplete_qty);
          $diff = $diff < 0 ? 0 : $diff;
  				$cost = empty($row[0]) ? $this->get_avg_cost($rs->product_code) : $row[0]->price;
  				$cost = $cost == 0 ? $rs->price : $cost;
          $receive_qty = empty($row[0]) ? 0 : round($row[0]->qty); //( ! empty($row) ? round($row->qty, 2) : 0);
          $amount = round($cost * $receive_qty, 2);

          $arr = array(
            'no' => $no,
            'barcode' => $rs->barcode,
            'product_code' => $rs->product_code,
            'product_name' => limitText($rs->name, 50),
            'qty' => round($rs->sold_qty,2),
            'received' => round($rs->receive_qty,2),
            'uncomplete' => round($uncomplete_qty, 2),
            'receive_qty' => $receive_qty,
            'price' => round($cost,2),
            'amount' => $amount,
            'limit' => $diff,
            'backlog' => number($diff),
            'disabled' => $can_edit_price ? "" : "disabled"
          );

          array_push($ds['details'], (object)$arr);

          $no++;
          $totalInputQty += $receive_qty;
          $totalQty += $rs->sold_qty;
          $totalReceive += $rs->receive_qty;
          $totalUncomplete += $uncomplete_qty;
          $totalBacklog += $diff;
        }

        $ds['totalQty'] = $totalQty;
        $ds['totalReceived'] = $totalReceive;
        $ds['totalUncomplete'] = $totalUncomplete;
        $ds['totalBacklog'] = $totalBacklog;
        $ds['totalInputQty'] = $totalInputQty;
        $ds['totalAmount'] = $totalAmount;
      }

      $this->load->view('inventory/receive_transform/receive_transform_edit', $ds);
    }
    else
    {
      $this->page_error();
    }
  }


  public function update_header()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->code) && ! empty($ds->date_add))
    {
      $date_add = db_date($ds->date_add, TRUE);
      $shipped_date = empty($ds->shipped_date) ? NULL : db_date($ds->shipped_date, TRUE);

      $arr = array(
        'date_add' => $date_add,
        'shipped_date' => $shipped_date,
        'remark' => get_null($ds->remark),
        'user' => $this->_user->uname
      );

      if( ! $this->receive_transform_model->update($ds->code, $arr))
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



  //--- check exists document code
  public function is_exists($code)
  {
    $ext = $this->receive_transform_model->is_exists($code);
    if($ext)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }



  public function add_new()
  {
    $this->load->view('inventory/receive_transform/receive_transform_add');
  }


  public function add()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds) && ! empty($ds->date_add))
    {
      $date_add = db_date($ds->date_add, TRUE);
      $shipped_date = empty($ds->shipped_date) ? NULL : db_date($ds->shipped_date, TRUE);
      $code = $this->get_new_code($date_add);

      $arr = array(
        'code' => $code,
        'order_code' => NULL,
        'invoice_code' => NULL,
        'remark' => get_null($ds->remark),
        'date_add' => $date_add,
        'shipped_date' => $shipped_date,
        'user' => $this->_user->uname
      );

      if( ! $this->receive_transform_model->add($arr))
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

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_TRANSFORM');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_TRANSFORM');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_transform_model->get_max_code($pre);
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



	public function get_transform_backlogs($code, $product_code)
	{
		return $this->receive_transform_model->get_transform_backlogs($code, $product_code);
	}


  public function clear_filter()
  {
    $filter = array(
      'trans_code',
      'trans_invoice',
      'trans_order_code',
      'trans_from_date',
      'trans_to_date',
      'trans_status',
      'trans_must_accept',
      'trans_sap_status',
      'trans_zone',
      'trans_warehouse'
    );
    clear_filter($filter);
  }

} //--- end class
