<?php
class Ns_consignment_api
{    
  private $api = [];
  protected \CI_Controller $ci;
  public $error = "";
  public $logs_json = FALSE;
  public $test = FALSE;
  public $url = "";
  public $company = "Consignment";

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/api/api_logs_model');
    $this->ci->load->library('netsuite_client');

    $this->api = getNetsuiteApiConfig();
    $this->logs_json = is_true($this->api['NS_LOG_JSON']);
    $this->test = is_true($this->api['NS_API_TEST']);
    $this->company = $this->api['NS_CONSIGN_COMPANY'];
  }


  public function get_onhand_stock(string $code, string $warehouse_code)
  {
    $sc = TRUE;
    $qty = 0;
    $action = "get";
    $type = "ADD16";    
    $scriptId = getConfig('NS_GET_STOCK_URL'); //"ns/stock-sync";
    $api_path = $scriptId;       
    $method = 'POST';

    $playload = array(
      "company" => $this->company,
      "item" => $code,
      "productCode" => "",
      "segment" => "",
      "className" => "",
      "family" => "",
      "type" => "",
      "kind" => "",
      "gender" => "",
      "sportsType" => "",
      "clubCollection" => "",
      "brand" => "",
      "mainGroup" => "",
      "subGroup" => "",
      "mainColor" => "",
      "subColor" => "",
      "size" => "",
      "location" => $warehouse_code,
      "bin" => "",
      "limit" => 1,
      "offset" => 0
    );

    if( ! empty($playload))
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
            'action' => $action,
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

        if ($status !== 200 || ! ($res->success ?? false) || ($res->status ?? '') === 'Fail' || ($res->Status ?? '') === 'Fail' || ($res->Response ?? '') === 'Fail')
        {
          $sc = FALSE;
          $this->error = ($res->errorCode ?? 'Error') . ' : ' . ($res->message ?? 'Unknown error');          
        }       
        
        if($sc === TRUE)
        {
          if(property_exists($res, 'success') && property_exists($res, 'listData'))
          {
            if($res->success && ! empty($res->listData))
            {
              $ds = $res->listData[0];

              if(! empty($ds->listItems))
              {
                $qty = $ds->listItems[0]->onhandQty;
              }            
            }
            else
            {
              $sc = FALSE;
              $this->error = ! isset($res->errorCode) ? $res->errorCode.' : '.$res->message : 'Unknown error';
            }
          }
          else 
          {
            $sc = FALSE;
            $errCode = isset($res->errorCode) ? $res->errorCode : "";
            $this->error = isset($res->message) ? $errCode .' : '. $res->message : "Unknown error";
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

    return $sc === TRUE ? $qty : 0;
  }


  public function export_consignment($code)
  {
    $sc = TRUE;
    $this->ci->load->model('account/consignment_order_model');
    $this->ci->load->model('masters/warehouse_model');

    $action = "create";
    $type = "INT17.1";
    $scriptId = getConfig('NS_CONSIGNMENT_URL');
    $api_path = $scriptId;            
    $method = 'POST';
    $doc = $this->ci->consignment_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
          'subsidiary' => "Consignment",
          'source' => "IX",
          'saleChannel' => getConfig('NS_CONSIGNMENT_CHANNEL'),
          'date' => $doc->date_add,
          'documentId' => $doc->code,
          'memoMain' => $doc->remark,
          'line' => []
        );

        $details = $this->ci->consignment_order_model->get_details($code);

        if( ! empty($details))
        {
          $line = 1;

          foreach($details as $rs)
          {
            $playload['line'][] = array(
              "line" => $line,
              'itemNumber' => $rs->product_code,
              'location' => $doc->warehouse_code,
              'quantity' => round(floatval($rs->qty), 2) * -1,
              'memo' => "",
              'bin' => "",
              'refInvoice' => ""
            );

            $line++;
          }
        }
        
        if( ! empty($playload['line']))
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
                'action' => $action,
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

            if($status !== 200 || ! ($res->success ?? false) || ($res->status ?? '') === 'Fail' || ($res->Status ?? '') === 'Fail' || ($res->Response ?? '') === 'Fail')
            {
              $sc = FALSE;
              $this->error = ($res->errorCode ?? 'Error') . ' : ' . ($res->message ?? 'Unknown error');              
            }

            if($sc === TRUE)
            {
              if(property_exists($res, 'reference'))
              {
                $docNum = trim($res->reference);
                $arr = array(
                  'DocNum' => $docNum,
                  'is_exported' => 'Y'
                );

                if( ! $this->ci->consignment_order_model->update($code, $arr))
                {
                  $sc = FALSE;
                  $this->error = "Failed to update consignment order with reference";
                }
              }
              else 
              {
                $sc = FALSE;
                $this->error = "Reference not found in response";
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
