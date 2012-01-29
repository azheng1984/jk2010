$(function() {
  $('#price_range input').attr("autocomplete", "off");
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