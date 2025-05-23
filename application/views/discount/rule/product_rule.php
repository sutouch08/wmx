<?php
$allProduct = $rule->all_product == 0 ? 'N' : 'Y';

//--- ระบุชื่อสินค้า
$pdListNo = empty($pdList) ? 0 : count($pdList);
$product_id = ($allProduct == 'N' && $pdListNo > 0 ) ? 'Y' : 'N';

//--- กำหนดรุ่นสินค้า
$pdModelNo = empty($pdModel) ? 0 : count($pdModel);
$product_model = ($pdModelNo > 0 && $allProduct == 'N') ? 'Y' : 'N';

//--- กำหนดกลุ่มสินค้าหลัก
$pdMainGroupNo = count($pdMainGroup);
$product_main_group = ($pdMainGroupNo > 0 && $allProduct == 'N' && $product_id == 'N' && $product_model == 'N') ? 'Y' : 'N';

//--- กำหนดกลุ่มสินค้า
$pdGroupNo = count($pdGroup);
$product_group = ($pdGroupNo > 0 && $allProduct == 'N' && $product_id == 'N' && $product_model == 'N') ? 'Y' : 'N';

//--- กำหนด Segment
$pdSegmentNo = count($pdSegment);
$product_segment = ($pdSegmentNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';

//--- กำหนด Class
$pdClassNo = count($pdClass);
$product_class = ($pdClassNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';

//--- กำหนด Family
$pdFamilyNo = count($pdFamily);
$product_family = ($pdFamilyNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';

//--- กำหนดชนิดสินค้า
$pdTypeNo = count($pdType);
$product_type = ($pdTypeNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';

//--- กำหนดประเภทสินค้า
$pdKindNo = count($pdKind);
$product_kind = ($pdKindNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';


//--- กำหนด Gender
$pdGenderNo = count($pdGender);
$product_gender = ($pdGenderNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';

//--- กำหนด Sport type
$pdSportTypeNo = count($pdSportType);
$product_sport_type = ($pdSportTypeNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';

//--- กำหนดหมวดหมู่สินค้า
$pdCollectionNo = count($pdCollection);
$product_collection = ($pdCollectionNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';

//--- กำหนดยี่ห้อสินค้า
$pdBrandNo = count($pdBrand);
$product_brand = ($pdBrandNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';

//--- กำหนดปีสินค้า
$pdYearNo = count($pdYear);
$product_year = ($pdYearNo > 0 && $allProduct == 'N' && $product_model == 'N' && $product_id == 'N') ? 'Y' : 'N';
 ?>

 <div class="row">
   <div class="col-lg-12 col-md-12 col-sm-12 padding-5">
     <h4 class="title">Product Conditions</h4>
   </div>

   <div class="divider margin-top-5"></div>
   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">All products</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="btn btn-sm width-50 btn-primary" id="btn-pd-all-yes" onclick="toggleAllProduct('Y')">YES</button>
       <button type="button" class="btn btn-sm width-50" id="btn-pd-all-no" onclick="toggleAllProduct('N')">NO</button>
     </div>
   </div>
   <div class="divider-hidden"></div>

   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">SKU</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-product-id-yes" onclick="toggleProductId('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-product-id-no" onclick="toggleProductId('N')" disabled>NO</button>
     </div>
   </div>

   <div class="col-lg-5 col-md-5 col-sm-5 padding-5">
     <input type="text" class="not-pd-all option form-control input-sm" id="txt-product-id-box" placeholder="รหัส/ชื่อสินค้า" disabled />
     <input type="hidden" id="product-id" data-code="" data-name=""/>
   </div>

   <div class="col-sm-1 padding-5">
     <button type="button" class="not-pd-all option btn btn-xs btn-info btn-block" id="btn-product-id-add" onclick="addProductId()" disabled><i class="fa fa-plus"></i> เพิ่ม</button>
   </div>

   <div class="col-sm-1 col-1-harf padding-5 hide">
     <button type="button" class="not-pd-all option btn btn-xs btn-info btn-block" id="btn-product-import" onclick="getUploadFile()" disabled><i class="fa fa-upload"></i> import</button>
   </div>

   <div class="divider-hidden"></div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 not-show">
     <span class="form-control text-label">SKU</span>
   </div>
   <div class="col-lg-10 col-md-9-harf col-sm-9 padding-5" style="max-height:300px; overflow:auto; margin-bottom:5px;">
     <table class="table table-striped border-1">
       <thead>
         <tr class="font-size-11">
           <th class="fix-width-40">
             <label>
               <input type="checkbox" class="ace" onchange="checkItemAll($(this))">
               <span class="lbl"></span>
             </label>
           </th>
           <th class="fix-width-150">SKU Code</th>
           <th class="min-width-250">Description</th>
           <th class="fix-width-60 text-center"><button type="button" class="btn btn-minier btn-danger btn-block" onclick="removeItem()">Delete</button></th>
         </tr>
       </thead>
       <tbody id="itemList">
         <?php if(!empty($pdList)) : ?>
           <?php foreach($pdList as $item) : ?>
             <tr class="font-size-11" id="item-row-<?php echo $item->product_id; ?>">
               <td class="middle text-center">
                 <label>
                   <input type="checkbox" class="ace item-chk"
                    id="item-id-<?php echo $item->product_id; ?>"
                    value="<?php echo $item->product_id; ?>"
                    data-code="<?php echo $item->product_code; ?>" >
                   <span class="lbl"></span>
                 </label>
               </td>
               <td class="middle"><?php echo $item->product_code; ?></td>
               <td class="middle" colspan="2"><?php echo $item->product_name; ?></td>
             </tr>
           <?php endforeach; ?>
         <?php endif; ?>
       </tbody>
     </table>
   </div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Model</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-model-id-yes" onclick="toggleModelId('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-model-id-no" onclick="toggleModelId('N')" disabled>NO</button>
     </div>
   </div>


   <div class="col-lg-5 col-md-5 col-sm-5 padding-5">
     <input type="text" class="not-pd-all option form-control input-sm" id="txt-model-id-box" placeholder="รหัส/ชื่อรุ่น" disabled />
     <input type="hidden" id="model-id" data-code="" data-name="" />
   </div>

   <div class="col-lg-1 col-md-1 col-sm-1 padding-5">
     <button type="button" class="not-pd-all option btn btn-xs btn-info btn-block" id="btn-model-id-add" onclick="addModelId()" disabled><i class="fa fa-plus"></i> เพิ่ม</button>
   </div>

   <div class="col-sm-1 col-1-harf padding-5 hide">
     <button type="button" class="not-pd-all option btn btn-xs btn-info btn-block" id="btn-model-import" onclick="getUploadFile()" disabled><i class="fa fa-file-excel-o"></i>&nbsp; import</button>
   </div>

   <div class="divider-hidden"></div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 not-show">
     <span class="form-control text-label">Model</span>
   </div>
   <div class="col-lg-10 col-md-9-harf col-sm-9 padding-5" style="max-height:300px; overflow:auto; margin-bottom:5px;">
     <table class="table table-striped border-1">
       <thead>
         <tr class="font-size-11">
           <th class="fix-width-40">
             <label>
               <input type="checkbox" class="ace" onchange="checkModelAll($(this))">
               <span class="lbl"></span>
             </label>
           </th>
           <th class="min-width-100">Model Code</th>
           <th class="min-width-250">Desctiption</th>
           <th class="fix-width-60 text-center"><button type="button" class="btn btn-minier btn-danger btn-block" onclick="removeModel()">Delete</button></th>
         </tr>
       </thead>
       <tbody id="modelList">
         <?php if(!empty($pdModel)) : ?>
           <?php foreach($pdModel as $item) : ?>
             <tr class="font-size-11" id="model-row-<?php echo $item->model_id; ?>">
               <td class="middle text-center">
                 <label>
                   <input type="checkbox" class="ace model-chk"
                   id="model-<?php echo $item->model_id; ?>"
                   value="<?php echo $item->model_id; ?>"
                   data-code="<?php echo $item->model_code; ?>"  />
                   <span class="lbl"></span>
                 </label>
               </td>
               <td class="middle"><?php echo $item->model_code; ?></td>
               <td class="middle" colspan="2"><?php echo $item->model_name; ?></td>
             </tr>
           <?php endforeach; ?>
         <?php endif; ?>
       </tbody>
     </table>
   </div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Main Group</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-main-group-yes" onclick="toggleProductMainGroup('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-main-group-no" onclick="toggleProductMainGroup('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-main-group" onclick="showProductMainGroup()" disabled>
       Select <span class="badge pull-right" id="badge-pd-main-group"><?php echo $pdMainGroupNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>

   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Sub Group</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-group-yes" onclick="toggleProductGroup('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-group-no" onclick="toggleProductGroup('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-group" onclick="showProductGroup()" disabled>
       Select <span class="badge pull-right" id="badge-pd-group"><?php echo $pdGroupNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Segment</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-segment-yes" onclick="toggleProductSegment('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-segment-no" onclick="toggleProductSegment('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-segment" onclick="showProductSegment()" disabled>
       Select <span class="badge pull-right" id="badge-pd-segment"><?php echo $pdSegmentNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Class</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-class-yes" onclick="toggleProductClass('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-class-no" onclick="toggleProductClass('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-class" onclick="showProductClass()" disabled>
       Select <span class="badge pull-right" id="badge-pd-class"><?php echo $pdClassNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Family</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-family-yes" onclick="toggleProductFamily('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-family-no" onclick="toggleProductFamily('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-family" onclick="showProductFamily()" disabled>
       Select <span class="badge pull-right" id="badge-pd-family"><?php echo $pdFamilyNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Type</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-type-yes" onclick="toggleProductType('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-type-no" onclick="toggleProductType('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-type" onclick="showProductType()" disabled>
       Select <span class="badge pull-right" id="badge-pd-type"><?php echo $pdTypeNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Kind</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-kind-yes" onclick="toggleProductKind('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-kind-no" onclick="toggleProductKind('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-kind" onclick="showProductKind()" disabled>
       Select <span class="badge pull-right" id="badge-pd-kind"><?php echo $pdKindNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Gender</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-gender-yes" onclick="toggleProductGender('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-gender-no" onclick="toggleProductGender('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-gender" onclick="showProductGender()" disabled>
       Select <span class="badge pull-right" id="badge-pd-gender"><?php echo $pdGenderNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Sport Type</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-sport-type-yes" onclick="toggleProductSportType('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-sport-type-no" onclick="toggleProductSportType('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-sport-type" onclick="showProductSportType()" disabled>
       Select <span class="badge pull-right" id="badge-pd-sport-type"><?php echo $pdSportTypeNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Club/Collection</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-collection-yes" onclick="toggleProductCollection('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-collection-no" onclick="toggleProductCollection('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-collection" onclick="showProductCollection()" disabled>
       Select <span class="badge pull-right" id="badge-pd-collection"><?php echo $pdCollectionNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>


   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Brand</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-brand-yes" onclick="toggleProductBrand('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-brand-no" onclick="toggleProductBrand('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-brand" onclick="showProductBrand()" disabled>
       Select <span class="badge pull-right" id="badge-pd-brand"><?php echo $pdBrandNo; ?></span>
     </button>
   </div>
   <div class="divider-hidden"></div>

   <div class="col-lg-2 col-md-2-harf col-sm-3">
     <span class="form-control text-label text-right">Year</span>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <div class="btn-group width-100">
       <button type="button" class="not-pd-all btn btn-sm width-50" id="btn-pd-year-yes" onclick="toggleProductYear('Y')" disabled>YES</button>
       <button type="button" class="not-pd-all btn btn-sm width-50 btn-primary" id="btn-pd-year-no" onclick="toggleProductYear('N')" disabled>NO</button>
     </div>
   </div>
   <div class="col-lg-2 col-md-2-harf col-sm-3 padding-5">
     <button type="button" class="option btn btn-xs btn-info btn-block padding-right-5" id="btn-select-pd-year" onclick="showProductYear()" disabled>
       Select <span class="badge pull-right" id="badge-pd-year"><?php echo $pdYearNo; ?></span>
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
 <input type="hidden" id="product_main_group" value="<?php echo $product_main_group; ?>" />
 <input type="hidden" id="product_group" value="<?php echo $product_group; ?>" />
 <input type="hidden" id="product_segment" value="<?php echo $product_segment; ?>" />
 <input type="hidden" id="product_class" value="<?php echo $product_class; ?>" />
 <input type="hidden" id="product_family" value="<?php echo $product_family; ?>" />
 <input type="hidden" id="product_type" value="<?php echo $product_type; ?>" />
 <input type="hidden" id="product_kind" value="<?php echo $product_kind; ?>" />
 <input type="hidden" id="product_gender" value="<?php echo $product_gender; ?>" />
 <input type="hidden" id="product_sport_type" value="<?php echo $product_sport_type; ?>" />
 <input type="hidden" id="product_collection" value="<?php echo $product_collection; ?>" />
 <input type="hidden" id="product_brand" value="<?php echo $product_brand; ?>" />
 <input type="hidden" id="product_year" value="<?php echo $product_year; ?>" />


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
   <tr class="font-size-11" id="item-row-{{id}}">
     <td class="middle text-center">
      <label>
        <input type="checkbox" class="ace item-chk"
        id="item-{{id}}"
        value="{{id}}"
        data-code="{{code}}" />
        <span class="lbl"></span>
      </label>
    </td>
     <td class="middle">{{code}}</td>
     <td class="middle" colspan="2">{{name}}</td>
   </tr>
 </script>

 <script type="text/x-handlebarsTemplate" id="modelRowTemplate">
   <tr class="font-size-11" id="model-row-{{id}}">
     <td class="middle text-center">
      <label>
        <input type="checkbox" class="ace model-chk"
        id="model-{{id}}"
        value="{{id}}"
        data-code="{{code}}" />
        <span class="lbl"></span>
      </label>
    </td>
     <td class="middle" >{{code}}</td>
     <td class="middle" colspan="2">{{name}}</td>
   </tr>
 </script>

 <?php $this->load->view('discount/rule/product_rule_modal'); ?>
