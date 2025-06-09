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
    $action = "GET Promotions";
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

    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('discount/discount_model');
    $this->load->library('promotion');
    $this->load->helper('discount');

    $cs = $this->customers_model->get($data->customerCode);

    if(empty($cs))
    {
      $arr = array(
        'status' => FALSE,
        'error' => "Invalid customerCode"
      );

      $this->response($arr, 400);
    }


    if( ! empty($data->items))
    {
      $c_item = [];
      $maxDisc = 0;

      foreach($data->items as $item)
      {
        $promoList = [];
        $promoApply = [];
        $pd = $this->products_model->get($item->itemSKU);

        if( ! empty($pd))
        {
          $disc = $this->promotion->getItemDiscount($pd, $cs, $item->sellingPrice, $item->qty, $data->paymentChannel, $data->salesChannel, $data->requestDate);

          // มูลค่าหลังส่วนลด ใช้เพือดูของแถม
          // ถ้าไม่พบส่วนลดก่อนหน้านี้ ใช้ราคาขายเลย
          $amount = empty($disc->rule_code) ? $item->sellingPrice * $item->qty : $disc->sellPrice * $item->qty;

          // ถ้ามีส่วนลดก่อนหน้า จะต้องดึงเฉพาะโปรของแถมที่ใช้ร่วมกับโปรอื่นได้เท่านั้น
          $canGroup = empty($disc->rule_code) ? 0 : 1;
          $freeList = $this->promotion->getFreeItemRule($pd, $cs, $amount, $item->qty, $data->paymentChannel, $data->salesChannel, $data->requestDate, $canGroup);
          // print_r($freeList);

          //---- update max disc
          if( ! empty($disc->rule_code))
          {
            $maxDisc =  $disc->totalDiscPrecent > $maxDisc ? $disc->totalDiscPrecent : $maxDisc;
            $promoApply[] = array(
              'code' => $disc->policy_code,
              'name' => $disc->policy_name,
              'conditionCode' => $disc->rule_code,
              'conditionName' => $disc->rule_name
            );

            if( ! empty($disc->all_rules))
            {
              foreach($disc->all_rules as $ro)
              {
                $promoList[] = $ro;
              }
            }
          }

          if( ! empty($freeList) && ! empty($freeList->all_rules))
          {
            $promoApply[] = array(
              'code' => $freeList->policy_code,
              'name' => $freeList->policy_name,
              'conditionCode' => $freeList->rule_code,
              'conditionName' => $freeList->rule_name
            );

            foreach($freeList->all_rules as $ro)
            {
              $promoList[] = $ro;
            }
          }

          $c_item[] = array(
            'productId' => $item->productId,
            'itemSKU' => $item->itemSKU,
            'discount' => $disc->totalDiscAmount, //--- ส่วนลดรวม Qty * item discount
            'discountPercent' => $disc->totalDiscPrecent,
            'discountLabel' => discountLabel($disc->disc1, $disc->disc2, $disc->disc3),
            'discountGP' => $disc->totalDiscAmount,
            'discountGPpercent' => $disc->totalDiscPrecent,
            'priceAfterDisc' => $disc->sellPrice, //-- ราคาขายต่อหน่วย
            'itemDiscAmount' => $disc->discAmount, //--- ส่วนลดรวมต่อ 1 รายการ
            'amountAfterDisc' => $item->qty * $disc->sellPrice,
            'conditionCode' => ! empty($disc->rule_code) ? $disc->rule_code : $freeList->rule_code,
            'conditionName' => ! empty($disc->rule_code) ? $disc->rule_name : $freeList->rule_name,
            'freeQty' => floatval($freeList->freeQty),
            'freeItems' => empty($freeList->id_rule) ? NULL : $this->promotion->getFreeItemList($freeList->id_rule),
            'promotionApply' => $promoApply,
            'promotions' => $promoList
          );
        }
        else
        {
          $c_item[] = array(
            'productId' => $item->productId,
            'itemSKU' => $item->itemSKU,
            'discount' => 0,
            'discountPercent' => 0,
            'discountLabel' => 0,
            'discountGP' => 0,
            'discountGPpercent' => 0,
            'conditionCode' => NULL,
            'conditionName' => NULL,
            'freeQty' => 0,
            'freeItems' => NULL,
            'promotionApply' => NULL,
            'promotions' => NULL
          );
        }
      }
    }

    $ds = array(
      'refId' => $data->refId,
      'maxDiscountPercent' => $maxDisc,
      'discountGPpercent' => $maxDisc,
      'items' => $c_item
    );

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
