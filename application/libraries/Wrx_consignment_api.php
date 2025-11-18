<?php
class Wrx_consignment_api
{
  private $token;
  private $api;
  protected $ci;
  public $error;
  public $logs_json = FALSE;
  public $test = FALSE;
  public $url;
  public $company = "Consignment";

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/api/api_logs_model');

    $this->api = getWrxApiConfig();
    $this->logs_json = is_true($this->api['WRX_LOG_JSON']);
    $this->test = is_true($this->api['WRX_API_TEST']);
  }


  public function get_onhand_stock($code, $warehouse_code)
  {
    $sc = TRUE;
    $qty = 1000;
    $action = "check stock";
    $type = "ADD16";
    $url = $this->api['WRX_API_HOST'];
    $url .= "ns/stock-sync";
    $api_path = $url;
    $req_time = NULL;

    $headers = array(
      "Content-Type: application/json",
      "Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}"
    );

    $apiUrl = str_replace(" ","%20",$url);

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
      $json = json_encode($playload);

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

        if( ! empty($res) && property_exists($res, 'status') && property_exists($res, 'data'))
        {
          if($res->code == 200 && $res->status == 'success' && ! empty($res->data))
          {
            $ds = $res->data;

            if($ds->success && ! empty($ds->listItems))
            {
              $qty = $ds->listItems[0]->listLocations[0]->onhandQty;
            }
            else
            {
              $qty = 0;
              $this->error = empty($ds->message) ? $res->serviceMessage : $ds->message;
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
              'message' => $this->error,
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

    return $sc === TRUE ? $qty : 0;
  }


  public function export_consignment($code)
  {
    $sc = TRUE;
    $this->ci->load->model('account/consignment_order_model');
    $this->ci->load->model('masters/warehouse_model');

    $action = "create";
    $type = "INT17.1";
    $url = $this->api['WRX_API_HOST'];
    $url .= getConfig('WRX_CONSIGNMENT_URL');
    $api_path = $url;
    $req_time = NULL;
    $headers = array(
      "Content-Type: application/json",
      "Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}"
    );

    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $doc = $this->ci->consignment_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
          'subsidiary' => "Consignment",
          // 'customer' => $doc->customer_code,
          'saleChannel' => getConfig('WRX_CONSIGNMENT_CHANNEL'),
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

                if($ds->Status == 'Success' OR $ds->Status == 'success')
                {
                  $docNum = NULL;

                  if( ! empty($ds->reference))
                  {
                    $docNum = trim($ds->reference);
                  }

                  $arr = array(
                    'DocNum' => $docNum,
                    'is_exported' => 'Y'
                  );

                  if( ! $this->ci->consignment_order_model->update($code, $arr))
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
