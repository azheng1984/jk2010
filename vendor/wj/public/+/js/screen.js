//TODO:页面加载完成后，异步发送 tracking
//TODO:publisher sign
function suggest(data) {
  var text = '<div id="suggestion"><ul>';
  if (typeof(data) == 'undefined') {
    return;
  }
  $.each(data, function(index, value){
    text += '<li><span class="query">' + index + '</span><span class="product_amount">' + value + '</span></li>';
  });
  text += '</ul></div>';
  $('#header').append(text);
}
$(function() {
  $('#header form').bind('submit', function() {
    query = encodeURIComponent($.trim($('#header input').attr('value')))
      .replace(/%20/g, '+') + '/';
    if (query == '%2B/') {
      query = '';
    }
    window.location = '/' + query;
    return false;
  });
  var suggestion = null;
  //TODO:键盘事件
  $('#header input').click(function() {
    if (suggestion == false) {
      var query = $('#header input').attr('value');
      if (query == '') {
        return;
      }
      var uri = 'http://q.dev.huobiwanjia.com/' + encodeURIComponent(query);
      $.ajax({
        url:uri,
        cache:true,
        dataType:'jsonp',
        jsonp:false
      });
      suggestion = true;
      return;
    }
    if (suggestion == null) {
      suggestion = false;
    }
  });
  $('#header input').focusout(function() {
    if (suggestion) {
      $('#suggestion').remove();
      suggestion = null;
    }
  });
});