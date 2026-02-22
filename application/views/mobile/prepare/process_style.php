<style>
  #item-qty {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  #btn-increse, #btn-decrese {
    width:46px;
    height: 46px;
    border-radius: 50% !important;
  }

  #incomplete-box {
    position: fixed;
    top:85px;
    left: 0;
    width:100vw;
    height: calc(100vh - 245px);
    overflow: auto;
    font-size: 11px;
  }

  .incomplete-item {
    padding:5px;
    border:solid 1px #ccc;
    border-radius: 5px;
    margin-bottom: 10px;
  }

  .incomplete-item:last-child {      
    margin-bottom:50px !important;
  }

  .stock-reload {
    position: absolute;
    bottom: 10px;
    right: 10px;
    color:blue;
  }

  .stock-pre {
    width:100%;
    white-space:nowrap;
    overflow-x:scroll;
    padding-right:30px;
  }

  .incomplete-item.heighlight {
    background-color: #d1ffff;
  }

  .incomplete-box {
    margin-top:78px;
    margin-bottom: 220px;
  }

  #complete-list {
    position: absolute;
    top: 45px;
    left: 0px;
    height: calc(100vh - 120px);
    padding: 0px 15px 15px;
    overflow: auto;;
  }


  .complete-item {
    position: relative;
    padding:5px;
    border:solid 1px #ccc;
    border-radius: 5px;
    margin-top: 10px;
  }

  #complete-box {
    position: fixed;
    top:0px;
    right: -100vw;
    width: 100vw;
    height: calc(100vh - 75px);
    padding-top: 55px;
    overflow: auto;
    background-color: white;
    z-index: 10;
    font-size: 11px;
  }

  #close-bar {
    position: fixed;
    bottom: 250px;
    width: 100vw;
    z-index: 7;
  }

  .item-bc {
    margin-bottom: 120px !important;
  }

  .badge-no {
    position: absolute;
    top:5px;
    right: 5px;
  }

  .badge-qty {
    position: absolute;
    top:5px;
    right: 5px;
    min-width:50px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    background-color: #d0d0d0;
    border-radius: 4px;
  }

  .hide-text {
    white-space: nowrap !important;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .slide-out {
    transition: bottom 0.3s ease-in-out;
    bottom: 0px;
  }

  .slide-in {
    transition: bottom 0.3s ease-in-out;
    bottom: 68px;
  }

  .header-info{
    position: fixed;
    top: 46px;
    left: 0px;
    height: 45px;
    background-color: #000;
    color: #fff;
    border-bottom: solid 1px #ccc;
    z-index: 7;
  }

  .bottom-info {
    position: fixed;
    left: 0px;
    bottom:68px;
    height: 30px;
    background-color: white;
    z-index: 7;
    padding:3px;
  }

  #control-box {
    position: fixed;
    bottom: 98px;
    left: 0px;
    width: 100%;
    padding: 5px 12px;
    background-color: white;
    box-shadow: 0px -2px 7px #f3ecec;
    display: flex;
    justify-content: center;
    z-index: 7;
  }

  .control-box-inner {
    width:100%;
    max-width: 600px;
  }

  .control-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    color: grey;
    z-index: 2;
  }

</style>
