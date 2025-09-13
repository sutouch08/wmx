<div class="pg-footer visible-xs">
  <div class="pg-footer-inner">
    <div class="pg-footer-content text-right">
      <div class="footer-menu width-20">
        <span class="width-100" onclick="viewAll()">
          <i class="fa fa-cubes fa-2x"></i><span>view All</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="viewPending()">
          <i class="fa fa-cube fa-2x"></i><span>Pending</span>
        </span>
      </div>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="viewProcess()">
          <i class="fa fa-cube fa-2x"></i><span>Receiving</span>
        </span>
      </div>

    <?php if($doc->status == 'O' OR $doc->status == 'R') : ?>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="processMobile('<?php echo $doc->code; ?>')">
          <i class="fa fa-external-link fa-2x white"></i><span class="fon-size-12">รับเข้า</span>
        </span>
      </div>
    <?php else : ?>
      <div class="footer-menu width-20">
        <span class="width-100" onclick="doRefresh()">
          <i class="fa fa-refresh fa-2x white"></i><span class="fon-size-12">Refresh</span>
        </span>
      </div>
    <?php endif; ?>

      <div class="footer-menu width-20">
        <span class="width-100" onclick="toggleHeader()">
          <i class="fa fa-bars fa-2x white"></i><span class="fon-size-12">ข้อมูล</span>
        </span>
      </div>
    </div>
  </div>
</div>
