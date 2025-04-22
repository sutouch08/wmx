<style>
li {
  list-style-type: none;
}

.active > a , .active > a:hover {
	background: none;
}

.pg-footer .pg-footer-inner .pg-footer-content {
		position: fixed;
		z-index: 100;
		left: 0px;
		right: 0px;
		bottom: 0px;
		padding: 8px;
		padding-bottom: 15px;
		line-height: 20px;
		background-color:#438eb9;
}


.footer-menu {
	float: left;
	text-align: center;
	vertical-align: middle;
	line-height: 20px;
}

.footer-menu span {
	display:block;
	font-size: 12px;
	color:white;
}

.table-process {
  min-width:1240px;
}

.table-listing {
  min-width:1040px;
}

.nav-title {
  position: relative;
  height: 45px;
  padding:10px 5px;
  font-size:16px;
  text-align: center;
  border-bottom: solid 1px #ccc;
  background-color: white;
  width: 100%;
  z-index: 10
}

@media (max-width:767px) {
  .fi {
    margin-bottom: 10px;
  }

  .help-block {
    margin-top: 0px;
    margin-bottom: 0px;
  }

  .tab-content {
    margin-top:-30px;
  }

  #fromDate, #toDate {
    z-index: 11;
  }

  .filter-pad {
    position: fixed;
    top: 0px;
    right: 0px;
    background-color: white;
    height:100vh;
    width:100vw;
    box-shadow: #333 1px 0px 5px 1px;
    z-index: 10;
    padding-bottom: 80px;
    padding-right: 15px;
    padding-left: 15px;
    overflow: auto;
  }

  .move-out {
    transition: right 0.3s ease-in-out;
    right:-100vw;
  }

  .move-in {
    transition: right 0.3s ease-in-out;
     right:0px;
  }

  .extra-menu {
    position: fixed;
    bottom: 0px;
    left: 0px;
    width: 100vw;
    height: 68px;
    padding: 8px;
    line-height: 20;
    background-color: #438eb9;
    opacity: 0.9;
    z-index: 11;
  }

  .slide-out {
    transition: bottom 0.3s ease-in-out;
    bottom: 0px;
  }

  .slide-in {
    transition: bottom 0.3s ease-in-out;
    bottom: 68px;
  }

  .no-border-xs {
    border:0px !important
  }

  .margin-bottom-3 {
    margin-bottom: 3px !important
  }

  .pre-wrap {
    white-space: normal;
  }

  .btn-scroll-up.display {
    bottom:80px;
  }

  .move-list {
    margin-left: -20px;
    width: 100vw;
    max-height: 100vh;
    padding-bottom: 80px;
    overflow: auto;
    background-color: white;
    z-index: 8;
  }

  .move-list-item {
    position: relative;
    height: 80px;
    padding:10px 5px;
    border-bottom: solid 1px #ccc;
    background-color: #white;
  }

  .move-list-item:first-child {
    border-top:solid 1px #ccc;
  }

  .move-list-link {
    position: absolute;
    top: 0;
    right: 0px;
    width: 50px;
    height: 80px;
    padding:13px 20px;
    text-align: center;
    vertical-align: middle;
  }

  p.move-list-line {
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
  }
}
</style>
