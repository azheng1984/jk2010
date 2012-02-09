$(function() {
  if(window.location.pathname === '/') {
    $('#search_input').focus();
  }
  var suggestion = false;
  $('#search_input').click(function() {
    if (suggestion == false) {
      $('#header').append('<div id="suggestion"><ul><li>缓释胶囊 <div>234</div></li><li>缓释胶囊 <div>234</div></li><li>缓释胶囊 <div>234</div></li></ul></div>');
    }
    suggestion = true;
  });
  $('#search_input').focusout(function() {
    if (suggestion) {
      $('#suggestion').remove();
      suggestion = false;
    }
  });
  $('#option input').focusin(function() {
    $('#option form a').show();
  });
  $('#option input').focusout(function() {
    $('#option form a').hide();
  });
});