huobiwanjia.home = {};

/* focus input
 *****************************/
$(function() {
  $('#header input').focus();
});

/*
 * slide list enhancement
 *****************************/
$(function() {
  var index = 0;
  $('#slide_list').children().each(function() {
    var current = $(this);
    var currentIndex = index;
    ++index;
    var classAttribute = ' class="item"';
    if (current.attr('href') === undefined) {
      classAttribute = ' class="current"';
    }
    var id = 'slide_' + index;
    current.replaceWith('<span id="' + id + '"' + classAttribute + '></span>');
    $('#' + id).hover(
      function() { if ($(this).hasClass('item')) $(this).addClass('hover'); },
      function() { $(this).removeClass('hover'); }
    );
    $('#' + id).click(function() {
      $('#slide_list .current').attr('class', 'item');
      var src = $('#slide img').attr('src')
        .replace(/^(.*)\/(.*?)\.(png|jpg)$/, '$1/' + currentIndex + '.$3');
      $('#slide img').attr('src', src);
      $(this).attr('class', 'current');
    });
  });
});

/* merchant list enhancement
 *****************************/
$(function() {
  var index = 0;
  $('#merchant_list').children().each(function() {
    var current = $(this);
    //var currentIndex = index;
    ++index;
    var classAttribute = ' class="item"';
    if (current.attr('href') === undefined) {
      classAttribute = ' class="current"';
    }
    var id = 'merchant_' + index;
    current.replaceWith('<span id="' + id + '"' + classAttribute + '><img src="/+/img/logo/360buy.png"/></span>');
    $('#' + id).hover(
      function() { if ($(this).hasClass('item')) $(this).addClass('hover'); },
      function() { $(this).removeClass('hover'); }
    );
    $('#' + id).click(function() {
      $('#merchant_list .current').attr('class', 'item');
      $(this).attr('class', 'current');
    });
  });
});

/* scroll enhancement
 *****************************/
$(function() {
  $('#scroll a').replaceWith('<span class="full"></span>');
  $('.full').hover(
    function() { $(this).addClass('hover'); },
    function() { $(this).removeClass('hover'); }
  );
  $('.full').click(function() {
    $('#merchant_list').addClass('move');
    $('#merchant_list .current').attr('class', 'item');
    $('#merchant_list').html(
      '<div id="current">' + $('#merchant_list').html() + '</div><div id="next">'
      + '<span class="item"><img src="/+/img/logo/360buy.png"/></span>'
      + '<a href="?merchant_id=1"><img src="/+/img/logo/360buy.png"/></a>'
      + '<a href="?merchant_id=1"><img src="/+/img/logo/360buy.png"/></a>'
      + '<a href="?merchant_id=1"><img src="/+/img/logo/360buy.png"/></a>'
      + '<a href="?merchant_id=1"><img src="/+/img/logo/360buy.png"/></a></div>'
    );
    $('#current').animate({"top":'-=70px'}, 'slow');
    $('#next').animate({'top':'-=70px'}, 'slow', function() {
      $('#merchant_list').html($('#next').html());
      $('#merchant_list span').attr('class', 'current');
      $('#merchant_list').removeClass('move');
    });
  });
});

/* merchant click tracking
 *****************************/
$(function() {
});