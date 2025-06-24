<?php
class Wrx_ob_api
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

  public function update_status($code)
  {
    $sc = TRUE;
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/delivery_order_model');
    $this->ci->load->model('masters/sender_model');

    $action = "update";
    $type = "INT21";
    $url = $this->api['WRX_API_HOST'];
    $url .= getConfig('WRX_OB_URL');
    $api_path = $url;

    $headers = array(
      "Content-Type: application/json",
      "Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}"
    );

    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';
    $order = $this->ci->orders_model->get($code);

    if( ! empty($order))
    {
      $sender = $this->ci->sender_model->get_code($order->id_sender);
      $playload = array(
        'Company' => $this->company,
        'HeaderInternalId' => intval($order->oracle_id),
        'Fulfillment' => $order->fulfillment_code,
        'Status' => ($order->state == 8 ? 'Shipped' : ($order->state > 3 ? 'Packed' : 'Picked')),
        'ShippingMethod' => empty($sender) ? "" :  $sender, //$this->ci->sender_model->get_code($order->id_sender),
        'TrackingNo'=> empty($order->shipping_code) ? "" : $order->shipping_code,
        'UpdateBy' => 'WMS API',
        'LineItems' => []
      );

      $details = $order->state == 8 ? $this->ci->delivery_order_model->get_sold_details($code) : $this->ci->orders_model->get_order_details($code);

      if( ! empty($details))
      {
        foreach($details as $rs)
        {
          $playload['LineItems'][] = array(
            'LineInternalId' => intval($rs->line_id),
            'Item' => $rs->product_code,
            'Qty' => intval($rs->qty)
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
            if($res->status !== 'success')
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
                'response_json' => NULL
              );

              $this->ci->api_logs_model->add_logs($logs);
            }
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid playload";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Order not found !";

      $logs = array(
        'trans_id' => genUid(),
        'type' => $type,
        'api_path' => $api_path,
        'code' => $code,
        'action' => $action,
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => NULL,
        'response_json' => NULL
      );

      $this->ci->api_logs_model->add_api_logs($logs);
    }

    return $sc;
  }

} //-- end class

 ?>
