function bindEvent() {
  $('#value_list li').hover(
      function() {
        $(this).attr('class', 'current');
        $('#value_list .current .delete').show();
      },function() {
        $('#value_list .current .delete').hide();
        $(this).attr('class', '');
    }
  );
  /*
  $('#category').mouseup(function() {
    $uri3 = window.location.pathname + '?anchor=' + $(this).attr('href').replace('#', '') + '&media=json';
    $.get($uri3, function(data) {
      $('#filter').html(data);
      bindEvent();
    });
  });
  $('#key').mouseup(function() {
    $uri4 = window.location.pathname + '?media=json';
    $.get($uri4, function(data) {
      $('#filter').html(data);
      bindEvent();
    });
  });
  $('#category_list a').mouseup(function() {
    if ($(this).attr('href') == '#') {
      $uri5 = window.location.pathname + '?media=json';
      $.get($uri5, function(data) {
        $('#filter').html(data);
        bindEvent();
      });
    }
  });
  */
}

$(function() {
  $uri = window.location.pathname + '?';
  if (window.location.hash != '') {
    $uri += 'anchor=' + window.location.hash.replace('#', '') + '&';
  }
  $uri += 'media=json';
  $.get($uri, function(data) {
    $('#filter').html(data);
    $('#key_list .key').mouseup(function() {
      $uri2 = window.location.pathname + '?anchor=' + $(this).text() + '&media=json';
      $(this).attr('id', 'target');
      $.get($uri2, function(data) {
        $('#target').after(data).attr('id', '').css('background', "url('/minus.png') no-repeat");
        //alert(data);
        //alert($(this).text());
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
  $('#input_start').focusin(function() {
    $('#start_img').attr('src', "/slider_active.png");
    $('#end_img').attr('src', "/slider.png");
    $('#option .cursor').show();
  });
  $('#input_end').focusin(function() {
    $('#start_img').attr('src', "/slider.png");
    $('#end_img').attr('src', "/slider_active.png");
    $('#option .cursor').show();
  });
  $('#option input').focusout(function() {
    $('#option .cursor').hide();
  });

});
