<?php
class Wrx_ib_api
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

  public function export_receive($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/receive_po_model');
    $this->ci->load->model('masters/warehouse_model');
    $this->ci->load->model('masters/zone_model');

    $action = "create";
    $type = "ADD24";
    $url = $this->api['WRX_API_HOST'];
    $url .= getConfig('WRX_IB_URL');
    $api_path = $url;
    $req_time = NULL;
    $headers = array(
      "Content-Type: application/json",
      "Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}"
    );

    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $doc = $this->ci->receive_po_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
          'source' => 'NONE',
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
              'bin' => "store" //$doc->zone_code
            );
          }
        }

        if( ! empty($playload['items']))
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

            if( ! empty($res) && property_exists($res, 'status') && property_exists($res, 'data'))
            {
              if($res->status == 'success' && ! empty($res->data))
              {
                $ds = $res->data;

                if($ds->status == 'Success' OR $ds->status == 'success')
                {
                  $res->serviceMessage = $ds->message;
                  $message = explode(':', $ds->message);
                  $docNum = NULL;

                  if( ! empty($message[1]))
                  {
                    $docNum = trim($message[1]);
                  }

                  $arr = array(
                    'oracle_id' => $ds->internalId,
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


  public function export_return($code)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/return_order_model');
    $this->ci->load->model('masters/warehouse_model');
    $this->ci->load->model('masters/zone_model');

    $action = "create";
    $type = "ADD91";
    $url = $this->api['WRX_API_HOST'];
    $url .= getConfig('WRX_RETURN_URL');
    $api_path = $url;
    $req_time = NULL;
    $headers = array(
      "Content-Type: application/json",
      "Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}"
    );

    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $doc = $this->ci->return_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = array(
          'company' => $this->company,
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

            if( ! empty($res) && property_exists($res, 'status') && property_exists($res, 'data'))
            {
              if($res->status == 'success' && ! empty($res->data))
              {
                $ds = $res->data;

                if($ds->status == 'Success' OR $ds->status == 'success')
                {
                  $arr = array(
                    'oracle_id' => $ds->itemReceiptId,
                    'DocNum' => $ds->itemReceiptNumber
                  );

                  if( ! $this->ci->return_order_model->update($code, $arr))
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
