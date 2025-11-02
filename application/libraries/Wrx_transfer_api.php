<?php
class Wrx_transfer_api
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

  public function export_transfer($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/transfer_model');

    $action = "create";
    $type = "ADD40";
    $url = $this->api['WRX_API_HOST'];
    $url .= getConfig('WRX_TR_URL');
    $api_path = $url;
    $req_start = NULL;
    $headers = array(
      "Content-Type: application/json",
      "Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}"
    );

    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $doc = $this->ci->transfer_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
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
          $line = 1;

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
            $req_start = date('Y-m-d H:i:s');

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
            $req_end = date('Y-m-d H:i:s');

            if( ! empty($res) && property_exists($res, 'status') && property_exists($res, 'code'))
            {
              if($res->code == 200 && ! empty($res->data))
              {
                $ds = $res->data;

                if($ds->status == 'SUCCESS' OR $ds->status == 'success' OR $ds->status == 'Success')
                {
                  $docNum = NULL;

                  if( ! empty($ds->responseBody->itemFulfill))
                  {
                    $docNum = trim($ds->responseBody->itemFulfill);
                  }

                  $arr = array(
                    'is_export' => 1,
                    'DocNum' => $docNum
                  );

                  if( ! $this->ci->transfer_model->update($code, $arr))
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
                  'response_json' => $response,
                  'req_start' => $req_start,
                  'req_end' => $req_end
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
                'message' => $this->error
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
                  'response_json' => json_encode($resp),
                  'req_start' => $req_start,
                  'req_end' => $req_end
                );

                $this->ci->api_logs_model->add_logs($logs);
              }
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

 ?>
