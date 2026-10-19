<?php
class Ns_ib_api
{  
  private $api = [];
  protected \CI_Controller $ci;
  public $error = "";
  public $logs_json = FALSE;
  public $test = FALSE;  
  public $company = "WARRIX SPORT PUBLIC COMPANY LIMITED";

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/api/api_logs_model');
    $this->ci->load->library('netsuite_client');

    $this->api = getNetsuiteApiConfig();
    $this->logs_json = is_true($this->api['NS_LOG_JSON']);
    $this->test = is_true($this->api['NS_API_TEST']);
    $this->company = $this->api['NS_MAIN_COMPANY'];   
  }

  public function export_receive(string $code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/receive_product_model');
    $this->ci->load->model('masters/warehouse_model');
    $this->ci->load->model('masters/zone_model');

    $action = "create";
    $type = "INT16";    
    $scriptId = getConfig('NS_GR_URL');
    $api_path = $scriptId;        
    $method = 'POST';

    $doc = $this->ci->receive_product_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
          'source' => "IX",
          'referenceId' => $doc->code,
          'transferOrder' => $doc->reference,
          'fulfillmentNo' => $doc->fulfillment_code,
          'date' => $doc->shipped_date,
          'fromLocation' => $doc->from_warehouse,
          'toLocation' => $doc->warehouse_code,
          'source' => "WMS",
          'department' => "",
          'memoHeader' => "",
          'items' => []
        );

        $details = $this->ci->receive_product_model->get_details($code);

        if( ! empty($details))
        {
          foreach($details as $rs)
          {
            $playload['items'][] = array(
              'lineNumber' => intval($rs->line_num),
              'itemNumber' => $rs->product_code,
              'qty' => floatval($rs->receive_qty)
            );
          }
        }

        if( ! empty($playload['items']))
        {          
          if($this->test)
          {
            if($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $type,
                'api_path' => $api_path,
                'code' => $code,
                'action' => 'test',
                'status' => 'test',
                'message' => 'test',
                'request_json' => json_encode($playload),
                'response_json' => NULL
              );

              $this->ci->api_logs_model->add_logs($logs);
            }
          }
          else
          {
            $req_start = date('Y-m-d H:i:s');
            [$status, $res, $json] = $this->ci->netsuite_client->callRestlet($scriptId, $playload, $method);
            $req_end = date('Y-m-d H:i:s');         
            
            if($status !== 200)
            {
              $sc = FALSE;
              $this->error = (isset($res->ErrorCode) ? $res->ErrorCode : (isset($res->errorCode) ? $res->errorCode : 'Error')) . ' : ' . (isset($res->Message) ? $res->Message : (isset($res->message) ? $res->message : 'Unknown error')); 
            }

            if($sc === TRUE)
            {
              $reStatus = isset($res->status) ? $res->status : (isset($res->Status) ? $res->Status : 'fail');

              if(strtolower($reStatus) === 'success')
              {
                $arr = array(
                  'oracle_id' => $res->ReferenceId,
                  'DocNum' => $res->itemReceiptNumber
                );

                if( ! $this->ci->receive_product_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Export data success but update document failed";
                }
              }
              else 
              {
                $sc = FALSE;
                $this->error = (isset($res->ErrorCode) ? $res->ErrorCode : (isset($res->errorCode) ? $res->errorCode : 'Error')) . ' : ' . (isset($res->Message) ? $res->Message : (isset($res->message) ? $res->message : 'Unknown error'));
              }
            }

            if ($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $type,
                'api_path' => $api_path,
                'code' => $code,
                'action' => $action,
                'status' => $sc === TRUE ? 'success' : 'failed',
                'message' => $this->error,
                'request_json' => json_encode($playload),
                'response_json' => $json,
                'req_start' => $req_start,
                'req_end' => $req_end
              );

              $this->ci->api_logs_model->add_logs($logs);
            }            
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No receipt items";
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

    return $sc;
  }


  public function export_receive_po(string $code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/receive_po_model');
    $this->ci->load->model('masters/warehouse_model');
    $this->ci->load->model('masters/zone_model');

    $action = "create";
    $type = "ADD24";    
    $scriptId = getConfig('NS_GRPO_URL');
    $api_path = $scriptId;
    $method = 'POST';
    $doc = $this->ci->receive_po_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
          'source' => 'IX',
          'poNumber' => $doc->reference,
          'receiptDate' => $doc->shipped_date,
          'items' => []
        );

        $details = $this->ci->receive_po_model->get_details($code);

        if( ! empty($details))
        {
          foreach($details as $rs)
          {
            $playload['items'][] = array(
              'orderLine' => intval($rs->po_line_num),
              'itemNumber' => $rs->product_code,
              'receiveQty' => floatval($rs->receive_qty),
              'lineLocation' => $doc->warehouse_code,
              'bin' => "Store" //$doc->zone_code
            );
          }
        }

        if( ! empty($playload['items']))
        {          
          if($this->test)
          {
            if($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $type,
                'api_path' => $api_path,
                'code' => $code,
                'action' => 'test',
                'status' => 'test',
                'message' => 'test',
                'request_json' => json_encode($playload),
                'response_json' => NULL
              );

              $this->ci->api_logs_model->add_logs($logs);
            }
          }
          else
          {
            $req_start = date('Y-m-d H:i:s');
            [$status, $res, $json] = $this->ci->netsuite_client->callRestlet($scriptId, $playload, $method);
            $req_end = date('Y-m-d H:i:s');                    

            if($status !== 200)
            {
              $sc = FALSE;
              $this->error = ($res->errorCode ?? 'Error') .' : '. ($res->message ?? 'Unknown error');
            }

            if($sc === TRUE)
            {
              if(strtolower($res->status) === 'success')
              {
                $message = explode(':', ($res->message ?? ''));
                $docNum = NULL;
                if( ! empty($message[1]))
                {
                  $docNum = trim($message[1]);
                }

                $arr = array(
                  'oracle_id' => $res->internalId ?? NULL,
                  'DocNum' => $docNum
                );

                if( ! $this->ci->receive_po_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Export data success but update document failed";
                }                
              }
              else 
              {
                $sc = FALSE;
                $this->error = "Export data failed: ". ($res->message ?? 'Unknown error');
              }
            }

            if ($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $type,
                'api_path' => $api_path,
                'code' => $code,
                'action' => $action,
                'status' => $sc === TRUE ? 'success' : 'failed',
                'message' => $this->error,
                'request_json' => json_encode($playload),
                'response_json' => $json,
                'req_start' => $req_start,
                'req_end' => $req_end
              );

              $this->ci->api_logs_model->add_logs($logs);
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No receipt items";
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

    return $sc;
  }


  public function export_return(string $code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/return_order_model');
    $this->ci->load->model('masters/warehouse_model');
    $this->ci->load->model('masters/zone_model');

    $action = "create";
    $type = "ADD91";
    $scriptId = getConfig('NS_RETURN_URL');
    $api_path = $scriptId;    
    $method = 'POST';
    $doc = $this->ci->return_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
          'source' => "IX",
          'referenceId' => $doc->referenceId,
          'returnAuthNumber' => $doc->reference,
          'tranDate' => $doc->shipped_date,
          'memoHeader' => "",
          'items' => []
        );

        $details = $this->ci->return_order_model->get_details($code);

        if( ! empty($details))
        {
          foreach($details as $rs)
          {
            $playload['items'][] = array(
              'orderLine' => intval($rs->line_num),
              'itemNumber' => $rs->product_code,
              'returnQty' => floatval($rs->receive_qty),
              'location' => $doc->warehouse_code,
              'uom' => $rs->unit_code
            );
          }
        }

        if( ! empty($playload['items']))
        {          
          if($this->test)
          {
            if($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $type,
                'api_path' => $api_path,
                'code' => $code,
                'action' => 'test',
                'status' => 'test',
                'message' => 'test',
                'request_json' => json_encode($playload),
                'response_json' => NULL
              );

              $this->ci->api_logs_model->add_logs($logs);
            }
          }
          else
          {
            $req_start = date('Y-m-d H:i:s');
            [$status, $res, $json] = $this->ci->netsuite_client->callRestlet($scriptId, $playload, $method); 
            $req_end = date('Y-m-d H:i:s');                    

            if($status !== 200)
            {
              $sc = FALSE;
              $this->error = ($res->ErrorCode ?? 'Error') .' : ' . ($res->Message ?? 'Unknown error');
            }

            if($sc === TRUE)
            {
              if(strtolower($res->status) === 'success')
              {
                $arr = array(
                  'oracle_id' => $res->itemReceiptId ?? NULL,
                  'DocNum' => $res->itemReceiptNumber ?? NULL
                );

                if( ! $this->ci->return_order_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Export data success but update document failed";
                }
              }
              else 
              {
                $sc = FALSE;
                $this->error = "Export failed: " . ($res->message ?? 'Unknown error');
              }
            }

            if ($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $type,
                'api_path' => $api_path,
                'code' => $code,
                'action' => $action,
                'status' => $sc === TRUE ? 'success' : 'failed',
                'message' => $this->error,
                'request_json' => json_encode($playload),
                'response_json' => $json,
                'req_start' => $req_start,
                'req_end' => $req_end
              );

              $this->ci->api_logs_model->add_logs($logs);
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No receipt items";
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

    return $sc;
  }

} //-- end class

 ?>
