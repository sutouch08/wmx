<style>
  #item-search-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 100vw;
    background-color: #ffffff;
    z-index: 101;
  }

  #item-search-control {
    width: 100%;
    position: fixed;
    top: 70px;
    left: 0px;
    padding: 0 20px;
  }

  #item-search {
    padding-left: 15px;
    padding-right: 40px;
  }

  #search-btn {
    position: absolute;
    top: 3px;
    right: 3px;
    z-index: 2;
    color: #969696;
    background-color: #ddd;
    width: 40px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 5px;
  }

  #close-search {
    position: fixed;
    bottom: 45px;
    width: 100%;
    left: 0;
    display: flex;
    justify-content: center;
    align-items: center;
  }

</style>

<div class="hide" id="item-search-backdrop">
  <div class="nav-title nav-title-center">
  	<a onclick="closeItemSearch()"><i class="fa fa-angle-left fa-2x"></i></a>
  	<div class="font-size-18 text-center">Item Search</div>
  </div>
  <div id="item-search-control">
    <div class="input-group width-100">
      <input type="text" class="form-control input-lg" id="item-search" placeholder="SKU Code" autocomplete="off">
      <span id="search-btn">
        <i class="ace-icon fa fa-search fa-2x control-icon" onclick="getItemByCode()"></i>
      </span>
    </div>
  </div>
  <div id="close-search">
    <button type="button" class="btn-close" onclick="closeItemSearch()"><i class="fa fa-times"></i></button>
  </div>
</div>
