$(function() {
  $('#price_limit input').attr("autocomplete", "off");
  var isHover = false;
  $('#result ol li').hover(function() {
    if (isHover == true) {
      return;
    }
    isHover = true;
    $(this).append('<div id="hover"><div class="toolbar"><a href="javascript:void(0)"><span> </span>同款</a> <a href="javascript:void(0)"><span class="heart"> </span>关注</a></div><div class="tag"><a href="javascript:void(0)">分类: 手机</a></div>'
    + '</div>');
  }, function() {
    $('#hover').remove();
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
  $('#price_limit input').focusin(function() {
    $('#price_limit a').show();
  });
  $('#price_limit input').focusout(function() {
    $('#price_limit a').hide();
  });
  '<a href="javascript:void(0)">确定</a>';
});

function bindEvent() {
  $('#target:parent li').hover(
      function() {
        $(this).attr('class', 'current');
        $('.value_list .current .delete').show();
        $('.value_list .current .delete').mouseover(function() {
          $property = $(this).parent().children('a').first();
          if ($property.attr('class') == 'selected') {
            $property.attr('class', 'line-through selected');
            return;
          }
         $property.attr('class', 'line-through gray-color');
        });
        $('.value_list .current .delete').mouseout(function() {
          $property = $(this).parent().children('a').first();
          if ($property.attr('class') == 'line-through gray-color') {
            $property.attr('class', '');
            return;
          }
          $property.attr('class', 'selected');
        });
      },function() {
        $('.value_list .current .delete').off('mouseover');
        $('.value_list .current .delete').off('mouseout');
        $('.value_list .current .delete').hide();
        $('.value_list .current').attr('class', '');
      }
  );
}
$(function() {
  $uri = window.location.pathname + '?media=json';
  $.get($uri, function(data) {
    $('#result').after('<div id="filter">' + data + '</div>');
    $('#key_list .key').mouseup(function() {
      if ($(this).attr('class') === 'key open') {
        $(this).attr('class', 'key');
        $(this).parent().children('ol').remove();
        return;
      }
      $uri2 = window.location.pathname + '?key=' + $(this).text() + '&media=json';
      $(this).attr('id', 'target');
      $.get($uri2, function(data) {
        bindEvent();
        $('#target').after(data).attr('id', '').attr('class', 'key open');
      });
    });
    bindEvent();
  });
});