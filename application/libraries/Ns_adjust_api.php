<?php
class Ns_adjust_api
{  
  private $api = [];
  protected \CI_Controller $ci;
  public $error = "";
  public $ex = 1; //-- export success = 1, falied = 0
  public $logs_json = FALSE;
  public $test = FALSE;
  public $url = "";
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

  public function export_adjust($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/adjust_model');
    $this->ci->load->model('masters/warehouse_model');
    $this->ci->load->model('masters/zone_model');

    $action = "create";
    $type = "INT17";    
    $scriptId = getConfig('NS_ADJUST_URL');
    $api_path = $scriptId;        
    $method = 'POST';
    $doc = $this->ci->adjust_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
          'source' => "IX",
          'customer' => "",
          'saleChannel' => getConfig('NS_ADJ_CHANNEL'),
          'adjustDate' => $doc->date_add,
          'documentId' => $doc->code,
          'memoMain' => $doc->remark,
          'line' => []
        );

        $details = $this->ci->adjust_model->get_details($code);

        if( ! empty($details))
        {
          $line = 1;
          foreach($details as $rs)
          {
            $playload['line'][] = array(
              "line" => $line,
              'itemNumber' => $rs->product_code,
              'location' => $rs->warehouse_code,
              'quantity' => floatval($rs->qty),
              'memo' => "",
              'binNumber' => "Store"
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

            if($status !== 200)
            {
              $sc = FALSE;
              $this->error = ($res->ErrorCode ?? 'Error') . ' : ' . ($res->Message ?? 'Unknown error');                         
            }

            if($sc === TRUE)
            {
              if(strtolower($res->Status) === 'success')
              {
                $docNum = trim($res->reference ?? NULL);

                $arr = array(
                  'DocNum' => $docNum
                );

                if( ! $this->ci->adjust_model->update($code, $arr))
                {
                  $this->ex = 0;
                  $this->error = "Export data success but update document failed";
                }
              }
              else 
              {
                $sc = FALSE;
                $this->error = ($res->ErrorCode ?? 'Error') . ' : ' . ($res->Message ?? 'Unknown error');
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
