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
  huobiwanjia.home.initializeList($('#slide_list').children());
  huobiwanjia.home.enhanceSlideList();
});

huobiwanjia.home.initializeList = function(list) {
  list.each(function() {
    var current = $(this), classAttribute =
      current.attr('href') === undefined ? 'class="current"' : 'class="item"';
    current.replaceWith(
      '<span ' + classAttribute + '>' + current.html() + '</span>'
    );
  });
};

huobiwanjia.home.enhanceList = function(list, clickEvent, isHold) {
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
    }).mouseout(function() {
      if (isUp === false) {
        current.removeClass('active');
        isUp = null;
      }
    }).mouseup(function() {
      isUp = null;
    }).keypress(function(e) {
      if(e.which == 13){
        clickEvent(current, currentIndex);
      }
    }).hover(
      function() {
        if (current.hasClass('current') === false) {
          current.addClass('hover');
        }
      },
      function() { current.removeClass('hover'); }
    ).click(function() { clickEvent(current, currentIndex); });
    if (typeof isHold === 'undefined') {
      current.focusin(huobiwanjia.home.hold).focusout(huobiwanjia.home.play);
    }
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
  if (span !== null) {
    $('#slide_list .current').attr('class', 'item').attr('tabindex', 0);
    span.attr('class', 'current').removeAttr('tabindex');
  }
  var merchant = huobiwanjia.home.slideshow.merchantList[
    huobiwanjia.home.currentMerchantIndex
  ];
  var src = '/+/img/slide/' + merchant[2] + '/' + index + '.jpg';
  $('#slide img').attr('src', src);
  $('#slide').attr('href', 'http://' + merchant[3][index]);
  if (index === 0 && $.inArray(src, huobiwanjia.home.slideCache) === -1) {
    huobiwanjia.home.slideCache.push(src);
  }
};

/* merchant list enhancement
 *****************************/
$(function() {
  huobiwanjia.home.initializeList($('#merchant_list').children());
  huobiwanjia.home.enhanceMerchantList();
});

huobiwanjia.home.enhanceMerchantList = function() {
  var list = $('#merchant_list').children();
  huobiwanjia.home.enhanceList(list, huobiwanjia.home.selectMerchant);
  list.children().each(function() {
    $(this).hover(huobiwanjia.home.hold, huobiwanjia.home.play);
  });
};

huobiwanjia.home.selectMerchant = function(span, index) {
  if (huobiwanjia.home.isScrolling) {
    return;
  }
  huobiwanjia.home.currentMerchantIndex = index;
  var merchant = huobiwanjia.home.slideshow.merchantList[index];
  $('#merchant_list .current').attr('class', 'item').attr('tabindex', 0);
  span.attr('class', 'current').removeAttr('tabindex');
  $('#merchant span').text(merchant[0]);
  $('#merchant').attr('href', 'http://' + merchant[1]);
  var html = '';
  if (merchant[3].length > 1) {
    html = '<span class="current"></span>';
    for (var count = merchant[3].length - 1; count > 0; --count) {
      html += '<span class="item"></span>';
    }
  }
  $('#slide_list').html(html);
  huobiwanjia.home.selectSlide(null, 0);
  huobiwanjia.home.enhanceSlideList();
};

/* scroll enhancement
 *****************************/
$(function() {
  huobiwanjia.home.initializeList($('#scroll a'));
  huobiwanjia.home.enhanceScroll();
});

huobiwanjia.home.enhanceScroll = function() {
  huobiwanjia.home.enhanceList(
    $('#scroll span'), huobiwanjia.home.executeScroll, false
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
    var html = '<span class="previous small"></span>'
      + '<span class="small"></span>';
    if (huobiwanjia.home.page === 1) {
      html = '<span></span>';
    }
    if (huobiwanjia.home.page === huobiwanjia.home.pageAmount) {
      html = '<span class="previous"></span>';
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
        huobiwanjia.home.fillMerchantList(data);
      },
      error: function() {
        window.location = '?page=' + huobiwanjia.home.page;
      }
    });
    return;
  }
  huobiwanjia.home.fillMerchantList(
    huobiwanjia.home.merchantListCache[huobiwanjia.home.page]
  );
};

huobiwanjia.home.fillMerchantList = function(data) {
  huobiwanjia.home.slideshow.merchantList = data;
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
  }, 5000);
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