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
  huobiwanjia.home.slideCache = [];
  huobiwanjia.home.pageAmount =
    Math.ceil(huobiwanjia.home.slideshow.merchantAmount / 5);
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
  var src = '/+/img/slide/' + merchant[2] + '/0.jpg';
  $('#slide img').attr('src', src);
  if ($.inArray(src, huobiwanjia.home.slideCache) === -1) {
    huobiwanjia.home.slideCache.push(src);
  };
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
  huobiwanjia.home.merchantListCache = [];
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
    huobiwanjia.home.executeScroll($(this).hasClass('previous'));
    if (typeof (huobiwanjia.home.merchantListCache[huobiwanjia.home.page])
      === 'undefined') {
      $.getJSON('?page=' + huobiwanjia.home.page + '&media=json',
        function(data) {
        huobiwanjia.home.slideshow.merchantList = data;
        huobiwanjia.home.fillMerchantList();
      });
      return;
    }
    huobiwanjia.home.slideshow.merchantList =
      huobiwanjia.home.merchantListCache[huobiwanjia.home.page];
    huobiwanjia.home.fillMerchantList();
  });
};

huobiwanjia.home.executeScroll = function(isPrevious) {
  huobiwanjia.home.merchantListCache[huobiwanjia.home.page] =
    huobiwanjia.home.slideshow.merchantList;
  ++huobiwanjia.home.page;
  if (isPrevious) {
    huobiwanjia.home.page -= 2;
  }
  $('#merchant_list').addClass('move');
  $('#merchant_list .current').attr('class', 'item');
  var length = 5;
  if (huobiwanjia.home.page === huobiwanjia.home.pageAmount) {
    length = huobiwanjia.home.slideshow.merchantAmount
      - (huobiwanjia.home.page - 1) * 5;
  }
  var next = '';
  for (var index = 0; index < length; ++index) {
    next = '<span class="item"></span>' + next;
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
  $('#current').animate({'top':targetPosition}, 'slow');
  $('#' + target).animate({'top':targetPosition}, 'slow', function() {
    $('#merchant_list').html($('#' + target).html());
    $('#merchant_list').removeClass('move');
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
  var html = '';
  for (var index = 0; index < huobiwanjia.home.slideshow.merchantList.length; ++index) {
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
  huobiwanjia.home.selectMerchant($('#merchant_list span').first(), 0);
  huobiwanjia.home.enhanceMerchantList();
  huobiwanjia.home.play();
  huobiwanjia.home.isScrollEnabled = true;
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
  if (huobiwanjia.home.isEnableScroll === false) {
    return;
  }
  if (huobiwanjia.home.timer !== -1) {
    huobiwanjia.home.stop();
  }
  huobiwanjia.home.timer = setInterval(function() {
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
  var next = huobiwanjia.home.slideshow.currentMerchantIndex + 1;
  if (typeof huobiwanjia.home.slideshow.merchantList[next] === 'undefined') {
    next = 1;
  }
  if (next === huobiwanjia.home.slideshow.currentMerchantIndex) {
    return;
  }
  var merchant = huobiwanjia.home.slideshow.merchantList[next];
  var src = '/+/img/slide/' + merchant[2] + '/0.jpg';
  if ($.inArray(src, huobiwanjia.home.slideCache) === -1) {
    huobiwanjia.home.slideCache.push(src);
    new Image().src = '/+/img/slide/' + merchant[2] + '/0.jpg';
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