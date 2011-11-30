$(function() {
  $uri = window.location.pathname + '?anchor=' + window.location.hash.replace('#', '') + '&media=json';
  $.get($uri, function(data) {
    $('#filter').html(data);
    //$('.result').html(data);
    //alert('Load was performed.');
  });
  var isHover = false;
  $('#result ol li').hover(function() {
    if (isHover == true) {
      return;
    }
    isHover = true;
    $(this).append('<div id="hover"><div class="toolbar"><a href="javascript:void(0)">同款</a> <a href="javascript:void(0)">关注</a></div><div class="tag"><a href="javascript:void(0)">分类: 手机</a></div>'
    + '</div>');
    //$('#hover').fadeIn();
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
  $('#input_start').click(function() {
    $('#start_img').attr('src', "/slider_active.png");
    $('#end_img').attr('src', "/slider.png");
    $('#option .cursor').show();
  });
  $('#input_end').click(function() {
    $('#start_img').attr('src', "/slider.png");
    $('#end_img').attr('src', "/slider_active.png");
    $('#option .cursor').show();
  });
  $('#option input').focusout(function() {
    $('#option .cursor').hide();
  });
});
