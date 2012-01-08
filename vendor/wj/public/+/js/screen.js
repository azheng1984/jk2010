$(function() {
  if(window.location.pathname === '/') {
    $('#header input').focus();
  }
  $('#header form').bind('submit', function() {
    $query = encodeURIComponent($('#header input').attr('value'))
      .replace(/%20/g, '+');
    window.location = '/' + $query + '/';
    return false;
  });
  var suggestion = false;
  $('#header input').click(function() {
    if (suggestion == false) {
      $('#header').append('<div id="suggestion"><ul><li><span class="query">缓释胶囊</span><span class="product_amount">234</span></li><li><span class="query">缓释胶囊</span><span class="product_amount">234</span></li></ul></div>');
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