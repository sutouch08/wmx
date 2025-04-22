<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends PS_Controller
{
  public $menu_code = 'ICTRWH';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'TRANSFER';
	public $title = 'โอนสินค้าระหว่างคลัง';
  public $filter;
  public $error = "";
  public $require_remark = 1;
  public $is_mobile = FALSE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/transfer';
    $this->load->model('inventory/transfer_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');
    $this->load->helper('warehouse');
    $this->load->library('user_agent');

    $this->is_mobile = $this->agent->is_mobile();
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'tr_code', ''),
      'wx_code' => get_filter('wx_code', 'wx_code', ''),
      'pallet_no' => get_filter('pallet_no', 'pallet_no', ''),
      'from_warehouse' => get_filter('from_warehouse', 'tr_from_warehouse', 'all'),
      'to_warehouse' => get_filter('to_warehouse', 'tr_to_warehouse', 'all'),
      'user' => get_filter('user', 'tr_user', 'all'),
      'status' => get_filter('status', 'tr_status', ($this->is_mobile ? '3' : 'all')),
      'is_approve' => get_filter('is_approve', 'tr_is_approve', 'all'),
      'valid' => get_filter('valid', 'tr_valid', 'all'),
      'sap' => get_filter('sap', 'tr_sap', 'all'),
      'must_accept' => get_filter('must_accept', 'tr_must_accept', 'all'),
      'from_date' => get_filter('fromDate', 'tr_fromDate', ''),
      'to_date' => get_filter('toDate', 'tr_toDate', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->transfer_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs     = $this->transfer_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $whs = array();
    $usr = array();

    if( ! empty($docs))
    {
      foreach($docs as $rs)
      {
        if(empty($usr[$rs->user]))
        {
          $user = $this->user_model->get($rs->user);
          $usr[$rs->user] = empty($user) ? "" : $user->name;
        }

        if(empty($whs[$rs->from_warehouse]))
        {
          $fWh = $this->warehouse_model->get($rs->from_warehouse);
          $whs[$rs->from_warehouse] = empty($fWh) ? "" : $fWh->name;
        }

        if(empty($whs[$rs->to_warehouse]))
        {
          $tWh = $this->warehouse_model->get($rs->to_warehouse);
          $whs[$rs->to_warehouse] = empty($tWh) ? "" : $tWh->name;
        }

        $rs->display_name = $usr[$rs->user];
        $rs->from_warehouse_name = $whs[$rs->from_warehouse];
        $rs->to_warehouse_name = $whs[$rs->to_warehouse];
      }
    }

    $filter['docs'] = $docs;
		$this->pagination->initialize($init);

    if($this->is_mobile)
    {
      $this->load->view('transfer/mobile/transfer_list_mobile', $filter);
    }
    else
    {
      $this->load->view('transfer/transfer_list', $filter);
    }
  }


  public function import_data()
	{
    $sc = TRUE;

    $code = $this->input->post('transfer_code');

    if( ! empty($code))
    {
      $doc = $this->transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == -1 OR $doc->status == 0)
        {
          $uid = genUid();
          $import = 0;
          $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
        	$path = $this->config->item('upload_path').'transfer/';
          $file	= 'uploadFile';
          $Ymd = date('Ymd');
      		$config = array(   // initial config for upload class
      			"allowed_types" => "xlsx",
      			"upload_path" => $path,
      			"file_name"	=> "Transfer-import-{$Ymd}-{$uid}",
      			"max_size" => 5120,
      			"overwrite" => TRUE
      		);

      		$this->load->library("upload", $config);

      		if(! $this->upload->do_upload($file))
          {
            $sc = FALSE;
            $this->error = $this->upload->display_errors();
      		}
          else
          {
            ini_set('max_execution_time', 1200);
            $this->load->library('excel');
            $info = $this->upload->data();
            /// read file
      			$excel = PHPExcel_IOFactory::load($info['full_path']);
      			//get only the Cell Collection
            $cs	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

            $count = count($cs);
            $limit = intval(getConfig('IMPORT_ROWS_LIMIT')) + 1;
            $must_accept = 0;
            $paleetNo = NULL;

            if($count > $limit)
            {
              $sc = FALSE;
              $this->error = "Import data exceeds limit rows : allow {$limit} rows";
            }
            else
            {
              $i = 1;

              foreach($cs as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }

                if($i == 1)
                {
                  if(trim($rs['A']) != 'ProductCode')
                  {
                    $sc = FALSE;
                    $this->error = "Column A should be 'ProductCode'";
                  }

                  if(trim($rs['B']) != 'FromBinCode')
                  {
                    $sc = FALSE;
                    $this->error = "Column B should be 'FromBinCode'";
                  }

                  if(trim($rs['C']) != 'ToBinCode')
                  {
                    $sc = FALSE;
                    $this->error = "Column C should be 'ToBinCode'";
                  }

                  if(trim($rs['D']) != 'Qty')
                  {
                    $sc = FALSE;
                    $this->error = "Column C should be 'Qty'";
                  }

                  $i++;
                }
                else
                {
                  if($i === 2)
                  {
                    //--- check binCode to match with header warehouse
                    $fromZone = $this->zone_model->get($rs['B']);
                    $toZone = $this->zone_model->get($rs['C']);

                    $must_accept = empty($toZone) ? 0 : (empty($toZone->user_id) ? 0 : 1);
                    $palletNo = empty($rs['E']) ? NULL : trim($rs['E']);

                    if( ! empty($palletNo))
                    {
                      if($this->transfer_model->is_exists_pallet($palletNo))
                      {
                        $sc = FALSE;
                        $this->error = "Pallet No {$palletNo} already exists";
                      }
                    }

                    if(empty($fromZone) OR empty($toZone))
                    {
                      $sc = FALSE;
                      if(empty($fromZone))
                      {
                        $this->error .= "FromBinCode '{$rs['B']}' is not valid bin code".PHP_EOL;
                      }

                      if(empty($toZone))
                      {
                        $this->error .= "ToBinCode '{$rs['C']}' is not valid bin code".PHP_EOL;
                      }
                    }
                    else
                    {
                      if($fromZone->warehouse_code != $doc->from_warehouse)
                      {
                        $sc = FALSE;
                        $this->error .= "FromBinCode '{$rs['B']}' not match document warehouse".PHP_EOL;
                      }

                      if($toZone->warehouse_code != $doc->to_warehouse)
                      {
                        $sc = FALSE;
                        $this->error .= "ToBinCode '{$rs['C']}' not match document warehouse".PHP_EOL;
                      }
                    }

                    $i++;
                    break;
                  }
                }

                if($i > 2)
                {
                  break;
                }
              }
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_begin();
            //--- drop current details
            if( $this->transfer_model->drop_all_detail($code))
            {
              //--- update transfer header to match import file
              $arr = array(
                'is_import' => 1,
                'import_id' => $uid,
                'import_user' => $this->_user->uname,
                'pallet_no' => $palletNo
              );

              if( ! $this->transfer_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update transfer header";
              }

              //--- import items rows
              if($sc === TRUE)
              {
                $this->load->model('masters/products_model');
                $i = 1;

                foreach($cs as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  //---
                  if($i == 1)
                  {
                    //--- skip first row
                    $i++;
                  }
                  else
                  {
                    //--- skip empty rows
                    if( ! empty($rs['A']) && ! empty($rs['B']) && ! empty($rs['C']) && ! empty($rs['D']))
                    {
                      $qty = $rs['D'];

                      if($qty > 0)
                      {
                        $pd = $this->products_model->get(trim($rs['A']));

                        if( ! empty($pd))
                        {
                          $arr = array(
                            'transfer_code' => $code,
                            'product_code' => $pd->code,
                            'product_name' => $pd->name,
                            'from_zone' => trim($rs['B']),
                            'to_zone' => trim($rs['C']),
                            'qty' => $qty,
                            'must_accept' => $must_accept,
                            'is_import' => 1,
                            'import_id' => $uid,
                            'pallet_no' => $palletNo
                          );

                          if( ! $this->transfer_model->add_detail($arr))
                          {
                            $sc = FALSE;
                            $this->error = "Failed to insert item row @ Line {$i} : {$pd->code}";
                          }
                        }
                        else
                        {
                          $sc = FALSE;
                          $this->error = "Invaild ProductCode or ProductCode not found : {$rs['A']}";
                        }
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = "Qty cannot be empty";
                      }//--- if(qty > 0)
                    } //--- if( ! empty($rs['A']) && ! empty($rs['B']) && ! empty($rs['C']) && ! empty($rs['D']))
                    $i++;
                  } //--- if(i == 1)
                } //--- foreach
              }//--- $sc = TRUE
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to delete previous items";
            }

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          } //--- $sc === TRUE
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid document status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document number";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
	}


  public function get_template_file()
  {
    $path = $this->config->item('upload_path').'transfer/';
    $file_name = $path."import_transfer_template.xlsx";

    if(file_exists($file_name))
    {
      header('Content-Description: File Transfer');
      header('Content-Type:Application/octet-stream');
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: 0');
      header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
      header('Content-Length: '.filesize($file_name));
      header('Pragma: public');

      flush();
      readfile($file_name);
      die();
    }
    else
    {
      echo "File Not Found";
    }
  }


  public function view_detail($code)
  {
    $this->load->model('approve_logs_model');
    $doc = $this->transfer_model->get($code);

    $details = $this->transfer_model->get_details($code);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'approve_logs' => $this->approve_logs_model->get($code),
      'accept_list' => $this->transfer_model->get_accept_list($code),
      'barcode' => FALSE
    );

    $this->load->view('transfer/transfer_view', $ds);
  }


  public function add_new()
  {
    $this->load->view('transfer/transfer_add');
  }


  public function add()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $date_add = db_date($ds->date_add, TRUE);
      $code = $this->get_new_code($date_add);
      $arr = array(
        'code' => $code,
        'from_warehouse' => $ds->from_warehouse,
        'to_warehouse' => $ds->to_warehouse,
        'remark' => get_null($ds->remark),
        'user' => $this->_user->uname,
        'date_add' => $date_add
      );

      if( ! $this->transfer_model->add($arr))
      {
        $sc = FALSE;
        $this->error = 'เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง';
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


  public function is_document_avalible()
  {
    $code = $this->input->get('code');
    $uuid = $this->input->get('uuid');
    if( ! $this->transfer_model->is_document_avalible($code, $uuid))
    {
      echo "not_available";
    }
    else
    {
      echo "available";
    }
  }


  public function edit($code, $uuid, $barcode = '')
  {
    $doc = $this->transfer_model->get($code);

    if( ! empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);

    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'barcode' => $barcode == '' ? FALSE : TRUE
    );

    $this->transfer_model->update_uuid($code, $uuid);

    $this->load->view('transfer/transfer_edit', $ds);
  }


  public function update_transfer_qty()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $qty = $this->input->post('qty');

    if( ! $this->transfer_model->update_detail($id, ['qty' => $qty]))
    {
      $sc = FALSE;
      $this->error = "Failed to update qty";
    }

    $this->_response($sc);
  }


  public function update_uuid()
  {
    $sc = TRUE;
    $code = trim($this->input->post('code'));
    $uuid = trim($this->input->post('uuid'));

    if( ! empty($uuid))
    {
      return $this->transfer_model->update_uuid($code, $uuid);
    }
  }


  public function get_zone()
  {
    $sc = TRUE;
    $zone_code = $this->input->get('zone_code');
    $warehouse_code = $this->input->get('warehouse_code');

    $zone = $this->zone_model->get_zone($zone_code, $warehouse_code);

    if(empty($zone))
    {
      $sc = FALSE;
      $this->error = "Invalid zone";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'zone' => $sc === TRUE ? $zone : NULL
    );

    echo json_encode($arr);
  }


  public function process($code)
  {
    $this->load->model('masters/products_model');

    $doc = $this->transfer_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 3)
      {
        $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
        $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
        $details = $this->transfer_model->get_details($code);
        $bcList = [];

        foreach($details as $rs)
        {
          if(empty($bcList[$rs->product_code]))
          {
            $barcode = $this->products_model->get_barcode($rs->product_code);
            $bcList[$rs->product_code] = (object)['barcode' => $barcode, 'product_code' => $rs->product_code];
          }

          $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
          $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
          $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
          $rs->barcode = $bcList[$rs->product_code]->barcode;
        }

        $ds = array(
          'title' => $doc->code . (empty($doc->pallet_no) ? NULL : "  [{$doc->pallet_no}]") . "<br/>".$doc->from_warehouse." | ".$doc->to_warehouse,
          'doc' => $doc,
          'details' => $details,
          'bcList' => $bcList
        );

        $this->load->view('transfer/mobile/transfer_process_mobile', $ds);
      }
      else
      {
        $this->load->view('transfer/invalid_state');
      }
    }
    else
    {
      $this->page_error();
    }
  }


  public function update($code)
  {
    $sc = TRUE;

    $fWh = $this->input->post('from_warehouse');
		$tWh = $this->input->post('to_warehouse');
		$wx_code = get_null(trim($this->input->post('wx_code')));

    if($sc === TRUE)
    {
      //---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
      $direction = 0;  //--- Wrx to Wrx

      $must_approve = getConfig('STRICT_TRANSFER') == 1 ? 1 : 0;

      $arr = array(
        'date_add' => db_date($this->input->post('date_add'), TRUE),
        'from_warehouse' => $fWh,
        'to_warehouse' => $tWh,
        'remark' => get_null(trim($this->input->post('remark'))),
        'direction' => $direction,
        'wx_code' => $wx_code,
        'must_approve' => $must_approve,
        'update_user' => $this->_user->uname
      );

      if( ! $this->transfer_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "ปรับปรุงรายการไม่สำเร็จ";
      }
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function check_temp_exists($code)
  {
    $temp = $this->transfer_model->is_exists_temp($code);
    if($temp === TRUE)
    {
      echo 'exists';
    }
    else
    {
      echo 'not_exists';
    }
  }


  function pull_back()
  {
    $sc = TRUE;
    $code = $this->input->post('transfer_code');

    if($code)
    {
      $doc = $this->transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 3)
        {
          $this->db->trans_begin();

          if( ! $this->transfer_model->drop_all_temp($code))
          {
            $sc = FALSE;
            $this->error = "Failed to remove previous transfer temp";
          }

          if($sc === TRUE)
          {
            $arr = array(
              'status' => -1,
              'update_user' => $this->_user->uname
            );

            if( ! $this->transfer_model->update($code, $arr))
            {
              $sc = FALSE;
              set_error('update');
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

    $this->_response($sc);
  }


  public function save_as_request()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->transfer_model->get($code);

  		if( ! empty($doc))
  		{
  			if($doc->status == -1)
  			{
  				$details = $this->transfer_model->get_details($code);

  				if( ! empty($details))
  				{
            $this->db->trans_begin();

            //--- delete current temp
            if( ! $this->transfer_model->drop_all_temp($code))
            {
              $sc = FALSE;
              $this->error = "Failed remove previous temp data";
            }

            if($sc === TRUE)
            {
              foreach($details as $rs)
              {
                if($sc === FALSE)
                {
                  break;
                }

                $qty = $rs->qty - $rs->wms_qty;

                if($qty > 0)
                {
                  $arr = array(
                    'transfer_code' => $rs->transfer_code,
                    'product_code' => $rs->product_code,
                    'zone_code' => $rs->from_zone,
                    'qty' => $qty
                  );

                  if( ! $this->transfer_model->add_temp($arr))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to insert transfer temp";
                  }
                }
              } //--- end foreach


              if($sc === TRUE)
              {
                $arr = array(
                  'status' => 3,
                  'is_approve' => 0
                );

                if( ! $this->transfer_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Update Status Failed";
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
  					$this->error = "ไม่พบรายการโอนย้าย";
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
  			$this->error = "เลขที่เอกสารไม่ถูกต้อง";
  		}
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : ($ex = 0 ? 'warning' : 'failed'),
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function save_mobile_transfer($code)
  {
    $sc = TRUE;
    $this->load->model('stock/stock_model');
    $this->load->model('inventory/movement_model');
    $doc = $this->transfer_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 3)
      {
        $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();
        $details = $this->transfer_model->get_details($code);
        $valid = 1;

        $this->db->trans_begin();

        if( ! empty($details))
        {
          foreach($details as $rs)
          {
            if($sc === FALSE) { break; }

            if($rs->qty > 0 && $rs->wms_qty > 0)
            {
              $stock = $this->stock_model->get_stock_zone($rs->from_zone, $rs->product_code);

              if($stock < $rs->wms_qty)
              {
                $sc = FALSE;
                $this->error = "สต็อกต้นทางไม่พอโอนออก";
              }

              if($sc === TRUE)
              {
                if( ! $this->stock_model->update_stock_zone($rs->from_zone, $rs->product_code, ($rs->wms_qty * -1)))
                {
                  $sc = FALSE;
                  $this->error = "ย้ายสต็อกออกจากโซนต้นทางไม่สำเร็จ";
                }
              }

              if($sc === TRUE)
              {
                $move_out = array(
                  'reference' => $doc->code,
                  'warehouse_code' => $doc->from_warehouse,
                  'zone_code' => $rs->from_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => 0,
                  'move_out' => $rs->wms_qty,
                  'date_add' => $date_add
                );

                if( ! $this->movement_model->add($move_out))
                {
                  $sc = FALSE;
                  $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->stock_model->update_stock_zone($rs->to_zone, $rs->product_code, $rs->wms_qty))
                {
                  $sc = FALSE;
                  $this->error = "ย้ายสต็อกเข้าโซนปลายทางไม่สำเร็จ";
                }
              }

              if($sc === TRUE)
              {
                $move_in = array(
                  'reference' => $doc->code,
                  'warehouse_code' => $doc->to_warehouse,
                  'zone_code' => $rs->to_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => $rs->wms_qty,
                  'move_out' => 0,
                  'date_add' => $date_add
                );

                if( ! $this->movement_model->add($move_in))
                {
                  $sc = FALSE;
                  $this->error = 'บันทึก movement ขาเข้าไม่สำเร็จ';
                }
              }

              if($rs->wms_qty == $rs->qty)
              {
                if( ! $this->transfer_model->update_detail($rs->id, ['valid' => 1, 'date_upd' => now()]))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update transfer row status";
                }
              }
              else
              {
                $valid = 0;
              }
            } //--- end if wms_qty > 0
          } //--- end foreach
        } //-- end if( ! empty($details))

        if($sc === TRUE)
        {
          $arr = array(
            'shipped_date' => $date_add,
            'status' => 1,
            'valid' => $valid
          );

          if( ! $this->transfer_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Update failed : change document status failed";
          }
        }

        if($sc === TRUE)
        {
          if( ! $this->transfer_model->drop_all_temp($code))
          {
            $sc = FALSE;
            $this->error = "Failed to delete exists temp";
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

    $this->_response($sc);
  }


	public function save_transfer($code)
  {
    $sc = TRUE;
		$doc = $this->transfer_model->get($code);

		if( ! empty($doc))
		{
      $this->load->model('stock/stock_model');
      $this->load->model('inventory/movement_model');

			$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

			if($doc->status == -1 OR $doc->status == 0)
			{
				$details = $this->transfer_model->get_details($code);

				if( ! empty($details))
				{
          foreach($details as $rs)
          {
            if($sc === FALSE) { break; }

            $stock = $this->stock_model->get_stock_zone($rs->from_zone, $rs->product_code);

            if($stock < $rs->qty)
            {
              $sc = FALSE;
              $this->error = "สต็อกในโซนต้นทางไม่พอโอน <br/>SKU : {$rs->product_code}<br/>Qty : {$rs->qty} / {$stock}";
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_begin();

            foreach($details as $rs)
            {
              if( ! $this->stock_model->update_stock_zone($rs->from_zone, $rs->product_code, ($rs->qty * -1)))
              {
                $sc = FALSE;
                $this->error = "ย้ายสต็อกออกจากโซนต้นทางไม่สำเร็จ";
              }

              if($sc === TRUE)
              {
                $move_out = array(
                  'reference' => $code,
                  'warehouse_code' => $doc->from_warehouse,
                  'zone_code' => $rs->from_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => 0,
                  'move_out' => $rs->qty,
                  'date_add' => $date_add
                );

                //--- move out
                if(! $this->movement_model->add($move_out))
                {
                  $sc = FALSE;
                  $this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->stock_model->update_stock_zone($rs->to_zone, $rs->product_code, $rs->qty))
                {
                  $sc = FALSE;
                  $this->error = "ย้ายสต็อกเข้าโซนปลายทางไม่สำเร็จ";
                }
              }

              if($sc === TRUE)
              {
                $move_in = array(
                  'reference' => $code,
                  'warehouse_code' => $doc->to_warehouse,
                  'zone_code' => $rs->to_zone,
                  'product_code' => $rs->product_code,
                  'move_in' => $rs->qty,
                  'move_out' => 0,
                  'date_add' => $date_add
                );

                //--- move in
                if(! $this->movement_model->add($move_in))
                {
                  $sc = FALSE;
                  $this->error = 'บันทึก movement ขาเข้าไม่สำเร็จ';
                }
              }

              if($sc === TRUE)
              {
                $arr = array('wms_qty' => $rs->qty, 'valid' => 1);

                if( ! $this->transfer_model->update_detail($rs->id, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update wms qty at {$rs->product_code}";
                }
              }
            } //--- end foreach

            if($sc === TRUE)
            {
              $arr = array(
                'status' => 1,
                'shipped_date' => $date_add,
                'update_user' => $this->_user->uname,
                'valid' => 1
              );

              if( ! $this->transfer_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
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
					$this->error = "ไม่พบรายการโอนย้าย";
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

    $this->_response($sc);
  }


  public function unsave_transfer($code)
  {
    $sc = TRUE;
    $uat = is_true(getConfig('IS_UAT'));

    $this->load->model('inventory/movement_model');
    //--- check Transfer doc exists in SAP
    $doc = $this->transfer_model->get_sap_transfer_doc($code);

    if( ! empty($doc) && ! $uat)
    {
      $sc = FALSE;
      $this->error = "เอกสารเข้า SAP แล้วไม่อนุญาติให้ยกเลิก";
    }

    if($sc === TRUE OR $uat)
    {
      //--- check middle doc delete it if exists
      $middle = $this->transfer_model->get_middle_transfer_doc($code);

      if( ! empty($middle))
      {
        foreach($middle as $rs)
        {
          $this->transfer_model->drop_middle_exits_data($rs->DocEntry);
        }
      }


      $this->db->trans_begin();
      //--- change state to -1
      $arr = array(
        'status' => -1,
        'is_approve' => 0
      );

      if( ! $this->transfer_model->update($code, $arr))
      {
        $sc = FALSE;
        $this->error = "Failed to change status";
      }
      else
      {
        if( ! $this->transfer_model->valid_all_detail($code, 0))
        {
          $sc = FALSE;
          $this->error = "Failed to rollback transfer rows status";
        }
        else
        {
          if( ! $this->movement_model->drop_movement($code))
          {
            $sc = FALSE;
            $this->error = "Failed to remove stock movement";
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

    echo $sc === TRUE ? 'success' : $this->error;
  }


	public function add_to_transfer()
  {
    $sc = TRUE;

		$data = json_decode($this->input->post('data'));

		if( ! empty($data))
		{
			if(! empty($data->transfer_code))
	    {
	      $this->load->model('masters/products_model');

				$code = $data->transfer_code;
	      $from_zone = $data->from_zone;
	      $to_zone = $data->to_zone;

        $zone = $this->zone_model->get($to_zone);

	      $items = $data->items;

	      if( ! empty($items))
	      {
	        $this->db->trans_begin();

	        foreach($items as $item)
	        {
            if($sc === FALSE)
            {
              break;
            }

	          $id = $this->transfer_model->get_id($code, $item->item_code, $from_zone, $to_zone);

	          if( ! empty($id))
	          {
	            if( !$this->transfer_model->update_qty($id, $item->qty))
              {
                $sc = FALSE;
                $this->error = "Update data failed";
              }
	          }
	          else
	          {
	            $arr = array(
	              'transfer_code' => $code,
	              'product_code' => $item->item_code,
	              'product_name' => $this->products_model->get_name($item->item_code),
	              'from_zone' => $from_zone,
	              'to_zone' => $to_zone,
	              'qty' => $item->qty
	            );

	            if( ! $this->transfer_model->add_detail($arr))
              {
                $sc = FALSE;
                $this->error = "Insert data failed";
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
				$this->error = "Missing document code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing form data";
		}

    echo $sc === TRUE ? 'success' : $this->error;

  }


  public function roll_back_to_temp()
  {
    $sc = TRUE;

    $code = $this->input->post('transfer_code');
    $id = $this->input->post('id');
    $product_code = $this->input->post('product_code');

    if($code && $id && $product_code)
    {
      $doc = $this->transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 3)
        {
          $detail = $this->transfer_model->get_detail_by_id($id);

          if( ! empty($detail))
          {
            if($detail->wms_qty > 0)
            {
              $this->db->trans_begin();

              $arr = array(
                'transfer_code' => $detail->transfer_code,
                'product_code' => $detail->product_code,
                'zone_code' => $detail->from_zone,
                'qty' => $detail->wms_qty
              );

              if( ! $this->transfer_model->update_temp($arr))
              {
                $sc = FALSE;
                $this->error = "Failed to update temp row";
              }

              if($sc === TRUE)
              {
                if( ! $this->transfer_model->update_detail($id, ['wms_qty' => 0]))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update wms_qty";
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
            $this->error = "Item not found";
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
        $this->error = "ไม่พบเลขที่เอกสาร";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function add_to_temp()
  {
    $sc = TRUE;

    if($this->input->post('transfer_code'))
    {
      $this->load->model('masters/products_model');
      $res = [];
      $code = $this->input->post('transfer_code');
      $zone_code = $this->input->post('from_zone');
      $barcode = trim($this->input->post('barcode'));
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);

      if( ! empty($item))
      {
        $product_code = $item->code;
        $stock = $this->stock_model->get_stock_zone($zone_code, $product_code);
        //--- จำนวนที่อยู่ใน temp
        $temp_qty = $this->transfer_model->get_temp_qty($code, $product_code, $zone_code);

        //--- จำนวนที่โอนได้คงเหลือ
        $cqty = $stock - $temp_qty;

        if($qty <= $cqty)
        {
          $arr = array(
            'transfer_code' => $code,
            'product_code' => $product_code,
            'zone_code' => $zone_code,
            'qty' => $qty
          );

          if($this->transfer_model->update_temp($arr) === FALSE)
          {
            $sc = FALSE;
            $this->error = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
          }

          if($sc === TRUE)
          {
            $temp_qty += $qty;

            $res = array(
              'barcode' => $barcode,
              'product_code' => $product_code,
              'stock_qty' => $stock,
              'temp_qty' => $temp_qty
            );
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = 'ยอดในโซนไม่เพียงพอ';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $res : NULL
    );

    echo json_encode($arr);
  }


  public function move_to_zone()
  {
    $sc = TRUE;
    $res = NULL;

    if($this->input->post('transfer_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('transfer_code');
      $product_code = $this->input->post('product_code');
      $to_zone = $this->input->post('zone_code');
      $zone = $this->zone_model->get($to_zone);
      $qty = $this->input->post('qty');

      //---- ดึงรายการตั้นต้นมาเช็ค
      $detail = $this->transfer_model->get_detail_row($code, $product_code, NULL, $to_zone);

      if( ! empty($detail))
      {
        $balance = $detail->qty - $detail->wms_qty;

        if($balance <= 0)
        {
          $sc = FALSE;
          $this->error = "ยอดตั้งต้นถูกรับครบแล้ว";
        }

        if($sc === TRUE && $balance < $qty)
        {
          $sc = FALSE;
          $this->error = "ยอดค้างรับน้อยกว่า จำนวนที่ระบุ ค้าง ({$balance}) ระบ ุ({$qty})";
        }

        if($sc === TRUE)
        {
          $temp = $this->transfer_model->get_temp_row($code, $product_code, $detail->from_zone);

          if( ! empty($temp))
          {
            if($temp->qty < $qty)
            {
              $sc = FALSE;
              $this->error = "จำนวนใน Temp ไม่เพียงพอ";
            }

            if($sc === TRUE)
            {
              $this->db->trans_begin();

              if($temp->qty == $qty)
              {
                if( ! $this->transfer_model->delete_temp($temp->id))
                {
                  $sc = FALSE;
                  $this->error = "Failed to delete temp id : {$temp->id}";
                }
                else
                {
                  $res = ['id' => $temp->id, 'qty' => 0];
                }
              }
              else
              {
                if( ! $this->transfer_model->update_temp_qty($temp->id, ($qty * -1)))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update temp id : {$temp->id}";
                }
                else
                {
                  $res = ['id' => $temp->id, 'qty' => $temp->qty - $qty];
                }
              }

              if($sc === TRUE)
              {
                if( ! $this->transfer_model->update_wms_qty($detail->id, $qty))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update wms_qty id : {$detail->id}";
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
            $this->error = "ไม่พบรายการใน temp";
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการตั้งต้นที่ตรงกับโซนต้นทาง-ปลายทาง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $res
    );

    echo json_encode($arr);
  }


  public function save_to_zone()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $this->load->model('masters/products_model');

      $code = $ds->transfer_code;
      $to_zone = $ds->zone_code;
      $items = $ds->items;
      $zone = $this->zone_model->get($to_zone);

      if( ! empty($items))
      {
        $this->db->trans_begin();

        foreach($items as $rs)
        {
          $qty = $rs->qty;
          //---- ดึงรายการตั้นต้นมาเช็ค
          $detail = $this->transfer_model->get_detail_row($code, $rs->product_code, NULL, $to_zone);

          if( ! empty($detail))
          {
            $balance = $detail->qty - $detail->wms_qty;

            if($balance <= 0)
            {
              $sc = FALSE;
              $this->error = "{$rs->product_code} : ยอดตั้งต้นถูกรับครบแล้ว";
            }

            if($sc === TRUE && $balance < $qty)
            {
              $sc = FALSE;
              $this->error = "{$rs->product_code} : ยอดค้างรับน้อยกว่า จำนวนที่ระบุ ค้าง ({$balance}) ระบ ุ({$qty})";
            }

            if($sc === TRUE)
            {
              $temp = $this->transfer_model->get_temp_row($code, $rs->product_code, $detail->from_zone);

              if( ! empty($temp))
              {
                if($temp->qty < $qty)
                {
                  $sc = FALSE;
                  $this->error = "{$rs->product_code} : จำนวนใน Temp ไม่เพียงพอ";
                }

                if($sc === TRUE)
                {
                  if($temp->qty == $qty)
                  {
                    if( ! $this->transfer_model->delete_temp($temp->id))
                    {
                      $sc = FALSE;
                      $this->error = "{$rs->product_code} : Failed to delete temp id : {$temp->id}";
                    }
                    else
                    {
                      $res = ['id' => $temp->id, 'qty' => 0];
                    }
                  }
                  else
                  {
                    if( ! $this->transfer_model->update_temp_qty($temp->id, ($qty * -1)))
                    {
                      $sc = FALSE;
                      $this->error = "{$rs->product_code} : Failed to update temp id : {$temp->id}";
                    }
                    else
                    {
                      $res = ['id' => $temp->id, 'qty' => $temp->qty - $qty];
                    }
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->transfer_model->update_wms_qty($detail->id, $qty))
                    {
                      $sc = FALSE;
                      $this->error = "{$rs->product_code} : Failed to update wms_qty id : {$detail->id}";
                    }
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "ไม่พบรายการใน temp";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการตั้งต้นที่ตรงกับโซนต้นทาง-ปลายทาง";
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
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function is_exists($code, $old_code = NULL)
  {
    $exists = $this->transfer_model->is_exists($code, $old_code);
    if($exists)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }


  public function is_exists_detail($code)
  {
    $detail = $this->transfer_model->is_exists_detail($code);
    $temp = $this->transfer_model->is_exists_temp($code);

    if($detail === FALSE && $temp === FALSE)
    {
      echo 'not_exists';
    }
    else
    {
      echo 'exists';
    }
  }


  public function get_temp_table($code)
  {
    $ds = array();
    $temp = $this->transfer_model->get_transfer_temp($code);

    if( ! empty($temp))
    {
      $no = 1;

      $bcList = [];

      foreach($temp as $rs)
      {
        $ds[] = array(
          'no' => $no,
          'id' => $rs->id,
          'barcode' => $rs->barcode,
          'products' => $rs->product_code,
          'from_zone' => $rs->zone_code,
          'fromZone' => $this->zone_model->get_name($rs->zone_code),
          'qty' => $rs->qty
        );

        $no++;

        if(empty($bcList[$rs->barcode]))
        {
          $bcList[$rs->barcode] = ['barcode' => $rs->barcode, 'product_code' => $rs->product_code];
        }
      }

      $ds[] = array('bcList' => $bcList);
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }


  public function get_transfer_table($code)
  {
    $ds = array();
    $details = $this->transfer_model->get_details($code);

    if( ! empty($details))
    {
      $no = 1;
      $total_qty = 0;
      $total_wms = 0;

      foreach($details as $rs)
      {
        $btn_delete = '';

        if($this->pm->can_add OR $this->pm->can_edit && $rs->valid == 0)
        {
          $btn_delete .= '<button type="button" class="btn btn-minier btn-danger" ';
          $btn_delete .= 'onclick="rollBackToTemp('.$rs->id.', \''.$rs->product_code.'\')">';
          $btn_delete .= '<i class="fa fa-trash"></i></button>';
        }

        $arr = array(
          'id' => $rs->id,
          'no' => $no,
          'barcode' => $rs->barcode,
          'product_code' => $rs->product_code,
          'product_name' => $rs->product_name,
          'from_zone' => $rs->from_zone,
          'to_zone' => $rs->to_zone,
          'qty' => $rs->qty,
          'qty_label' => number($rs->qty),
          'wms_qty_label' => number($rs->wms_qty),
          'wms_qty' => $rs->wms_qty,
          'btn_delete' => $btn_delete
        );

        array_push($ds, $arr);
        $no++;
        $total_qty += $rs->qty;
        $total_wms += $rs->wms_qty;
      } //--- end foreach

      $arr = array(
        'totalQty' => number($total_qty),
        'totalWms' => number($total_wms)
      );

      array_push($ds, $arr);
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }


  public function get_transfer_zone($warehouse = '')
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $zone = $this->zone_model->search($txt, $warehouse);
    if( ! empty($zone))
    {
      foreach($zone as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบโซน';
    }

    echo json_encode($sc);
  }


  public function get_from_zone()
  {
    $sc = TRUE;
    $transfer_code = $this->input->get('transfer_code');
    $zone_code = $this->input->get('zone_code');

    if($zone_code && $transfer_code)
    {
      $doc = $this->transfer_model->get($transfer_code);

      if( ! empty($doc))
      {
        $zone = $this->zone_model->get_zone($zone_code, $doc->from_warehouse);

        if(empty($zone))
        {
          $sc = FALSE;
          $this->error = "Invalid zone";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invaild document no";
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
      'zone' => $sc === TRUE ? $zone : NULL
    );

    echo json_encode($arr);
  }


  public function get_to_zone()
  {
    $sc = TRUE;
    $transfer_code = $this->input->get('transfer_code');
    $zone_code = $this->input->get('zone_code');

    if($zone_code && $transfer_code)
    {
      $doc = $this->transfer_model->get($transfer_code);

      if( ! empty($doc))
      {
        $zone = $this->zone_model->get_zone($zone_code, $doc->to_warehouse);

        if(empty($zone))
        {
          $sc = FALSE;
          $this->error = "Invalid zone";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invaild document no";
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
      'zone' => $sc === TRUE ? $zone : NULL
    );

    echo json_encode($arr);
  }


  public function get_product_in_zone()
  {
    ini_set('memory_limit','512M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
    ini_set('sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

    $sc = TRUE;
    $ds = array();

    if($this->input->get('zone_code'))
    {
      $this->load->model('masters/products_model');

      $zone_code = $this->input->get('zone_code');
      $transfer_code = $this->input->get('transfer_code');
      $product_code = get_null(trim($this->input->get('item_code')));
      $product_code = $product_code == '*' ? NULL : $product_code;

      $stock = $this->stock_model->get_all_stock_in_zone($zone_code, $product_code);

      if( ! empty($stock))
      {
        $no = 1;

        foreach($stock as $rs)
        {
          //--- จำนวนที่อยู่ใน temp
          $temp_qty = $this->transfer_model->get_temp_qty($transfer_code, $rs->product_code, $zone_code);
          //--- จำนวนที่อยู่ใน transfer_detail และยังไม่ valid
          $transfer_qty = $this->transfer_model->get_transfer_qty($transfer_code, $rs->product_code, $zone_code);
          //--- จำนวนที่โอนได้คงเหลือ
          $qty = $rs->qty - ($temp_qty + $transfer_qty);

          if($qty > 0)
          {
            $arr = array(
              'no' => $no,
              // 'barcode' => $this->products_model->get_barcode($rs->product_code),
              'product_code' => $rs->product_code,
              'product_name' => $rs->product_name,
              'qty' => $qty
            );

            array_push($ds, $arr);
            $no++;
          }
        }
      }
      else
      {
        array_push($ds, array("nodata" => "nodata"));
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc = TRUE ? json_encode($ds) : $this->error;
  }



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TRANSFER');
    $run_digit = getConfig('RUN_DIGIT_TRANSFER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->transfer_model->get_max_code($pre);
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


  public function delete_detail()
  {
    $sc = TRUE;

    $code = $this->input->post('transfer_code');
    $ids = json_decode($this->input->post('ids'));

    if( ! empty($ids))
    {
      $doc = $this->transfer_model->get_transfer($code);

      if( ! empty($doc))
      {
        if($doc->status < 1)
        {
          if( ! $this->transfer_model->delete_rows($ids))
          {
            $sc = FALSE;
            $this->error = "Failed to delete items";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid document status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $this->_response($sc);
  }


  public function delete_transfer($code)
  {
    $sc = TRUE;

    $reason = trim($this->input->post('reason'));
    $force_cancel = $this->input->post('force_cancel') == 1 ? 1 : 0;

    if($this->pm->can_delete)
    {
      $this->load->model('stock/stock_model');
      $this->load->model('inventory/movement_model');

      $doc = $this->transfer_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status != 2)
        {
          if($doc->status == 1)
          {
            $details = $this->transfer_model->get_details($code);

            if( ! empty($details))
            {
              foreach($details as $rs)
              {
                if($sc === FALSE) { break; }

                if($rs->qty > 0 && $rs->wms_qty > 0)
                {
                  $stock = $this->stock_model->get_stock_zone($rs->to_zone, $rs->product_code);
                  if($stock < $rs->wms_qty)
                  {
                    $sc = FALSE;
                    $this->error = "สต็อกในโซนไม่พอย้ายกลับ <br/>Zone : {$rs->to_zone} <br/>SKU : {$rs->product_code} <br/>Qty : {$rs->wms_qty} / {$stock}";
                  }
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

                  if( ! $this->stock_model->update_stock_zone($rs->to_zone, $rs->product_code, ($rs->wms_qty * -1)))
                  {
                    $sc = FALSE;
                    $this->error = "ย้ายสต็อกออกจากโซนไม่สำเร็จ";
                  }

                  if( ! $this->stock_model->update_stock_zone($rs->from_zone, $rs->product_code, $rs->wms_qty))
                  {
                    $sc = FALSE;
                    $this->error = "ย้ายสต็อกเข้าโซนไม่สำเร็จ";
                  }
                }
              }
            }

            if($sc === TRUE)
            {
              //--- clear temp
              if( ! $this->transfer_model->drop_all_temp($code))
              {
                $sc = FALSE;
                $this->error = "Failed to delete transfer temp";
              }
            }

            if($sc === TRUE)
            {
              //--- delete detail
              if( ! $this->transfer_model->drop_all_detail($code))
              {
                $sc = FALSE;
                $this->error = "Failed to delete transfer rows";
              }
            }

            if($sc === TRUE)
            {
              //--- drop movement
              if( ! $this->movement_model->drop_movement($code))
              {
                $sc = FALSE;
                $this->error = "Failed to delete movement";
              }
            }

            if($sc === TRUE)
            {
              //--- change status to 2 (cancled)
              $arr = array(
                'status' => 2,
                'cancle_reason' => trim($this->input->post('reason')),
                'cancle_user' => $this->_user->uname
              );

              if( ! $this->transfer_model->update($code, $arr))
              {
                $sc = FALSE;
                $this->error = "Change status failed";
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
        } //--- if status != 2
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
      set_error('permission');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function print_transfer($code)
  {
    $this->load->library('printer');
    $doc = $this->transfer_model->get($code);
    if( ! empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);
    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        // $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        // $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_transfer', $ds);
  }


  public function clear_filter()
  {
    $filter = array(
      'tr_code',
      'wx_code',
      'tr_from_warehouse',
      'tr_user',
      'tr_to_warehouse',
      'tr_fromDate',
      'tr_toDate',
      'tr_status',
      'tr_is_approve',
      'tr_valid',
      'tr_sap',
      'tr_must_accept',
      'pallet_no'
    );

    clear_filter($filter);

    echo 'done';
  }


} //--- end class
?>
