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
  huobiwanjia.home.slideshow.currentMerchantIndex = 0;
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
      $(this).attr('class', 'current');
      var merchant = huobiwanjia.home.slideshow.merchantList
        [huobiwanjia.home.slideshow.currentMerchantIndex];
      $('#slide img').attr('src', '/+/img/slide/' + merchant[2]
        + '/' + currentIndex + '.jpg');
      $('#slide').attr('href', 'http://' + merchant[3][currentIndex]);
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
      function() {
        if ($(this).hasClass('item')) { 
          $(this).addClass('hover');
        }
        huobiwanjia.home.stop();
      },
      function() {
        $(this).removeClass('hover');
        huobiwanjia.home.play();
      }
    );
    current.click(function() {
      huobiwanjia.home.selectMerchant($(this), currentIndex);
    });
  });
};

huobiwanjia.home.selectMerchant = function(span, index) {
  huobiwanjia.home.slideshow.currentMerchantIndex = index;
  var merchant = huobiwanjia.home.slideshow.merchantList[index];
  $('#merchant_list .current').attr('class', 'item');
  span.attr('class', 'current');
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
  $('#scroll a').each(function() {
    var classValue = $(this).attr('class');
    var classAttribute = '';
    if (typeof classAttribute !== 'undefined' && classAttribute !== false) {
      classAttribute = ' class="' + classValue + '"';
    }
    $(this).replaceWith('<span' + classAttribute + '></span>');
  });
  huobiwanjia.home.isScrollEnabled = true;
  huobiwanjia.home.page = typeof huobiwanjia.queryString.page === 'undefined' ?
    1 : huobiwanjia.queryString.page;
  huobiwanjia.home.enhanceScroll();
});

huobiwanjia.home.enhanceScroll = function() {
  $('#scroll span').hover(
    function() { $(this).addClass('hover'); },
    function() { $(this).removeClass('hover'); }
  );
  $('#scroll span').click(function() {
    if (huobiwanjia.home.isScrollEnabled === false) {
      return;
    }
    huobiwanjia.home.isScrollEnabled = false;
    huobiwanjia.home.stop();
    $('#merchant_list').addClass('move');
    $('#merchant_list .current').attr('class', 'item');
    var isPrevious = $(this).hasClass('previous');
    var next = '';
    for (var index = 0; index < huobiwanjia.home.slideshow.merchantList.length; ++index) {
      next = '<span class="item"><img src="/+/img/logo/'
        + huobiwanjia.home.slideshow.merchantList[index][2] + '.png"/></span>' + next;
    }
    var targetPosition = '-=70px';
    var target = 'next';
    if (isPrevious) {
      target = 'previous';
      $('#merchant_list').html('<div id="previous">' + next + '</div><div id="current">' + $('#merchant_list').html()
          + '</div>'
        );
      targetPosition = '+=70px';
    } else {
      $('#merchant_list').html('<div id="current">' + $('#merchant_list').html()
          + '</div><div id="next">' + next + '</div>'
        );
    }
    ++huobiwanjia.home.page;
    if (isPrevious) {
      huobiwanjia.home.page -= 2;
    }
    //TODO:js based list cache
    $.getJSON('?page=' + huobiwanjia.home.page + '&media=json', function(data) {
      //TODO:整体替换 target merchant list
    });
    $('#current').animate({"top":targetPosition}, 'slow');
    $('#' + target).animate({'top':targetPosition}, 'slow', function() {
      $('#merchant_list').html($('#' + target).html());
      $('#merchant_list').removeClass('move');
      huobiwanjia.home.selectMerchant($('#merchant_list span').first(), 0);
      huobiwanjia.home.play();
      huobiwanjia.home.enhanceMerchantList();
      huobiwanjia.home.isScrollEnabled = true;
      //TODO:根据总页和当前页拼接
      $('#scroll').html(
        '<span class="previous"></span><span></span>'
      );
      huobiwanjia.home.enhanceScroll();
    });
  });
};

/* auto play
 *****************************/
$(function() {
  $('#slide_wrapper').hover(
    function() { huobiwanjia.home.stop(); },
    function() { huobiwanjia.home.play(); }
  );
  huobiwanjia.home.play();
});

huobiwanjia.home.play = function() {
  huobiwanjia.home.timer = setInterval(function() {
    var next = $('#merchant_list .current').next();
    if (next.length === 0) {
      next = $('#merchant_list span').first();
    }
    //TODO:pre load first slide of next merchant
    huobiwanjia.home.selectMerchant(next, $('#merchant_list span').index(next));
  }, 5000);
};

huobiwanjia.home.stop = function() {
  clearInterval(huobiwanjia.home.timer);
};

/* merchant click tracking
 *****************************/
$(function() {
});