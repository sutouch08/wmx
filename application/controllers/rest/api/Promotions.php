<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Promotions extends REST_Controller
{
  public $error;
  public $user;
	public $api_path = "rest/api/promotions";
	public $logs;
	public $log_json = FALSE;
	public $api = FALSE;

  public function __construct()
  {
    parent::__construct();
		$this->api = is_true(getConfig('PROMOTION_API'));


		if( ! $this->api)
		{
			$arr = array(
				'status' => FALSE,
				'error' => "Access denied : Api is not enabled"
			);

			$this->response($arr, 400);
		}
    else
    {
      $this->user = 'api@warrix';
      $this->logs_json = is_true(getConfig('PROMOTION_API_LOG_JSON'));
    }
  }


  public function index_post()
  {
    $this->api_path = $this->api_path;
    $action = "GET";
    $json = file_get_contents("php://input");

    $data = json_decode($json);

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );

      $this->response($arr, 400);
    }

    if( ! $this->verify_data($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      $this->response($arr, 400);
    }

    if( ! empty($data->items))
    {
      foreach($)
    }

    $ds = $data;

    // $ds = array(
    //   'refId' => "xxx",
    //   'maxDiscountPercent' => 20.00,
    //   'discountGPpercent' => 20.00,
    //   'items' => array(
    //     array(
    //       'productId' => "xxxx",
    //       'itemSKU' => "xxxx",
    //       'discount' => 200.00,
    //       'discountPercent' => 20.00,
    //       'discountLabel' => "10+30%",
    //       'discountGP' => 200.00,
    //       'discountGPpercent' => 20.00,
    //       'conditionCode' => '1234',
    //       'conditionName' => 'xxxx',
    //       'freeQty' => 10,
    //       'freeItems' => array(
    //         array(
    //           'freeSKU' => 'WA-xxx-xxx'
    //         ),
    //         array(
    //           'freeSKU' => 'WA-xxx-xxy'
    //         )
    //       ),
    //       'promotion' => array(
    //         array(
    //           'name' => 'xxx1',
    //           'code' => '1234',
    //           'conditionName' => 'xxxx',
    //           'conditionCode' => '1234',
    //           'conditionPriority' => '1'
    //         ),
    //         array(
    //           'name' => 'xxx2',
    //           'code' => '1235',
    //           'conditionName' => 'xxxx2',
    //           'conditionCode' => '4567',
    //           'conditionPriority' => '10'
    //         )
    //       )
    //     ),
    //     array(
    //       'productId' => "xxxx",
    //       'itemSKU' => "xxxx",
    //       'discount' => 200.00,
    //       'discountPercent' => 20.00,
    //       'discountLabel' => "10+30%",
    //       'discountGP' => 200.00,
    //       'discountGPpercent' => 20.00,
    //       'conditionCode' => '1234',
    //       'conditionName' => 'xxxx',
    //       'freeQty' => 10,
    //       'freeItems' => array(
    //         array(
    //           'freeSKU' => 'WA-xxx-xxx'
    //         ),
    //         array(
    //           'freeSKU' => 'WA-xxx-xxy'
    //         )
    //       ),
    //       'promotion' => array(
    //         array(
    //           'name' => 'xxx1',
    //           'code' => '1234',
    //           'conditionName' => 'xxxx',
    //           'conditionCode' => '1234',
    //           'conditionPriority' => '1'
    //         ),
    //         array(
    //           'name' => 'xxx2',
    //           'code' => '1235',
    //           'conditionName' => 'xxxx2',
    //           'conditionCode' => '4567',
    //           'conditionPriority' => '10'
    //         )
    //       )
    //     )
    //   )
    // );

    $arr = array(
      'status' => TRUE,
      'message' => 'success',
      'data' => $ds
    );

    $this->response($arr, 200);
  }


  public function verify_data($data)
	{
    if(! property_exists($data, 'customerCode') OR $data->customerCode == '')
    {
      $this->error = 'Missing required parameter : customerCode';
			return FALSE;
    }

    if(! property_exists($data, 'salesChannel') OR $data->salesChannel == '')
    {
      $this->error = 'Missing required parameter : salesChannel';
			return FALSE;
    }

    if(! property_exists($data, 'paymentChannel') OR $data->paymentChannel == '')
    {
      $this->error = 'Missing required parameter : paymentChannel';
			return FALSE;
    }

    if(! property_exists($data, 'requestDate') OR $data->requestDate == '')
    {
      $this->error = 'Missing required parameter : requestDate';
			return FALSE;
    }

    if(! property_exists($data, 'items') OR empty($data->items))
    {
      $this->error = 'Missing required parameter : items';
			return FALSE;
    }

		return TRUE;
	}

  public function testConnection_get()
  {
    $arr = array(
      'status' => true,
      'message' => "Connected"
    );

    $this->response($arr, 200);
  }
}

 ?>
