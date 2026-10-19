<?php
class Ns_ob_api
{  
  private $api = [];
  protected \CI_Controller $ci;
  public $error = "";
  public $logs_json = FALSE;
  public $test = FALSE;
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

  public function update_status(string $code)
  {
    $sc = TRUE;
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/delivery_order_model');
    $this->ci->load->model('masters/sender_model');

    $action = "update";
    $type = "INT21";
    $scriptId = getConfig('NS_OB_URL');
    $api_path = $scriptId;
    $method = 'POST';
    $order = $this->ci->orders_model->get($code);

    if( ! empty($order))
    {
      $stateMap = [
        '1' => 'Picked',
        '2' => 'Picked',
        '3' => 'Picked',
        '4' => 'Packed',
        '5' => 'Packed',
        '6' => 'Packed',
        '7' => 'Packed',
        '8' => 'Shipped',
        '9' => 'Canceled'
      ];

      $sender = $this->ci->sender_model->get_code($order->id_sender);

      $playload = array(
        'Company' => $this->company,
        'source' => "IX",
        'HeaderInternalId' => intval($order->oracle_id),
        'Fulfillment' => $order->fulfillment_code,
        'Status' => $stateMap[$order->state],
        'ShippingMethod' => empty($sender) ? "" :  $sender,
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
              'request_json' => json_encode($playload),
              'response_json' => NULL
            );

            $this->ci->api_logs_model->add_logs($logs);
          }
        }
        else
        {
          $req_start = date('Y-m-d H:i:s');
          [$status, $res, $json] = $this->ci->netsuite_client->callRestlet($api_path, $playload, $method);
          $req_end = date('Y-m-d H:i:s');

          if ($status !== 200)
          {
            $sc = FALSE;
            $this->error = (isset($res->ErrorCode) ? $res->ErrorCode : 'Error') . ' : ' . (isset($res->Message) ? $res->Message : 'Unknown error');
          }

          if($sc === TRUE)
          {
            if($res->success)
            {
              $arr = array('erp_status' => $stateMap[$order->state]);
              $this->ci->orders_model->update($code, $arr);
            }
            else 
            {
              $sc = FALSE;
              $this->error = isset($res->Message) ? $res->Message : (isset($res->message) ? $res->message : 'Unknown error');
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
        $this->error = "Invalid playload";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Order not found !";
    }

    return $sc;
  }

} //-- end class

