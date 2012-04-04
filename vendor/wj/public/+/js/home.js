huobiwanjia.home = function() {
  var home = {
    isScrolling: false,
    timer: null,
    holdCount: 0,
    currentMerchantIndex: 0,
    merchantListCache: [],
    slideCache: []
  };
  $(function() {
    home.pageAmount = Math.ceil(huobiwanjia.home.slideshow.merchantAmount / 5);
    home.page = typeof huobiwanjia.query.page === 'undefined' ?
      1 : huobiwanjia.query.page;
    home.merchantListCache[home.page] =
      huobiwanjia.home.slideshow.merchantList;
  });
  return home;
}();

/* focus input
 *****************************/
$(function() {
  $('#header input').focus();
});

/*
 * slide enhancement
 *****************************/
$(function() {
  $('#slide_wrapper').hover(huobiwanjia.home.hold, huobiwanjia.home.play);
  $('#slide').focusin(huobiwanjia.home.hold).focusout(huobiwanjia.home.play);
  $('#merchant').focusin(huobiwanjia.home.hold).focusout(huobiwanjia.home.play);
});

/*
 * slide list enhancement
 *****************************/
$(function() {
  $('#slide_list').children().each(function() {
    var current = $(this), classAttribute =
      current.attr('href') === undefined ? 'class="current"' : 'class="item"';
    current.replaceWith('<span ' + classAttribute + '></span>');
  });
  huobiwanjia.home.enhanceSlideList();
});

huobiwanjia.home.enhanceList = function(list, clickEvent) {
  var index = 0;
  list.each(function() {
    var current = $(this), currentIndex = index++;
    if (current.hasClass('current') === false) {
      current.attr('tabindex', '0');
    }
    //ie 点击时先触发 focus 然后触发 mousedown
    var isUp = null;
    current.mousedown(function() {
      isUp = false;
      current.addClass('active');
    });
    current.mouseout(function() {
      if (isUp === false) {
        current.removeClass('active');
        isUp = null;
      }
    });
    current.mouseup(function() {
      isUp = null;
    });
    current.focusin(huobiwanjia.home.hold).focusout(huobiwanjia.home.play);
    current.keypress(function(e) {
      if(e.which == 13){
        clickEvent(current, currentIndex);
      }
    });
    current.hover(
      function() {
        if (current.hasClass('current') === false) {
          current.addClass('hover');
        }
      },
      function() { current.removeClass('hover'); }
    ).click(function() { clickEvent(current, currentIndex); });
  });
};

huobiwanjia.home.enhanceSlideList = function() {
  huobiwanjia.home.enhanceList(
    $('#slide_list').children(), huobiwanjia.home.selectSlide
  );
};

/* select slide
 *****************************/
huobiwanjia.home.selectSlide = function(span, index) {
  $('#slide_list .current').attr('class', 'item')
    .attr('tabindex', 0);
  span.attr('class', 'current').removeAttr('tabindex');
  var merchant = huobiwanjia.home.slideshow.merchantList[
    huobiwanjia.home.currentMerchantIndex
  ];
  $('#slide img').attr('src', '/+/img/slide/' + merchant[2]
    + '/' + index + '.jpg');
  $('#slide').attr('href', 'http://' + merchant[3][index]);
};

/* merchant list enhancement
 *****************************/
$(function() {
  $('#merchant_list').children().each(function() {
    var current = $(this), classAttribute = ' class="item"';
    if (current.attr('href') === undefined) {
      classAttribute = ' class="current"';
    }
    current.replaceWith('<span ' + classAttribute
      + '><img src="/+/img/logo/360buy.png"/></span>');
  });
  huobiwanjia.home.enhanceMerchantList();
});

huobiwanjia.home.enhanceMerchantList = function() {
  huobiwanjia.home.enhanceList(
    $('#merchant_list').children(), huobiwanjia.home.selectMerchant
  );
};

huobiwanjia.home.selectMerchant = function(span, index) {
  if (huobiwanjia.home.isScrolling) {
    return;
  }
  huobiwanjia.home.currentMerchantIndex = index;
  var merchant = huobiwanjia.home.slideshow.merchantList[index],
    src = '/+/img/slide/' + merchant[2] + '/0.jpg';
  $('#merchant_list .current').attr('class', 'item')
    .attr('tabindex', 0);
  span.attr('class', 'current').removeAttr('tabindex');
  $('#merchant span').text(merchant[0]);
  $('#merchant').attr('href', 'http://' + merchant[1]);
  $('#slide img').attr('src', src);
  if ($.inArray(src, huobiwanjia.home.slideCache) === -1) {
    huobiwanjia.home.slideCache.push(src);
  };
  $('#slide').attr('href', 'http://' + merchant[3][0]);
  if (merchant[3].length === 1) {
    $('#slide_list').empty();
    return;
  }
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
    var classValue = $(this).attr('class'), classAttribute = '';
    if (typeof classAttribute !== 'undefined' && classAttribute !== false) {
      classAttribute = ' class="' + classValue + '"';
    }
    $(this).replaceWith('<span' + classAttribute + '></span>');
  });
  huobiwanjia.home.enhanceScroll();
});

huobiwanjia.home.enhanceScroll = function() {
  huobiwanjia.home.enhanceList(
    $('#scroll span'), huobiwanjia.home.executeScroll
  );
};

