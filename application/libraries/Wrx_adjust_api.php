<?php
class Wrx_adjust_api
{
  private $token;
  private $api;
  protected $ci;
  public $error;
  public $logs_json = FALSE;
  public $test = FALSE;
  public $url;
  public $company = "WARRIX SPORT PUBLIC COMPANY LIMITED";

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/api/api_logs_model');

    $this->api = getWrxApiConfig();
    $this->logs_json = is_true($this->api['WRX_LOG_JSON']);
    $this->test = is_true($this->api['WRX_API_TEST']);
  }

  public function export_adjust($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/adjust_model');
    $this->ci->load->model('masters/warehouse_model');
    $this->ci->load->model('masters/zone_model');

    $action = "create";
    $type = "INT17";
    $url = $this->api['WRX_API_HOST'];
    $url .= getConfig('WRX_ADJ_URL');
    $api_path = $url;
    $req_time = NULL;
    $headers = array(
      "Content-Type: application/json",
      "Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}"
    );

    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $doc = $this->ci->adjust_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
          'customer' => "",
          'saleChannel' => getConfig('WRX_ADJ_CHANNEL'),
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
              'binNumber' => ""
            );

            $line++;
          }
        }

        $json = json_encode($playload);

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
                'request_json' => $json,
                'response_json' => NULL
              );

              $this->ci->api_logs_model->add_logs($logs);
            }
          }
          else
          {
            $req_time = date('Y-m-d H:i:s');

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response);

            if( ! empty($res) && property_exists($res, 'status') && property_exists($res, 'code'))
            {
              if($res->code == 200 && ! empty($res->data))
              {
                $ds = $res->data;

                if($ds->Status == 'Success' OR $ds->Status == 'success')
                {
                  $docNum = NULL;

                  if( ! empty($ds->reference))
                  {
                    $docNum = trim($ds->reference);
                  }

                  $arr = array(
                    'DocNum' => $docNum
                  );

                  if( ! $this->ci->adjust_model->update($code, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Export data success but update document failed";
                  }
                }
                else
                {
                  if( empty($ds->data))
                  {
                    $sc = FALSE;
                    $this->error = "Response data is empty";
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = $res->serviceMessage;
              }

              if($this->logs_json)
              {
                $logs = array(
                  'trans_id' => genUid(),
                  'type' => $type,
                  'api_path' => $api_path,
                  'code' => $code,
                  'action' => $action,
                  'status' => $sc === TRUE ? 'success' : 'failed',
                  'message' => $res->serviceMessage,
                  'request_json' => $json,
                  'response_json' => $response
                );

                $this->ci->api_logs_model->add_logs($logs);
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "No response from ERP";
              $resp = array(
                'status' => 'failed',
                'message' => $this->error,
                'request_start' => $req_time,
                'request_end' => date('Y-m-d H:i:s')
              );

              if($this->logs_json)
              {
                $logs = array(
                  'trans_id' => genUid(),
                  'type' => $type,
                  'api_path' => $api_path,
                  'code' => $code,
                  'action' => $action,
                  'status' => 'failed',
                  'message' => 'No response',
                  'request_json' => $json,
                  'response_json' => json_encode($resp)
                );

                $this->ci->api_logs_model->add_logs($logs);
              }
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
