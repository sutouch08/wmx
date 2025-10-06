<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="move-table">
  	<table class="table table-striped border-1" style="min-width:940px;">
    	<thead>
      	<tr>
        	<th class="fix-width-40 text-center">ลำดับ</th>
          <th class="fix-width-150">รหัส</th>
          <th class="min-width-250">สินค้า</th>
          <th class="fix-width-200">ต้นทาง</th>
          <th class="fix-width-200">ปลายทาง</th>
          <th class="fix-width-100 text-center">จำนวน</th>
        </tr>
      </thead>

      <tbody id="move-list">
<?php if(!empty($details)) : ?>
<?php		$no = 1;						?>
<?php   $total_qty = 0; ?>
<?php		foreach($details as $rs) : 	?>
				<tr class="font-size-11">
	      	<td class="middle text-center"><?php echo $no; ?></td>
	        <td class="middle"><?php echo $rs->product_code; ?></td>
	        <td class="middle"><?php echo $rs->product_name; ?></td>
	        <td class="middle"><?php echo $rs->from_zone; ?></td>
	        <td class="middle"><?php 	echo $rs->to_zone; 	?></td>
					<td class="middle text-center" ><?php echo number($rs->qty); ?></td>
	      </tr>
<?php			$no++;			?>
<?php     $total_qty += $rs->qty; ?>
<?php		endforeach;			?>
				<tr>
					<td colspan="5" class="middle text-right"><strong>รวม</strong></td>
					<td class="middle text-center"><strong><?php echo number($total_qty); ?></strong></td>
				</tr>
<?php	else : ?>
 				<tr>
        	<td colspan="6" class="text-center"><h4>ไม่พบรายการ</h4></td>
        </tr>
<?php	endif; ?>
      </tbody>
    </table>
  </div>
</div>
