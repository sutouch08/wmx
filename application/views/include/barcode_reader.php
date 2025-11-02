<style>
  #reader-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 100vw;
    background-color: #000000e0;
    z-index: 101;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  #cam {
    position: fixed;
    top: 45px;
    /* width: 100vw; */
    /* height: calc(100vh - 120px); */
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 13;
  }

  #reader {
    width: 100vw;
    height: 100vh;
    display: flex;
    justify-content:center;
    background-color: white;
  }

  #mark {
    width: 250px;
    height: 250px;
    position: absolute;
    top: calc(50vh - 170px);
    left: calc(50vw - 125px);
    z-index: 102;
  }

  #mark-top-left {
    width: 25px;
    height: 25px;
    position: absolute;
    top: 0;
    left: 0;
    border-top: solid 2px white;
    border-left: solid 2px white;
    border-top-left-radius: 5px;
  }

  #mark-top-right {
    width: 25px;
    height: 25px;
    position: absolute;
    top: 0;
    right: 0;
    border-top: solid 2px white;
    border-right: solid 2px white;
    border-top-right-radius: 5px;
  }

  #mark-bottom-left {
    width: 25px;
    height: 25px;
    position: absolute;
    bottom: 0;
    left: 0;
    border-bottom: solid 2px white;
    border-left: solid 2px white;
    border-bottom-left-radius: 5px;
  }

  #mark-bottom-right {
    width: 25px;
    height: 25px;
    position: absolute;
    bottom: 0;
    right: 0;
    border-bottom: solid 2px white;
    border-right: solid 2px white;
    border-bottom-right-radius: 5px;
  }

  video {
    position: absolute;
    top: 45px;
    width: auto !important;
    height: calc(100vh - 165px);
  }

  #stop-scan {
    position: absolute;
    bottom: 45px;
    display: flex;
    justify-content: center;
    padding: 20px 0 0 !important;
    border: 0;
    background: none;
  }

  #qr-shaded-region {
    display: none;
  }

  #qr-canvas {
    position: absolute;
    top: 45px;
    /* top: calc(50vh - 170px); */
  }

</style>

<input type="hidden" id="scan-result" />

<div id="reader-backdrop" class="hide">
  <div class="nav-title nav-title-center">
  	<a onclick="stopScan()"><i class="fa fa-angle-left fa-2x"></i></a>
  	<div class="font-size-18 text-center">Scan Barcode</div>
  </div>
  <div id="mark">
    <div id="mark-top-left"></div>
    <div id="mark-top-right"></div>
    <div id="mark-bottom-left"></div>
    <div id="mark-bottom-right"></div>
  </div>
  <div id="reader"></div>
  <div id="stop-scan">
    <button type="button" class="btn-close" onclick="stopScan()"><i class="fa fa-times"></i></button>
  </div>
</div>