huobiwanjia.home.executeScroll = function(span) {
  var isPrevious = span.hasClass('previous');
  if (huobiwanjia.home.isScrolling) {
    return;
  }
  huobiwanjia.home.isScrolling = true;
  huobiwanjia.home.hold();
  ++huobiwanjia.home.page;
  if (isPrevious) {
    huobiwanjia.home.page -= 2;
  }
  $('#merchant_list .current').attr('class', 'item');
  var length = 5;
  if (huobiwanjia.home.page === huobiwanjia.home.pageAmount) {
    length = huobiwanjia.home.slideshow.merchantAmount
      - (huobiwanjia.home.page - 1) * 5;
  }
  var targetHtml = '';
  for (var index = 0; index < length; ++index) {
    targetHtml += '<span class="item"></span>';
  }
  var target = 'next',
    targetPosition = '-=70px',
    currentHtml = '<div id="current">' + $('#merchant_list').html() + '</div>',
    html = currentHtml + '<div id="next">' + targetHtml + '</div>';
  if (isPrevious) {
    target = 'previous';
    targetPosition = '+=70px';
    html = '<div id="previous">' + targetHtml + '</div>' + currentHtml;
  }
  $('#merchant_list').html(html).addClass('move');
  $('#current').animate({ 'top': targetPosition }, 'slow');
  $('#' + target).animate({ 'top': targetPosition }, 'slow', function() {
    $('#merchant_list').html($('#' + target).html()).removeClass('move');
    var html = '<span class="previous"></span><span></span>';
    if (huobiwanjia.home.page === 1) {
      html = '<span class="full"></span>';
    }
    if (huobiwanjia.home.page === huobiwanjia.home.pageAmount) {
      html = '<span class="previous full"></span>';
    }
    $('#scroll').html(html);
    huobiwanjia.home.enhanceScroll();
  });
  if (typeof huobiwanjia.home.merchantListCache[huobiwanjia.home.page]
    === 'undefined') {
    $.ajax({
      type: 'GET',
      url: '?page=' + huobiwanjia.home.page + '&media=json',
      success: function(data) {
        huobiwanjia.home.merchantListCache[huobiwanjia.home.page] = data;
        huobiwanjia.home.slideshow.merchantList = data;
        huobiwanjia.home.fillMerchantList();
      },
      error: function() {
        window.location = '?page=' + huobiwanjia.home.page;
      }
    });
    return;
  }
  huobiwanjia.home.slideshow.merchantList =
    huobiwanjia.home.merchantListCache[huobiwanjia.home.page];
  huobiwanjia.home.fillMerchantList();
};

huobiwanjia.home.fillMerchantList = function() {
  var html = '',
    length = huobiwanjia.home.slideshow.merchantList.length;
  for (var index = 0; index < length; ++index) {
    var merchant = huobiwanjia.home.slideshow.merchantList[index];
    html += '<span class="item"><img src="/+/img/logo/'
      + merchant[2] + '.png"/></span>';
  }
  var target = $('#merchant_list');
  if ($('#merchant_list div').length !== 0) {
    target = $('#merchant_list div');
  }
  target.html(html);
  huobiwanjia.home.afterFillMerchantList();
};

huobiwanjia.home.afterFillMerchantList = function() {
  if ($('#merchant_list div').length !== 0) {
    setTimeout(huobiwanjia.home.afterFillMerchantList, 100);
    return;
  }
  huobiwanjia.home.isScrolling = false;
  huobiwanjia.home.selectMerchant($('#merchant_list span').first(), 0);
  huobiwanjia.home.enhanceMerchantList();
  huobiwanjia.home.play();
};

/* auto play
 *****************************/
$(function() {
  huobiwanjia.home.play();
});

huobiwanjia.home.hold = function() {
  ++huobiwanjia.home.holdCount;
};

huobiwanjia.home.play = function() {
  if (huobiwanjia.home.holdCount > 0) {
    --huobiwanjia.home.holdCount;
    return;
  }
  if (huobiwanjia.home.isScrolling) {
    return;
  }
  if (huobiwanjia.home.timer !== null) {
    clearInterval(huobiwanjia.home.timer);
  }
  huobiwanjia.home.timer = setInterval(function() {
    if (huobiwanjia.home.holdCount > 0) {
      return;
    }
    var next = $('#merchant_list .current').next();
    if (next.length === 0) {
      next = $('#merchant_list span').first();
    }
    huobiwanjia.home.selectMerchant(next, $('#merchant_list span').index(next));
    huobiwanjia.home.preloadSlide();
  }, 500);
  huobiwanjia.home.preloadSlide();
};

huobiwanjia.home.preloadSlide = function() {
  var next = huobiwanjia.home.currentMerchantIndex + 1;
  if (typeof huobiwanjia.home.slideshow.merchantList[next] === 'undefined') {
    next = 1;
  }
  if (next === huobiwanjia.home.slideshow.currentMerchantIndex) {
    return;
  }
  var merchant = huobiwanjia.home.slideshow.merchantList[next],
    src = '/+/img/slide/' + merchant[2] + '/0.jpg';
  if ($.inArray(src, huobiwanjia.home.slideCache) === -1) {
    huobiwanjia.home.slideCache.push(src);
    new Image().src = src;
  }
};

/* merchant click tracking
 *****************************/
$(function() {
});