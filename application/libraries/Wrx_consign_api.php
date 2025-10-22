<?php
class Wrx_consign_api
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


  public function get_onhand_stock($code, $warehouse_code)
  {
    $sc = TRUE;
    $qty = 1000;
    $action = "check stock";
    $type = "INT03";
    $url = $this->api['WRX_API_HOST'];
    $url .= "ns/check-stock";
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
          if($res->status == 'success' && ! empty($res->data))
          {
            $ds = $res->data;

            if($ds->success && ! empty($ds->listItems))
            {
              $qty = $ds->listItems[0]->listLocations[0]->onhandQty;
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

    return $qty;
  }


  public function export_consign($code)
  {
    $sc = TRUE;
    $this->ci->load->model('account/consign_order_model');    

    $action = "create";
    $type = "INT12";
    $url = $this->api['WRX_API_HOST'];
    $url .= getConfig('WRX_CONSIGN_URL');
    $api_path = $url;
    $req_time = NULL;
    $headers = array(
      "Content-Type: application/json",
      "Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}"
    );

    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $doc = $this->ci->consign_order_model->get($code);
    $playload = [];

    if( ! empty($doc))
    {
      if($doc->status == 'C')
      {
        $playload = [
          "source" => "WMS",
          "company" => $this->company,
          "referenceId" => $doc->code,
          "customerNumber" => $doc->customer_code,
          "onetimeCustomerName" => "",
          "onetimeBranchCode" => "",
          "onetimeCustomerAddress" => "",
          "onetimeTel" => "",
          "onetimeEmail" => "",
          "onetimeTaxId" => "",
          "status" => "Approved",
          "refer1OrderNumber" => "",
          "refer2OrderNumber" => "",
          "billToCode" => "",
          "shipToCode" => "" ,
          "salesPerson" => "",
          "salesChannel" => getConfig('WRX_CONSIGN_CHANNEL'),
          "salesType" => "Collection",
          "orderDate" => db_date($doc->shipped_date, FALSE),
          "paymentOption" => "Credit",
          "currency" => "THB",
          "memoHeader" => $doc->remark,
          "items" => []
        ];

        $details = $this->ci->consign_order_model->get_details($code);

        if( ! empty($details))
        {
          $line = 1;
          $rate = getConfig('SALE_VAT_RATE');
          $rate = ($rate == "" OR $rate == NULL) ? 7.00 : $rate;

          foreach($details as $rs)
          {
            $playload['items'][] = array(
              "orderLine" => $line,
              "itemNumber" => $rs->product_code,
              "orderQty" => intval($rs->qty),
              "uom" => $rs->unit_code,
              "unitPriceBfVat" => floatval(round(remove_vat($rs->sell_price, $rate), 2)),
              "unitPriceIncVat" => floatval(round($rs->sell_price, 2)),
              "unitPriceTag" => floatval($rs->price),
              "discountUnitPrice" => floatval(round($rs->price - $rs->sell_price, 2)),
              "amountBfVat" => floatval(round(remove_vat($rs->amount, $rate), 2)),
              "taxAmount" => floatval(round(get_vat_amount($rs->amount, $rate), 2)),
              "grossAmount" => floatval(round($rs->amount, 2)),
              "priceTagAmount" => floatval(round($rs->price * $rs->qty, 2)),
              "discountLineAmt" => floatval(round($rs->discount_amount, 2)),
              "discountLinePct" => $rs->discount_amount > 0 ? floatval(round(($rs->discount_amount / ($rs->qty * $rs->price)), 2)) * 100 : 0,
              "refer1OrderNumber" => "no-referece",
              "refer2OrderNumber" => "no-reference",
              "deposit" => "No",
              "depositFundAmt" => 0,
              "depositAccount" => "",
              // "depositDate" => "",
              "depositMemo" => "",
              "depositPaymentOption" => "",
              "preOrder" => "No",
              "shipDate" => db_date($doc->shipped_date, FALSE),
              "lineLocation" => $doc->warehouse_code,
              "memoLine" => "",
              "lineAttachmentFile" => "",
              "employee" => ""
            );

            $line++;
          }
        }

        $json = json_encode($playload);
        $json = "[".$json."]";

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
                $ds = $res->data[0];

                if($ds->status == 'Success' OR $ds->status == 'success')
                {
                  $docNum = NULL;

                  if( ! empty($ds->receiptNumber))
                  {
                    $docNum = trim($ds->receiptNumber);
                  }

                  $arr = array(
                    'DocNum' => $docNum,
                    'is_exported' => 'Y'
                  );

                  if( ! $this->ci->consign_order_model->update($code, $arr))
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

                if($doc->is_exported != 'Y')
                {
                  $arr = array('is_exported' => 'E');
                  $this->ci->consign_order_model->update($code, $arr);
                }
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
