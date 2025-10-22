<style>
  #extra-menu {
    position: fixed;
    left: 0;
    transition: bottom 0.3s ease-in-out;
    bottom: -100px;
  }

  #extra-menu.slide-in {
    transition: bottom 0.3s ease-in-out;
    bottom: 74px;
  }


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

  #summary-list {
    position: fixed;
    top: 80px;
    left: 0;
    height: calc(100vh - 155px);
  }

  #temp-list {
    position: fixed;
    top: 80px;
    left: 0;
    height: calc(100vh - 240px);
  }

  #temp-list.show-qty {
    height: calc(100vh - 295px);
  }

  #items-list {
    position: fixed;
    top: 85px;
    left: 0;
    height: calc(100vh - 239px);
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

  .temp-qty-box {
    color: white;
    background-color: black;
    height: 60px;
    border-radius: 5px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
  }

  .temp-qty {
    width:60px;
    font-size: 14px !important;
    color: white !important;
    text-align: center;
  }

  .move-qty-box {
    color: white;
    background-color: black;
    height: 60px;
    border-radius: 5px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
  }

  .move-qty {
    width:60px;
    font-size: 14px !important;
    color: white !important;
    text-align: center;
  }

  .valid-icon {
    position: absolute;
    bottom: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    width:25px;
    height: 25px;
    background-color: white;
    border: 1px solid green;
    border-radius: 50%;
  }

  .more-menu {
    bottom:90px;
    z-index: 10;
  }
</style>
