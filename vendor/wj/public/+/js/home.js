huobiwanjia.home = function() {
  var home = {
    isScrollEnabled: true,
    isHold: false,
    currentMerchantIndex: 0,
    merchantListCache: [],
    slideCache: []
  };
  $(function() {
    home.pageAmount = Math.ceil(huobiwanjia.home.slideshow.merchantAmount / 5);
    home.page = typeof huobiwanjia.queryString.page === 'undefined' ?
      1 : huobiwanjia.queryString.page;
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

huobiwanjia.home.enhanceSlideList = function() {
  var index = 0;
  $('#slide_list').children().each(function() {
    var current = $(this), currentIndex = index++;
    current.hover(
      function() { if ($(this).hasClass('item')) $(this).addClass('hover'); },
      function() { $(this).removeClass('hover'); }
    ).click(function() {
      $('#slide_list .current').attr('class', 'item');
      $(this).attr('class', 'current');
      var merchant = huobiwanjia.home.slideshow.merchantList[
        huobiwanjia.home.currentMerchantIndex
      ];
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
  var index = 0;
  $('#merchant_list').children().each(function() {
    var current = $(this), currentIndex = index++;
    current.hover(
      function() {
        if ($(this).hasClass('item')) { 
          $(this).addClass('hover');
        }
        huobiwanjia.home.isHold = true;
      },
      function() {
        $(this).removeClass('hover');
        huobiwanjia.home.isHold = false;
      }
    ).click(function() {
      huobiwanjia.home.selectMerchant($(this), currentIndex);
    });
  });
};

huobiwanjia.home.selectMerchant = function(span, index) {
  if (huobiwanjia.home.isScrollEnabled === false) {
    return;
  }
  huobiwanjia.home.currentMerchantIndex = index;
  var merchant = huobiwanjia.home.slideshow.merchantList[index],
    src = '/+/img/slide/' + merchant[2] + '/0.jpg';
  $('#merchant_list .current').attr('class', 'item');
  span.attr('class', 'current');
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
    var classValue = $(this).attr('class'),
      classAttribute = '';
    if (typeof classAttribute !== 'undefined' && classAttribute !== false) {
      classAttribute = ' class="' + classValue + '"';
    }
    $(this).replaceWith('<span' + classAttribute + '></span>');
  });
  huobiwanjia.home.enhanceScroll();
});

huobiwanjia.home.enhanceScroll = function() {
  $('#scroll span').hover(
    function() { $(this).addClass('hover'); },
    function() { $(this).removeClass('hover'); }
  ).click(function() {
    if (huobiwanjia.home.isScrollEnabled === false) {
      return;
    }
    huobiwanjia.home.isScrollEnabled = false;
    huobiwanjia.home.stop();
    huobiwanjia.home.executeScroll($(this).hasClass('previous'));
    if (typeof huobiwanjia.home.merchantListCache[huobiwanjia.home.page]
      === 'undefined') {
      $.ajax({
        type: "GET",
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
  });
};

huobiwanjia.home.executeScroll = function(isPrevious) {
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
  huobiwanjia.home.afterScroll();
};

huobiwanjia.home.afterScroll = function() {
  if ($('#merchant_list div').length !== 0) {
    setTimeout(huobiwanjia.home.afterScroll, 100);
    return;
  }
  huobiwanjia.home.isScrollEnabled = true;
  huobiwanjia.home.selectMerchant($('#merchant_list span').first(), 0);
  huobiwanjia.home.enhanceMerchantList();
  huobiwanjia.home.play();
};

/* auto play
 *****************************/
$(function() {
  $('#slide_wrapper').hover(
    function() { huobiwanjia.home.isHold = true; },
    function() { huobiwanjia.home.isHold = false; }
  );
  huobiwanjia.home.play();
});

huobiwanjia.home.play = function() {
  if (huobiwanjia.home.timer !== -1) {
    huobiwanjia.home.stop();
  }
  huobiwanjia.home.timer = setInterval(function() {
    if (huobiwanjia.home.isHold) {
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

huobiwanjia.home.stop = function() {
  clearInterval(huobiwanjia.home.timer);
  huobiwanjia.home.timer = -1;
};

/* merchant click tracking
 *****************************/
$(function() {
});