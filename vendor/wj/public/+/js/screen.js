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
var suggestion = null;
function getSuggestion() {
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
}
$(function() {
  $('#header input').attr('autocomplete', 'off');
  $('#header form').bind('submit', function() {
    query = encodeURIComponent($.trim($('#header input').attr('value')))
      .replace(/%20/g, '+') + '/';
    if (query == '%2B/') {
      query = '';
    }
    window.location = '/' + query;
    return false;
  });
  var previousValue = $('#header input').val();
  $('#header input').keyup(function() {
    if (previousValue != $(this).val()) {
      getSuggestion();
      previousValue = $(this).val();
      $('#header').append('<p>' + $(this).val() + '</p>');
    }
  });
  $('#header input').click(function() {
    getSuggestion();
  });
  $('#header input').focusout(function() {
    if (suggestion) {
      $('#suggestion').remove();
      suggestion = null;
    }
  });
});