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
    height: calc(100vh - 300px);
    overflow: auto;
    font-size: 11px;
  }

  .option-right {
    position: absolute;
    top: 0;
    right: 0;
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

  .list-block.valid {
    background-color: #f4ffe7 !important;
  }

</style>
