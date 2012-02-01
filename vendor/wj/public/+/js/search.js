$(function() {
  var query = {};
  if (location.search != '') {
    var qs = location.search;
    if (qs.charAt(0) == '?') qs= qs.substring(1);
    var re = /([^=&]+)(=([^&]*))?/g;
    while (match= re.exec(qs)) {
      var key = decodeURIComponent(match[1].replace(/\+/g,' '));
      var value = decodeURIComponent(match[3].replace(/\+/g,' '));
      query[key] = value;
    }
  }
  var priceFrom = typeof(query['price_from']) !== 'undefined' ? query['price_from'] : '';
  var priceTo = typeof(query['price_to']) !== 'undefined' ? query['price_to'] : '';
  var form = '<form id="price_range" action="."><label for="price_from">&yen;</label> ';
  if (typeof(query['sort']) !== 'undefined') {
    form += '<input name="sort" type="hidden" value="' + query['sort'] + '" /> ';
  }
  form += '<input id="price_from" name="price_from" type="text" value="' + priceFrom + '" autocomplete="off" />-' +
    '<input name="price_to" type="text" value="' + priceTo + '" autocomplete="off" /> ' +
    '<button type="submit"></button>' +
    '</form>';
  $('#sort').after(form);
  var isHover = false;
  $('#result li').hover(function() {
    if (isHover == true) {
      return;
    }
    isHover = true;
    $(this).append('<div id="product_toolbar"><a href="javascript:void(0)"><span> </span>同款</a></div><div id="product_tag"><a href="javascript:void(0)">分类: 手机</a></div>'
    + '</div>');
  }, function() {
    $('#product_toolbar').remove();
    $('#product_tag').remove();
    isHover = false;
  });
});
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
  $('#price_range input').focusin(function() {
    $('#price_range').append('<a href="javascript:void(0)">确定</a>');
  });
  $('#price_range input').focusout(function() {
    $('#price_range a').remove();
  });
  '<a href="javascript:void(0)">确定</a>';
});
$(function() {
  $uri = window.location.pathname + '?media=json';
  $.get($uri, function(data) {
    $('#result').after('<div id="tag">' + data + '</div>');
    $('#key_list .key').mouseup(function() {
      if ($(this).attr('class') === 'key open') {
        $(this).attr('class', 'key');
        $(this).parent().children('ol').remove();
        return;
      }
      $uri2 = window.location.pathname + '?key=' + $(this).text() + '&media=json';
      $(this).attr('id', 'target');
      $.get($uri2, function(data) {
        $('#target').after(data).attr('id', '').attr('class', 'key open');
      });
    });
  });
});