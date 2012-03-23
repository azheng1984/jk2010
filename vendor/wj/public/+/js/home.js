huobiwanjia.home = {};

/* focus input
 *****************************/
$(function() {
  $('#header input').focus();
});

/*
 * slide list
 *****************************/
$(function() {
  var index = 0;
  var span = $('#slide_list span');
  span.addClass('current').hover(function() {
    if ($(this).hasClass('current')) {
      return;
    }
    $(this).addClass('hover');
  }, function() {
    $(this).removeClass('hover');
  });
  var src = $('#slide img').attr('src');
  span.click(function() {
    if ($(this).hasClass('current')) {
      return;
    }
    $('#slide img').fadeOut("slow", function() {
      $(this).remove();
    });
    $('#slide').append('<img class="new" style="display:none" src="' + src + '"/>');
    $('#slide').attr('href', 'http://www.360buy.com/');
    $('#slide .new').fadeIn("slow");
    $('#slide_list .current').attr('class', 'item');
    $(this).attr('class', 'current');
  });
  $('#slide_list a').each(function() {
    var id = 'slide-' + index;
    $(this).replaceWith('<span id="' + id + '" class="item"></span>');
    var item = $('#' + id);
    item.hover(function() {
      if ($(this).hasClass('current')) {
        return;
      }
      $(this).addClass('hover');
    }, function() {
      $(this).removeClass('hover');
    });
    item.click(function() {
      if ($(this).hasClass('current')) {
        return;
      }
      $('#slide img').fadeOut("slow", function() {
        $(this).remove();
      });
//      $('#slide img').remove();
      $('#slide').append('<img class="new" style="display:none"'
        + ' src="http://img13.360buyimg.com/da/20120322/670_240_IGhuNK.jpg"/>');
      $('#slide .new').fadeIn('slow');
      $('#slide').attr('href', 'http://wop.360buy.com/p3243.html');
      $('#slide_list .current').attr('class', 'item');
      $(this).attr('class', 'current');
    });
    ++index;
  });
});

/* merchant list
 *****************************/
$(function() {
  var index = 0;
  var span = $('#merchant_list span');
  span.hover(function() {
    if ($(this).hasClass('current')) {
      return;
    }
    $(this).addClass('hover');
  }, function() {
    $(this).removeClass('hover');
  });
  span.click(function() {
    if ($(this).hasClass('current')) {
      return;
    }
    $('#merchant_list .current').attr('class', 'item');
    $(this).attr('class', 'current');
  });
  $('#merchant_list a').each(function() {
    var id = 'merchant-' + index;
    $(this).replaceWith('<span id="' + id + '" class="item">'
      + $(this).html() + '</span>');
    var item = $('#' + id);
    item.hover(function() {
      $(this).addClass('hover');
    }, function() {
      $(this).removeClass('hover');
    });
    item.click(function() {
      $('#merchant_list .current').attr('class', 'item');
      $(this).attr('class', 'current');
    });
    ++index;
  });
  $('#down').replaceWith('<span id="down" class="full"></span>');
  $('#down').hover(function() {
    $(this).addClass('hover');
  }, function() {
    $(this).removeClass('hover');
  });
  $('#down').click(function() {
    $('#merchant_list .current').attr('class', 'item');
    $('#merchant_list').css('position', 'relative');//修正 ie6 hidden 无效的 bug
    $('#merchant_list').css('overflow', 'hidden');
    $('#merchant_list').html('<div id="current">' + $('#merchant_list').html() +
      '</div><div id="next">'
        + '<a href="?merchant_id=1"><img src="/+/img/logo/360buy.png"/></a>'
        + '<a href="?merchant_id=1"><img src="/+/img/logo/360buy.png"/></a>'
        + '<a href="?merchant_id=1"><img src="/+/img/logo/360buy.png"/></a>'
        + '<a href="?merchant_id=1"><img src="/+/img/logo/360buy.png"/></a>'
        + '<a href="?merchant_id=1"><img src="/+/img/logo/360buy.png"/></a></div>'
    );
    $('#current').animate({"top":'-=69px'}, 'slow');
    $('#next').animate({'top':'-=69px'}, 'slow', function() {
      $('#merchant_list').html($('#next').html());
    });
    
  });
});

/* merchant click tracking
 *****************************/
$(function() {
  $('#slide').mouseup(function() {
    alert('tracking');
  });
  $('#merchant').mouseup(function() {
    alert('tracking');
  });
});