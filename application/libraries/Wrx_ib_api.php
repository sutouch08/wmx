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
          'Company' => $this->company,
          'PoNumber' => $doc->po_code,
          'InvoiceNumber' =>  $doc->invoice_code,
          'SupplierNumber' => $doc->vender_code,
          'SupplierName' => $doc->vender_name,
          'ReceiptDate' => $doc->shipped_date,
          'Location' => $doc->warehouse_code,
          'LineItems' => []
        );

        $details = $this->ci->receive_po_model->get_details($code);

        if( ! empty($details))
        {
          foreach($details as $rs)
          {

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
