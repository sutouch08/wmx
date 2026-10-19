<?php
class Ns_transfer_api
{  
  private $api;
  protected $ci;
  public $error;
  public $ex = 1; //-- export success = 1, falied = 0
  public $logs_json = FALSE;
  public $test = FALSE;
  public $url;
  public $company = "WARRIX SPORT PUBLIC COMPANY LIMITED";

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/api/api_logs_model');
    $this->ci->load->library('netsuite_client');

    $this->api = getNetsuiteApiConfig();
    $this->logs_json = is_true($this->api['NS_LOG_JSON']);
    $this->test = is_true($this->api['NS_API_TEST']);
    $this->company = $this->api['NS_COMPANY_NAME'];    
  }

  public function export_transfer($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/transfer_model');

    $action = "create";
    $type = "ADD40";    
    $scriptId = getConfig('NS_TR_URL');
    $api_path = $scriptId;        
    $method = 'POST';
    $doc = $this->ci->transfer_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
          'source' => "IX",
          'customForm' => "WRX Item Fulfillment",
          'referenceId' => $doc->code,
          'returnNumber' => $doc->code,
          'fulfillDate' => $doc->shipped_date,
          'tracking' => NULL,
          'status' => "Pending Approve",
          'shipToAttention' => "",
          'shipToAddressee' => "",
          'shipToAddress' => "",
          'shipToPhone' => "",
          'line' => []
        );

        $details = $this->ci->transfer_model->get_details($code);

        if( ! empty($details))
        {          
          foreach($details as $rs)
          {
            $playload['line'][] = array(
              'itemCode' => $rs->product_code,
              'location' => $rs->warehouse_code,
              'itemQty' => intval($rs->qty),
              'shipDate' => $doc->shipped_date
            );
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
            [$status, $res, $json] = $this->ci->netsuite_client->request($api_path, $method, $playload);
            $req_end = date('Y-m-d H:i:s');

            if($status !== 200)
            {
              $sc = FALSE;
              $this->error = ($res->errorCode ?? 'Error') . ' : ' . ($res->message ?? 'Unknown error');
            }

            if($sc === TRUE)
            {
              if(strtolower($res->status) === 'success')
              {
                $docNum = NULL;

                if( ! empty($res->responseBody->itemFulfillment))
                {
                  $docNum = trim($res->responseBody->itemFulfillment);
                }

                $arr = array(
                  'is_export' => 1,
                  'DocNum' => $docNum
                );

                if( ! $this->ci->transfer_model->update($code, $arr))
                {
                  $this->ex = 0;
                  $this->error = "Export data success but update document failed";
                }
              }
              else 
              {
                $sc = FALSE;
                $this->error = ($res->errorCode ?? 'Error') . ' : ' . ($res->message ?? 'Unknown error');
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
          $this->error = "No transfer items";
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

