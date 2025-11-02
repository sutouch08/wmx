<style>
  #item-panel {
    position: fixed;
    top:0;
    left: 0;
    height: 100vh;
    background-color: white;
    z-index: 101;
  }

  .item-input {
    margin-top: 45px;
    height: calc(100vh - 170px);
  }

  .submit-btn {
    position: absolute;
    bottom: 0px;
    padding: 30px 15px 60px;
    border-top: 1px solid #ddd;
  }

  #qty-down {
    position: absolute;
    top: 28px;
    left: 18px;
    font-size: 30px;
    width: 25px;
    height: 25px;
    background-color: #dbdbdb;
    color: #797979;
    border-radius: 50%;
    padding-bottom: 6px;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  #qty-up {
    position: absolute;
    top: 28px;
    right: 18px;
    font-size: 20px;
    width: 25px;
    height: 25px;
    background-color: #dbdbdb;
    color: #797979;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>

<div class="hide" id="item-panel">
  <div class="nav-title nav-title-right">
    <a class="hidden-lg" onclick="closeItemPanel()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">เพิ่มรายการใหม่</div>
  </div>
  <div class="col-xs-12 page-wrap item-input">
    <div class="col-xs-12 fi">
      <label>Barcode</label>
      <input type="text" class="form-control c" id="barcode"  value="" readonly />
    </div>
    <div class="col-xs-12 fi">
      <label>SKU</label>
      <input type="text" class="form-control c" id="item-code"  value="" readonly/>
    </div>
    <div class="col-xs-12 fi">
      <label>Description</label>
      <input type="text" class="form-control c" id="item-name"  value="" readonly/>
    </div>
    <div class="col-xs-6 fi">
      <label>Price</label>
      <input type="text" class="form-control text-center c" id="item-price"  value=""/>
    </div>
    <div class="col-xs-6 fi">
      <label>Dicount</label>
      <input type="text" class="form-control text-center c" id="item-disc"  value="" />
    </div>
    <div class="col-xs-6 fi">
      <label>In Stock</label>
      <input type="text" class="form-control text-center c" id="stock-qty"  value="0" readonly/>
    </div>
    <div class="col-xs-6 fi">
      <label>Qty</label>
      <input type="text" class="form-control text-center c" inputmode="numeric" id="qty"  value="1" />
      <span id="qty-down" onclick="decrease()">-</span>
      <span id="qty-up" onclick="increase()">+</span>
    </div>
    <div class="col-xs-6 fi">
      <label>Amount</label>
      <input type="text" class="form-control text-center c" id="amount"  value="0" />
    </div>

    <input type="hidden" id="count-stock" value="1" />    
    <input type="hidden" id="is-edit" value="0" />
    <input type="hidden" id="row-id" value="" />
  </div>

  <div class="col-xs-12 submit-btn hide" id="add-btn">
    <div class="col-xs-4">
      <button type="button" class="btn btn-sm btn-purple btn-block" onclick="reScan()"><i class="fa fa-qrcode fa-lg"></i>&nbsp;&nbsp Scan</button>
    </div>
    <div class="col-xs-8">
      <button type="button" class="btn btn-sm btn-primary btn-block" onclick="addDetail()"><i class="fa fa-plus"></i> Add</button>
    </div>
  </div>
  <div class="col-xs-12 submit-btn hide" id="edit-btn">
    <div class="col-xs-12">
      <button type="button" class="btn btn-sm btn-primary btn-block" onclick="updateDetail()"><i class="fa fa-save"></i> Update</button>
    </div>
  </div>
</div>
