//window.location='/' + encodeURIComponent(search_input.value).replace(/%20/g, '+') + '/'; return false;
$(function() {
  if(window.location.pathname === '/') {
    $('#header input').focus();
  }
  var suggestion = false;
  $('#header input').click(function() {
    if (suggestion == false) {
      $('#header').append('<div id="suggestion"><ul><li>缓释胶囊 <div>234</div></li><li>缓释胶囊 <div>234</div></li><li>缓释胶囊 <div>234</div></li></ul></div>');
    }
    suggestion = true;
  });
  $('#header input').focusout(function() {
    if (suggestion) {
      $('#suggestion').remove();
      suggestion = false;
    }
  });
});