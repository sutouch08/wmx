<div class="modal fade" id="cameras-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:500px; max-width:95%; margin-left:auto; margin-right:auto;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">Choose Camera</h4>
       <input type="hidden" id="select-side" value="i" />
      </div>
      <div class="modal-body">
        <div class="row" id="cameras-list" style="padding-left:12px; padding-right:12px;">

        </div>
        <div class="err-label" id="camera-error"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success btn-100" onclick="saveCameraId()">Save</button>
      </div>
   </div>
 </div>
</div>

<script id="cameras-list-template" type="text/x-handlebarsTemplate">
  <div class="col-xs-12">
    {{#each this}}
    <div class="radio">
      <label>
        <input type="radio" name="camera_id" id="{{id}}"  class="ace" maxlength="100"	value="{{id}}" />
        <span class="lbl">{{label}}</span>
      </label>
    </div>
    {{/each}}
  </div>
</script>

<div class="modal fade" id="user-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:500px; max-width:95%; margin-left:auto; margin-right:auto;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Your Information</h4>
      </div>
      <div class="modal-body">
        <div class="row" id="user-table" style="padding-left:12px; padding-right:12px;">

        </div>
      </div>
    </div>
  </div>
</div>

<script id="user-template" type="text/x-handlebarsTemplate">
  <table class="table table-bordered border-1">
    <tr><td class="fix-width-100">Username</td><td class="">{{uname}}</td></tr>
    <tr><td class="fix-width-100">ชื่อ</td><td class="">{{displayName}}</td></tr>
    <tr><td class="fix-width-100">เขต/พื้นที่</td><td class="">{{teamName}}</td></tr>
    <tr><td class="fix-width-100">ทีมติดตั้ง</td><td class="">{{team_group_name}}</td></tr>
    <tr><td class="fix-width-100">คลังต้นทาง</td><td class="">{{fromWhsCode}} : {{from_warehouse_name}}</td></tr>
    <tr><td class="fix-width-100">คลังปลายทาง</td><td class="">{{toWhsCode}} : {{to_warehouse_name}}</td></tr>
  </table>
</script>
