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
  $('#slide_list').children().each(function() {
    var current = $(this);
    var classAttribute = 'class="item"';
    if (current.attr('href') === undefined) {
      classAttribute = 'class="current"';
    }
    current.replaceWith('<span ' + classAttribute + '></span>');
  });
  huobiwanjia.home.enhanceSlideList();
});
huobiwanjia.home.enhanceSlideList = function() {
  var index = 0;
  $('#slide_list').children().each(function() {
    var current = $(this);
    var currentIndex = index;
    ++index;
    current.hover(
      function() { if ($(this).hasClass('item')) $(this).addClass('hover'); },
      function() { $(this).removeClass('hover'); }
    );
    current.click(function() {
      $('#slide_list .current').attr('class', 'item');
      var merchant = huobiwanjia.home.slideshow[currentIndex];
      $('#slide img').attr('src',
        '/+/img/slide/' + merchant[2] + '/' + currentIndex + '.jpg');
      $('#slide').attr('href', 'http://'
        + huobiwanjia.home.slideshow[0][3][currentIndex]);
      $(this).attr('class', 'current');
    });
  });
};

/* merchant list enhancement
 *****************************/
$(function() {
  $('#merchant_list').children().each(function() {
    var current = $(this);
    var classAttribute = ' class="item"';
    if (current.attr('href') === undefined) {
      classAttribute = ' class="current"';
    }
    current.replaceWith('<span ' + classAttribute
      + '><img src="/+/img/logo/360buy.png"/></span>');
  });
  huobiwanjia.home.enhanceMerchantList();
});
huobiwanjia.home.enhanceMerchantList = function() {
  var index = 0;
  $('#merchant_list').children().each(function() {
    var current = $(this);
    var currentIndex = index;
    ++index;
    current.hover(
      function() { if ($(this).hasClass('item')) $(this).addClass('hover'); },
      function() { $(this).removeClass('hover'); }
    );
    current.click(function() {
      $('#merchant_list .current').attr('class', 'item');
      $(this).attr('class', 'current');
      huobiwanjia.home.selectMerchant(currentIndex);
    });
  });
};
huobiwanjia.home.selectMerchant = function(index) {
  var merchant = huobiwanjia.home.slideshow[index];
  $(this).attr('class', 'current');
  $('#merchant span').html(merchant[0]);
  $('#merchant').attr('href', merchant[1]);
  $('#slide img').attr('src', '/+/img/slide/' + merchant[2] + '/0.jpg');
  $('#slide').attr('href', merchant[3][0]);
  var html = '<span class="current"></span>';
  for (var count = merchant[3].length - 1; count > 0; --count) {
    html += '<span class="item"></span>';
  }
  $('#slide_list').html(html);
  huobiwanjia.home.enhanceSlideList();
};

/* scroll enhancement
 *****************************/
$(function() {
  $('#scroll a').replaceWith('<span id="next" class="full"></span>');//TODO:考虑有 previous 情况
  huobiwanjia.home.isMoving = false;
  $('.full').hover(
    function() { $(this).addClass('hover'); },
    function() { $(this).removeClass('hover'); }
  );
  $('.full').click(function() {
    if (huobiwanjia.home.isMoving) {
      return;
    }
    huobiwanjia.home.isMoving = true;
    //TODO:ajax & cache & prev
    $('#merchant_list').addClass('move');
    $('#merchant_list .current').attr('class', 'item');
    var next = '';
    for (var index = 0; index < huobiwanjia.home.slideshow.length; ++index) {
      next = '<span class="item"><img src="/+/img/logo/'
        + huobiwanjia.home.slideshow[index][2] + '.png"/></span>' + next;
    }
    $('#merchant_list').html('<div id="current">' + $('#merchant_list').html()
      + '</div><div id="next_list">' + next + '</div>'
    );
    $('#current').animate({"top":'-=70px'}, 'slow');
    $('#next_list').animate({'top':'-=70px'}, 'slow', function() {
      $('#merchant_list').html($('#next_list').html());
      $('#merchant_list span').first().attr('class', 'current');
      $('#merchant_list').removeClass('move');
      huobiwanjia.home.selectMerchant(0);
      huobiwanjia.home.enhanceMerchantList();
      huobiwanjia.home.isMoving = false;
      //TODO:enhance scroll
      $('#scroll').html('<span id="previous"></span><span id="next"></span>');
    });
  });
});

/* merchant click tracking
 *****************************/
$(function() {
});