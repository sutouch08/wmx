<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Promotion
{
  protected $ci;

	public function __construct()
	{
    // Assign the CodeIgniter super-object
    $this->ci =& get_instance();
    $this->ci->load->model('discount/discount_model');
	}


  public function getItemDiscount(object $item, object $customer, $price, $qty, $payment_code, $channels_code, $date)
  {
    //--- default value if dont have any discount
    $sc = array(
      'sellPrice' => $price, //--- ราคา หลังส่วนลด
      'type' => 'P',
      'discAmount1' => 0,
      'disc1' => 0,
      'discAmount2' => 0,
      'disc2' => 0,
      'discAmount3' => 0,
      'disc3' => 0,
      'discAmount'=> 0,
      'totalDiscAmount' => 0,
      'totalDiscPrecent' => 0,
      'freeQty' => 0,
      'id_rule' => NULL,
      'rule_code' => NULL,
      'rule_name' => NULL,
      'priority' => 0,
      'policy_id' => NULL,
      'policy_code' => NULL,
      'policy_name' => NULL
    ); //-- end array

    $available_policy = $this->ci->discount_model->get_available_policy($date);

    if( ! empty($available_policy))
    {
      $rules = $this->ci->discount_model->get_rule_list($item, $customer, $price, $qty, $payment_code, $channels_code, $date, $available_policy);

      if( ! empty($rules))
      {
        $priority = 1;

        $type = 'P';

        $discAmount1 = 0;
        $discLabel1 = 0;

        $discAmount2 = 0;
        $discLabel2 = 0;

        $discAmount3 = 0;
        $discLabel3 = 0;

        $totalDiscAmount = 0; //--- ที่พัก มูลค่าส่วนลดที่มากที่สุด

        $freeQty = 0;

        $dis_rule = NULL; //---  ที่พัก rule id ที่ดีที่สุด
        $dis_code = NULL;
        $dis_name = NULL;
        $policy_code = NULL;
        $policy_name = NULL;
        $all_rules = []; //--- เก็บข้อมูลชื่อและรหัส เงื่อนไขที่ดึงได้ทั้งหมด

        $dis_policy = NULL;

        //---- วนรอบจนหมดเงื่อนไข
        //--- หากเงื่อนไขถัดไปได้ส่วนลดรวมมากกว่าเงื่อนไขก่อนหน้า ตัวแปรด้านบนจะถูกแทนค่าใหม่ ถ้าไม่ดีกว่าจะได้ค่าเดิม
        foreach($rules as $rs)
        {
          //---- เก็บข้อมูลรายชื่อโปรโมชั่น
          $all_rules[] = (object) array(
            'code' => $rs->policy_code,
            'name' => $rs->policy_name,
            'conditionCode' => $rs->code,
            'conditionName' => $rs->name,
            'conditionPriority' => $rs->priority
          );

          if($rs->priority >= $priority)
          {
            $discount1 = 0;
            $discount2 = 0;
            $discount3 = 0;
            $amount = $qty * $price;
            $isSetMin = ($rs->minQty > 0 OR $rs->minAmount > 0) ? TRUE : FALSE; //--- มีการกำหนดขั้นต่ำหรือไม่


            //---- ถ้ามีการกำหนดราคาขาย
            if( $rs->type == 'N' )
            {
              //--- step 1
              //--- ถ้ามีการกำหนดราคาขาย จะไม่สนใจส่วนลด ส่วนต่างราคาขาย จะถูกแปลงเป็นส่วนลดแทน
              $discount1 =	$price - $rs->price;
              $rs->disc1 = discountAmountToPercent($discount1, 1, $price);
            } //--- end if

            if($rs->type == 'D')
            {
              //--- ส่วนลดเสต็ป (เป็นจำนวนเงิน)
              $test_price = $price;

              $discount1 = $test_price * ( $rs->disc1 * 0.01 );
              $test_price -= $discount1;

              $discount2 = $test_price * ( $rs->disc2 * 0.01 );
              $test_price -= $discount2;

              $discount3 = $test_price * ( $rs->disc3 * 0.01 );
              $test_price -= $discount3;
            }	//-- end if

            //--- ส่วนลดรวมทั้ง 3 เสต็ป เป็นจำนวนเงิน
            $sumDiscount  = $discount1 + $discount2 + $discount3;

            $discLabel1 	= ( $sumDiscount > $totalDiscAmount ) ? $rs->disc1 : $discLabel1;
            $discAmount1  = ( $sumDiscount > $totalDiscAmount ) ? $discount1 : $discAmount1;

            $discLabel2		= ( $sumDiscount > $totalDiscAmount ) ? $rs->disc2 : $discLabel2;
            $discAmount2 	= ( $sumDiscount > $totalDiscAmount ) ? $discount2 : $discAmount2;

            $discLabel3		= ( $sumDiscount > $totalDiscAmount ) ? $rs->disc3 : $discLabel3;
            $discAmount3 	= ( $sumDiscount > $totalDiscAmount ) ? $discount3 : $discAmount3;

            //--- ถ้าส่วนลดรวมดีกว่าก่อนหน้านี้ เปลี่ยนมาใช้เงื่อนไขนี้แทน
            $dis_rule = ($sumDiscount >= $totalDiscAmount) ? $rs->id : $dis_rule;
            $dis_code = ($sumDiscount >= $totalDiscAmount) ? $rs->code : $dis_code;
            $dis_name = ($sumDiscount >= $totalDiscAmount) ? $rs->name : $dis_name;
            $dis_policy = ($sumDiscount >= $totalDiscAmount) ? $rs->id_policy : $dis_policy;
            $policy_code = ($sumDiscount >= $totalDiscAmount) ? $rs->policy_code : $policy_code;
            $policy_name = ($sumDiscount >= $totalDiscAmount) ? $rs->policy_name : $policy_name;
            $type = ($sumDiscount >= $totalDiscAmount) ? $rs->type : $type;

            //---- update  ลำดับความสำคัญ
            $priority = $rs->priority >= $priority ? $rs->priority : $priority;

            $freeQty = $rs->freeQty >= $freeQty ? $rs->freeQty : $freeQty;

            //---  ถ้าส่วนลดรวมของเงิ่อนไขนี้ ดีกว่าเงื่อนไขก่อนหน้านี้ ให้ใช้ค่าใหม่ ถ้าไม่ดีกว่าให้ใช้ค่าเดิม
            $totalDiscAmount = ($sumDiscount >= $totalDiscAmount) ? $sumDiscount : $totalDiscAmount;
          }
        }
        //--- end foreach

        //---- ได้ส่วนลดที่ดีที่สุดมาแล้ว
        $sc = array(
          'sellPrice' => round($price - $totalDiscAmount, 2), //--- ราคา หลังส่วนลด
          'type' => $type,
          'disAmount1' => round($discAmount1, 2), //--- ส่วนลดเป็นจำนวนเงิน (ยอดต่อหน่วย)
          'disc1' => $discLabel1, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
          'disAmount2' => round($discAmount2, 2),
          'disc2' => $discLabel2, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
          'disAmount3' => round($discAmount3, 2),
          'disc3' => $discLabel3, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
          'discAmount' => round($totalDiscAmount, 2), //--- ส่วนลด รวม 5 สเต็ปเป็นจำนวนเงิน/ 1 รายการ
          'totalDiscAmount' => round($totalDiscAmount * $qty, 2), //--- เอายอดส่วนลดที่ได้ มา คูณ ด้วย จำนวนสั่ง เป้นส่วนลดทั้งหมด
          'totalDiscPrecent' => round(discountAmountToPercent($totalDiscAmount, 1, $price), 2),
          'id_rule' => $dis_rule,
          'rule_code' => $dis_code,
          'rule_name' => $dis_name,
          'policy_id' => $dis_policy,
          'policy_code' => $policy_code,
          'policy_name' => $policy_name,
          'all_rules' => $all_rules
        );
      }
    }

    return (object) $sc;
  }


  public function getFreeItemRule(object $item, object $customer, $amount, $qty, $payment_code, $channels_code, $date, $can_group = 0)
  {
    $sc = array(
			'freeQty' => 0,
			'id_rule' => NULL,
      'rule_code' => NULL,
      'rule_name' => NULL,
      'priority' => 0,
			'policy_id' => NULL,
      'policy_code' => NULL,
      'policy_name' => NULL,
      'all_rules' => NULL
		);

    $available_policy = $this->ci->discount_model->get_available_policy($date);

    if( ! empty($available_policy))
    {
      $rules = $this->ci->discount_model->get_free_item_rule_list($item, $customer, $amount, $qty, $payment_code, $channels_code, $date, $can_group, $available_policy);

      if( ! empty($rules))
      {
        $priority = 1;

        $freeQty = 0;

        $all_rules = []; //--- เก็บข้อมูลชื่อและรหัส เงื่อนไขที่ดึงได้ทั้งหมด

        $dis_rule = NULL; //---  ที่พัก rule ที่ดีที่สุด
        $dis_code = NULL; //---  ที่พัก rule ที่ดีที่สุด
        $dis_name = NULL; //---  ที่พัก rule ที่ดีที่สุด

        $dis_policy = NULL;
        $policy_code = NULL;
        $policy_name = NULL;

        foreach($rules as $rs)
        {
          //---- เก็บข้อมูลรายชื่อโปรโมชั่น
          $all_rules[] = (object) array(
            'code' => $rs->policy_code,
            'name' => $rs->policy_name,
            'conditionCode' => $rs->code,
            'conditionName' => $rs->name,
            'conditionPriority' => $rs->priority
          );

          if($rs->priority >= $priority)
					{
						$getQty = $rs->freeQty > $freeQty ? $rs->freeQty : $freeQty;

						if($rs->minAmount > 0 && $rs->canRepeat == 1)
						{
							$sellAmount = $amount;
							$totalQty = 0;
							//---ถ้ามูลค่าที่คีย์มา มากกว่า มูลค่าขั้นต่ำ ทำการหาร เพื่อคำนวนยอดที่ได้
							while($sellAmount >= $rs->minAmount)
							{
								$sellAmount -= $rs->minAmount;
								$totalQty += $rs->freeQty;
							}

							$getQty = $totalQty > $getQty ? $totalQty : $getQty;
						}

						if($rs->minQty > 0 && $rs->canRepeat == 1)
						{
							$sellQty = $qty;
							$totalQty = 0;

							while($sellQty >= $rs->minQty)
							{
								$sellQty -= $rs->minQty;
								$totalQty += $rs->freeQty;
							}

							$getQty = $totalQty > $getQty ? $totalQty : $getQty;
						}

						if($getQty > $freeQty)
						{
							$freeQty = $getQty;
							$priority = $rs->priority;
							$dis_rule = $rs->id;
              $disc_code = $rs->code;
              $disc_name = $rs->name;
							$dis_policy = $rs->id_policy;
              $policy_code = $rs->policy_code;
              $policy_name = $rs->policy_name;
						}
					}
					//--- end priority
        }
        //-- end foreach

        $sc = array(
					'freeQty' => $freeQty,
					'id_rule' => $dis_rule,
          'rule_code' => $disc_code,
          'rule_name' => $disc_name,
          'priority' => $priority,
					'policy_id' => $dis_policy,
          'policy_code' => $policy_code,
          'policy_name' => $policy_name,
          'all_rules' => $all_rules
				);
      }
    }

    return (object) $sc;
  }


  public function getFreeItemList($id_rule)
  {
    $items = $this->ci->discount_model->get_free_item_list($id_rule);

    if( ! empty($items))
    {
      $list = [];
      foreach($items as $rs)
      {
        $list[] = (object) array('freeSKU' => $rs->product_code);
      }

      return $list;
    }

    return NULL;
  }
}
//-- end class

?>
