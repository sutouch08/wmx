<?php
class Wrx_ob_api
{
  private $token;
  private $api;
  protected $ci;
  public $error;
  public $logs_json = FALSE;
  public $test = FALSE;
  public $url = "https://9724922-sb1.restlets.api.netsuite.com/";
  public $company = "WARRIX SPORT PUBLIC COMPANY LIMITED";

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/api/api_logs_model');

    $this->api = getWrxApiConfig();
    $this->logs_json = is_true($this->api['WRX_LOG_JSON']);
    $this->test = is_true($this->api['WRX_API_TEST']);
  }

  public function update_status($code)
  {
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('masters/sender_model');

    $action = "INT021";
    $type = "INT021";
    $url = $this->url; //$this->api['WRX_API_HOST'];
    $url .= getConfig('WRX_OB_URL'); //"app/site/hosting/restlet.nl?script=customscript_xcust_rl_party_warehouse&deploy=customdeploy_xcust_rl_party_warehouse";
    $api_path = $url;

    $headers = array(
      "Content-type:application:json",
      "Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}"
    );

    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';
    $order = $this->ci->orders_model->get($code);

    if( ! empty($order))
    {
      $playload = array(
        'company' => $this->company,
        'headerInternalId' => intval($order->oracle_id),
        'fulfillment' => $order->fulfillment_code,
        'status' => ($order->state > 7 ? 'Shipped' : ($order->state > 3 ? 'Packed' : 'Picked')),
        'shippingCarrier' => $this->ci->sender_model->get_code($order->id_sender),
        'trackingNo'=> $order->shipping_code,
        'updateBy' => 'WMS API',
        'lineItems' => []
      );

      $details = $this->ci->orders_model->get_order_details($code);

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $playload['lineItems'][] = array(
            'lineInternalId' => intval($rs->line_id),
            'item' => $rs->product_code,
            'qty' => intval($rs->qty),
            'unit' => $rs->unit_code
          );
        }
      }

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
              'code' => NULL,
              'action' => 'test',
              'status' => 'test',
              'message' => 'test',
              'request_json' => $json,
              'response_json' => NULL
            );

            $this->ci->api_logs_model->add_logs($logs);
          }

          return TRUE;
        }
        else
        {
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

          if( ! empty($res) && property_exists($res, 'status'))
          {
            if($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $type,
                'api_path' => $api_path,
                'code' => NULL,
                'action' => $action,
                'status' => $res->status == 'success' ? 'success' : 'failed',
                'message' => "",
                'request_json' => $json,
                'response_json' => $response
              );

              $this->ci->api_logs_model->add_logs($logs);
            }
          }
          else
          {
            $this->error = "No response";

            if($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $type,
                'api_path' => $api_path,
                'code' => NULL,
                'action' => $action,
                'status' => 'failed',
                'message' => 'No response',
                'request_json' => $json,
                'response_json' => NULL
              );

              $this->ci->api_logs_model->add_logs($logs);
            }

            return FALSE;
          }
        }
      }
    }
    else
    {
      $logs = array(
        'trans_id' => genUid(),
        'type' => $type,
        'api_path' => $api_path,
        'code' => $code,
        'action' => $action,
        'status' => 'failed',
        'message' => 'Order not found',
        'request_json' => NULL,
        'response_json' => NULL
      );

      $this->ci->api_logs_model->add_api_logs($logs);

      return FALSE;
    }

    return FALSE;
  }

} //-- end class

 ?>
