function toggleFilter() {
  let filter = $('#filter-pad');

  if(filter.hasClass('move-in')) {
    filter.removeClass('move-in');
  }
  else {
    filter.addClass('move-in');
  }
}


function closeFilter() {
  $('#filter-pad').removeClass('move-in');
}


function toggleHeader() {
  let hd = $('#header-panel');

  if(hd.hasClass('move-in')) {
    hd.removeClass('move-in');
  }
  else {
    hd.addClass('move-in');
  }
}


function closeHeader() {
  $('#header-panel').removeClass('move-in');
}


function toggleUsermenu() {
  let menu = $('#user-menu');

  if(menu.hasClass('slide-in')) {
    menu.removeClass('slide-in');
    $('#backdrop').removeClass('visible');
  }
  else {
    menu.addClass('slide-in');
    $('#backdrop').addClass('visible');
  }
}


$('.paginater-toggle').click(function() {
  let el = $('.paginater');

  if(el.hasClass('open')) {
    el.removeClass('open');
    $(this).html('<i class="fa fa-angle-up fa-lg"></i>');
  }
  else {
    el.addClass('open');
    $(this).html('<i class="fa fa-angle-down fa-lg"></i>');
  }
});
