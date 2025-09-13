<div class="goback">
  <?php if($tab == 'view') : ?>
    <a class="goback-icon pull-left" onclick="javascript:history.back()"><i class="fa fa-angle-left fa-2x"></i></a>
  <?php else : ?>
    <a class="goback-icon pull-left" onclick="leave()"><i class="fa fa-angle-left fa-2x"></i></a>
  <?php endif; ?>
</div>
<div class="col-xs-12 filter-pad move-out" id="header-pad">
  <div class="nav-title">
    <a class="pull-left" onclick="closeHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">ข้อมูลเอกสาร</div>
  </div>
  <div class="width-100">
    <div class="header-info-row pre-wrap">Document No. <span class="pull-right"><?php echo $doc->code; ?></span></div>
    <div class="header-info-row pre-wrap">Document date <span class="pull-right"><?php echo thai_date($doc->date_add); ?></span></div>
    <div class="header-info-row pre-wrap">Posting date <span class="pull-right"><?php echo empty($doc->shipped_date) ? NULL : thai_date($doc->shipped_date); ?></span></div>
    <div class="header-info-row pre-wrap">Vender code <span class="pull-right"><?php echo $doc->vender_code; ?></span></div>
    <div class="header-info-row pre-wrap">Vender Name <span class="pull-right"><?php echo $doc->vender_name; ?></span></div>
    <div class="header-info-row pre-wrap">Po No. <span class="pull-right"><?php echo $doc->po_code; ?></span></div>
    <div class="header-info-row pre-wrap">ERP Ref. <span class="pull-right"><?php echo $doc->po_ref; ?></span></div>
    <div class="header-info-row pre-wrap">Invoice <span class="pull-right"><?php echo $doc->invoice_code; ?></span></div>
    <div class="header-info-row pre-wrap">Warehouse <span class="pull-right"><?php echo $doc->warehouse_code; ?></span></div>
    <div class="header-info-row pre-wrap">Bin Location <span class="pull-right"><?php echo empty($zone) ? NULL : $zone->name; ?></span></div>
    <div class="header-info-row pre-wrap">Remark <span class="pull-right"><?php echo $doc->remark; ?></span></div>
  </div>
</div><!-- end from-horizontal -->
<hr class="margin-top-15 margin-bottom-15"/>
