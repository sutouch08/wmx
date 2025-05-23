<?php
$allPayment = $rule->all_payment == 0 ? 'N' : 'Y';

$paymentNo = empty($payments) ? 0 : count($payments);
 ?>

	<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 padding-5">
      <h4 class="title">Payments Conditions</h4>
    </div>

        <div class="divider margin-top-5"></div>

				<div class="col-lg-2 col-md-2-harf col-sm-3">
					<span class="form-control left-label text-right">Payments</span>
				</div>
        <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
					<div class="btn-group width-100">
						<button type="button" class="btn btn-sm width-50" id="btn-all-payment" onclick="togglePayment('Y')" >ทั้งหมด</button>
						<button type="button" class="btn btn-sm width-50" id="btn-select-payment" onclick="togglePayment('N')" >ระบุ</button>
					</div>
        </div>
				<div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
					<button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-show-payment" onclick="showSelectPayment()" >
						Payments <span class="badge pull-right" id="badge-payment"><?php echo $paymentNo; ?></span>
					</button>
				</div>

        <div class="divider-hidden"></div>
        <div class="divider-hidden"></div>
        <div class="divider-hidden"></div>
				<div class="col-lg-2 col-md-2-harf col-sm-3">&nbsp;</div>
				<div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
					<button type="button" class="btn btn-sm btn-success btn-block" onclick="savePayment()"><i class="fa fa-save"></i> บันทึก</button>
				</div>
    </div>

		<input type="hidden" id="all_payment" value="<?php echo $allPayment; ?>" />

<?php $this->load->view('discount/rule/payment_rule_modal'); ?>
