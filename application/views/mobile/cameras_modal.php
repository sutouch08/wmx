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
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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