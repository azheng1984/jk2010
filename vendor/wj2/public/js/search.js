function bindEvent() {
  $('.value_list li').hover(
      function() {
        $(this).attr('class', 'current');
        $(this).children('a').css('text-decoration', 'line-through').css('color', '#888');
        $('.value_list .current .delete').show();
      },function() {
        $('.value_list .current .delete').hide();
        $(this).attr('class', '');
    }
  );
}
$(function() {
  $uri = window.location.pathname + '?media=json';
  $.get($uri, function(data) {
    $('#filter').html(data);
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
        bindEvent();
      });
    });
    bindEvent();
  });
  var isHover = false;
  $('#result ol li').hover(function() {
    if (isHover == true) {
      return;
    }
    isHover = true;
    $(this).append('<div id="hover"><div class="toolbar"><a href="javascript:void(0)">同款</a> <a href="javascript:void(0)">关注</a></div><div class="tag"><a href="javascript:void(0)">分类: 手机</a></div>'
    + '</div>');
  }, function() {
    $('#hover').remove();
    isHover = false;
  });
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
});