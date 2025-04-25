<?php
$allProduct = $rule->all_product == 0 ? 'N' : 'Y';


//--- ระบุชื่อสินค้า
$pdListNo = count($pdList);
$product_id = ($allProduct == 'N' && $pdListNo > 0 ) ? 'Y' : 'N';


//--- กำหนดรุ่นสินค้า
$pdModelNo = count($pdModel);
$product_model = ($pdModelNo > 0 && $allProduct == 'N') ? 'Y' : 'N';

//--- กำหนดกลุ่มสินค้า
$pdGroupNo = count($pdGroup);
$product_group = ($pdGroupNo > 0 && $allProduct == 'N' && $product_style == 'N') ? 'Y' : 'N';

//--- กำหนดชนิดสินค้า
$pdType = $this->discount_rule_model->getRuleProductType($rule->id);
$pdTypeNo = count($pdType);
$product_type = ($pdTypeNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';


//--- กำหนดหมวดหมู่สินค้า
$pdCategory = $this->discount_rule_model->getRuleProductCategory($rule->id);
$pdCategoryNo = count($pdCategory);
$product_category = ($pdCategoryNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';


//--- กำหนดยี่ห้อสินค้า
$pdBrand = $this->discount_rule_model->getRuleProductBrand($rule->id);
$pdBrandNo = count($pdBrand);
$product_brand = ($pdBrandNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';
 ?>

 <div class="row">
   <div class="col-lg-12 col-md-12 col-sm-12 padding-5">
     <h4 class="title">Product Conditions</h4>
   </div>

   <div class="divider margin-top-5"></div>
   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control left-label text-right">All products</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="btn btn-sm width-50 btn-primary" id="btn-pd-all-yes" onclick="toggleAllProduct('Y')">YES</button>
       <button type="button" class="btn btn-sm width-50" id="btn-pd-all-no" onclick="toggleAllProduct('N')">NO</button>
     </div>
   </div>
   <div class="divider-hidden"></div>

   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control left-label text-right">SKU</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-product-id-yes" onclick="toggleProductId('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-product-id-no" onclick="toggleProductId('N')" disabled>NO</button>
     </div>
   </div>

   <div class="col-lg-5 col-md-5 col-sm-5 padding-5">
     <input type="text" class="not-pd-all option form-control input-sm" id="txt-product-id-box" placeholder="รหัส/ชื่อสินค้า" disabled />
     <input type="hidden" id="id_product" />
   </div>

   <div class="col-sm-1 padding-5">
     <button type="button" class="not-pd-all option btn btn-xs btn-info btn-block" id="btn-product-id-add" onclick="addProductId()" disabled><i class="fa fa-plus"></i> เพิ่ม</button>
   </div>

   <div class="col-sm-1 col-1-harf padding-5">
     <button type="button" class="not-pd-all option btn btn-xs btn-info btn-block" id="btn-product-import" onclick="getUploadFile()" disabled><i class="fa fa-upload"></i> import</button>
   </div>

   <div class="divider-hidden"></div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 not-show">
     <span class="form-control left-label">SKU</span>
   </div>
   <div class="col-lg-10 col-md-9-harf col-sm-9 padding-5" style="max-height:300px; overflow:auto; margin-bottom:5px;">
     <table class="table table-striped border-1">
       <thead>
         <tr>
           <th class="fix-width-40">
             <label>
               <input type="checkbox" class="ace" onchange="checkItemAll($(this))">
               <span class="lbl"></span>
             </label>
           </th>
           <th class="fix-width-150">SKU Code</th>
           <th class="min-width-250">Description</th>
           <th class="fix-width-60 text-center"><button type="button" class="btn btn-mini btn-danger btn-block" onclick="removeItem()">Delete</button></th>
         </tr>
       </thead>
       <tbody id="itemList">
         <?php if(!empty($pdList)) : ?>
           <?php foreach($pdList as $item) : ?>
             <tr id="item-row-<?php echo $item->product_id; ?>">
               <td class="middle text-center">
                 <label>
                   <input type="checkbox" class="ace item-chk" value="<?php echo $item->product_id; ?>">
                   <span class="lbl"></span>
                 </label>
               </td>
               <td class="middle">
                 <?php echo $item->code; ?>
                 <input type="hidden" class="item-id" id="item-id-<?php echo $item->product_id; ?>" value="<?php echo $item->product_id; ?>">
               </td>
               <td class="middle" colspan="2"><?php echo $item->name; ?></td>
             </tr>
           <?php endforeach; ?>
         <?php endif; ?>
       </tbody>
     </table>
   </div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control left-label text-right">Model</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-model-id-yes" onclick="toggleModelId('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-model-id-no" onclick="toggleModelId('N')" disabled>NO</button>
     </div>
   </div>


   <div class="col-lg-5 col-md-5 col-sm-5 padding-5">
     <input type="text" class="not-pd-all option form-control input-sm" id="txt-model-id-box" placeholder="รหัส/ชื่อรุ่น" disabled />
     <input type="hidden" id="id_model" />
   </div>

   <div class="col-sm-1 padding-5">
     <button type="button" class="not-pd-all option btn btn-xs btn-info btn-block" id="btn-model-id-add" onclick="addModelId()" disabled><i class="fa fa-plus"></i> เพิ่ม</button>
   </div>

   <div class="col-sm-1 col-1-harf padding-5">
     <button type="button" class="not-pd-all option btn btn-xs btn-info btn-block" id="btn-model-import" onclick="getUploadFile()" disabled><i class="fa fa-upload"></i> import</button>
   </div>

   <div class="divider-hidden"></div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 not-show">
     <span class="form-control left-label">Model</span>
   </div>
   <div class="col-lg-10 col-md-9-harf col-sm-9 padding-5" style="max-height:300px; overflow:auto; margin-bottom:5px;">
     <table class="table table-striped border-1">
       <thead>
         <tr>
           <th class="fix-width-40">
             <label>
               <input type="checkbox" class="ace" onchange="checkModelAll($(this))">
               <span class="lbl"></span>
             </label>
           </th>
           <th class="min-width-100">Model Code</th>
           <th class="min-width-250">Desctiption</th>
           <th class="fix-width-60 text-center"><button type="button" class="btn btn-mini btn-danger btn-block" onclick="removeModel()">Delete</button></th>
         </tr>
       </thead>
       <tbody id="modelList">
         <?php if(!empty($pdModel)) : ?>
           <?php foreach($pdModel as $item) : ?>
             <tr id="model-row-<?php echo $item->model_id; ?>">
               <td class="middle text-center">
                 <label>
                   <input type="checkbox" class="ace model-chk" value="<?php echo $item->model_id; ?>">
                   <span class="lbl"></span>
                 </label>
               </td>
               <td class="middle"><?php echo $item->code; ?></td>
               <td class="middle" colspan="2">
                 <?php echo $item->name; ?>
                 <input type="hidden" class="model-id" id="model-id-<?php echo $item->model_id; ?>" value="<?php echo $item->model_id; ?>">
               </td>
             </tr>
           <?php endforeach; ?>
         <?php endif; ?>
       </tbody>
     </table>
   </div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control left-label text-right">Group</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-group-yes" onclick="toggleProductGroup('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-group-no" onclick="toggleProductGroup('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-group" onclick="showProductGroup()" disabled>
       Select Group <span class="badge pull-right" id="badge-pd-group"><?php echo $pdGroupNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control left-label text-right">Type</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-type-yes" onclick="toggleProductType('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-type-no" onclick="toggleProductType('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-type" onclick="showProductType()" disabled>
       Select Type <span class="badge pull-right" id="badge-pd-type"><?php echo $pdTypeNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control left-label text-right">Category</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-cat-yes" onclick="toggleProductCategory('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-cat-no" onclick="toggleProductCategory('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-cat" onclick="showProductCategory()" disabled>
       Select Category <span class="badge pull-right" id="badge-pd-cat"><?php echo $pdCategoryNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control left-label text-right">Brand</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-brand-yes" onclick="toggleProductBrand('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-brand-no" onclick="toggleProductBrand('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-brand" onclick="showProductBrand()" disabled>
       Select Brand <span class="badge pull-right" id="badge-pd-brand"><?php echo $pdBrandNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>

   <div class="divider-hidden"></div>
   <div class="col-lg-2 col-md-2-harf col-sm-3">&nbsp;</div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="btn btn-sm btn-success btn-block" onclick="saveProduct()"><i class="fa fa-save"></i> Save</button>
   </div>


 </div>

 <input type="hidden" id="all_product" value="<?php echo $allProduct; ?>" />
 <input type="hidden" id="product_id" value="<?php echo $product_id; ?>" />
 <input type="hidden" id="product_model" value="<?php echo $product_model; ?>" />
 <input type="hidden" id="product_type" value="<?php echo $product_type; ?>" />
 <input type="hidden" id="product_category" value="<?php echo $product_category; ?>" />
 <input type="hidden" id="product_brand" value="<?php echo $product_brand; ?>" />


 <div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog" model="width:500px;">
     <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
         <h4 class="modal-title">Import Product Model</h4>
       </div>
       <div class="modal-body">
         <form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
           <div class="row">
             <div class="col-sm-9">
               <button type="button" class="btn btn-sm btn-primary btn-block" id="show-file-name" onclick="getFile()">กรุณาเลือกไฟล์ Excel</button>
             </div>

             <div class="col-sm-3">
               <button type="button" class="btn btn-sm btn-info" onclick="readExcelFile()"><i class="fa fa-cloud-upload"></i> นำเข้า</button>
             </div>
           </div>
           <input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
         </form>
       </div>
       <div class="modal-footer">

       </div>
     </div>
   </div>
 </div>

 <script type="text/x-handlebarsTemplate" id="itemRowTemplate">
   <tr id="item-row-{{id}}">
     <td class="middle text-center"><label><input type="checkbox" class="ace item-chk" value="{{id}}"><span class="lbl"></span></label></td>
     <td class="middle">
       {{code}}
       <input type="hidden" class="item-id" id="item-id-{{id}}" value="{{id}}">
     </td>
     <td class="middle" colspan="2">{{name}}</td>
   </tr>
 </script>

 <script type="text/x-handlebarsTemplate" id="modelRowTemplate">
   <tr id="model-row-{{id}}">
     <td class="middle text-center"><label><input type="checkbox" class="ace model-chk" value="{{id}}"><span class="lbl"></span></label></td>
     <td class="middle" >
       {{code}}
       <input type="hidden" class="model-id" id="model-id-{{id}}" value="{{id}}">
     </td>
     <td class="middle" colspan="2">{{name}}</td>
   </tr>
 </script>

 <script>
   $('#id_product').select2();
   $('#id_model').select2();
 </script>

 <?php $this->load->view('discount/rule/product_rule_modal'); ?>
