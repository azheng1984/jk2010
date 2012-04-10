huobiwanjia.home = function() {
  var home = {
    slideshow: null,
    page: 1,
    currentMerchantIndex: 0,
    isScrolling: false,
    timer: null,
    holdCount: 1,
    merchantListCache: [],
    slideCache: []
  };

  home.initializeList = function(id) {
    $('#' + id).children().each(function() {
      var self = $(this), classAttribute =
        self.attr('href') === undefined ? 'class="current"' : 'class="item"';
      self.replaceWith(
        '<span ' + classAttribute + '>' + self.html() + '</span>'
      );
    });
  };

  home.enhanceSlideList = function() {
    home.enhanceList($('#slide_list').children(), home.selectSlide);
  };

  home.enhanceList = function(list, clickEvent, isHold) {
    list.each(function(index) {
      var self = $(this);
      if (self.hasClass('current') === false) {
        self.attr('tabindex', '0');
      }
      self.mousedown(function() {
        self.addClass('active');
      }).mouseout(function() {
        self.removeClass('active');
      }).keypress(function(e) {
        if(e.which == 13) {
          clickEvent(self, index);
        }
      }).hover(
        function() {
          if (self.hasClass('current') === false) {
            self.addClass('hover');
          }
        },
        function() {self.removeClass('hover');}
      ).click(function() {clickEvent(self, index);});
      if (typeof isHold === 'undefined') {
        self.focusin(home.hold).focusout(home.play);
      }
    });
  };

  home.selectSlide = function(span, index) {
    if (span !== null) {
      $('#slide_list .current').attr('class', 'item').attr('tabindex', 0);
      span.attr('class', 'current').removeAttr('tabindex');
    }
    var merchant = home.slideshow.merchantList[home.currentMerchantIndex];
    var src = '/+/img/slide/' + merchant[2] + '/' + index + '.jpg';
    $('#slide img').attr('src', src);
    $('#slide').attr('href', 'http://' + merchant[3][index]);
    if (index === 0 && $.inArray(src, home.slideCache) === -1) {
      home.slideCache.push(src);
    }
  };

  home.enhanceMerchantList = function() {
    home.enhanceList(
      $('#merchant_list').children().hover(home.hold, home.play),
      home.selectMerchant
    );
  };

  home.selectMerchant = function(span, index) {
    if (home.isScrolling) {
      return;
    }
    home.currentMerchantIndex = index;
    var merchant = home.slideshow.merchantList[index];
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
    home.selectSlide(null, 0);
    home.enhanceSlideList();
  };

  home.enhanceScroll = function() {
    home.enhanceList(
      $('#scroll span'), home.executeScroll, false
    );
  };

  home.executeScroll = function(span) {
    var pageAmount = Math.ceil(home.slideshow.merchantAmount / 5),
      isPrevious = span.hasClass('previous');
    if (home.isScrolling) {
      return;
    }
    home.isScrolling = true;
    home.hold();
    ++home.page;
    if (isPrevious) {
      home.page -= 2;
    }
    $('#merchant_list .current').attr('class', 'item');
    var length = 5;
    if (home.page === pageAmount) {
      length = home.slideshow.merchantAmount - (home.page - 1) * 5;
    }
    var targetHtml = '';
    for (var index = 0; index < length; ++index) {
      targetHtml += '<span class="item"></span>';
    }
    var target = 'next',
      targetPosition = '-=70px',
      currentHtml = '<div id="current">'
        + $('#merchant_list').html() + '</div>',
      html = currentHtml + '<div id="next">' + targetHtml + '</div>';
    if (isPrevious) {
      target = 'previous';
      targetPosition = '+=70px';
      html = '<div id="previous">' + targetHtml + '</div>' + currentHtml;
    }
    $('#merchant_list').html(html).addClass('move');
    $('#current').animate({top: targetPosition}, 'slow');
    $('#' + target).animate({top: targetPosition}, 'slow', function() {
      $('#merchant_list').html($('#' + target).html()).removeClass('move');
      var html = '<span class="previous small"></span>'
        + '<span class="small"></span>';
      if (home.page === 1) {
        html = '<span></span>';
      }
      if (home.page === pageAmount) {
        html = '<span class="previous"></span>';
      }
      $('#scroll').html(html);
      home.enhanceScroll();
    });
    if (typeof home.merchantListCache[home.page] === 'undefined') {
      $.ajax({
        url: '?page=' + home.page + '&media=json',
        success: function(data) {
          home.merchantListCache[home.page] = data;
          home.fillMerchantList(data);
        },
        error: function() {
          window.location = '?page=' + home.page;
        }
      });
      return;
    }
    home.fillMerchantList(home.merchantListCache[home.page]);
  };

  home.fillMerchantList = function(data) {
    home.slideshow.merchantList = data;
    var html = '', length = home.slideshow.merchantList.length;
    for (var index = 0; index < length; ++index) {
      var merchant = home.slideshow.merchantList[index];
      html += '<span class="item"><img src="/+/img/logo/'
        + merchant[2] + '.png"/></span>';
    }
    var target = $('#merchant_list');
    if ($('#merchant_list div').length !== 0) {
      target = $('#merchant_list div');
    }
    target.html(html);
    home.afterFillMerchantList();
  };

  home.afterFillMerchantList = function() {
    if ($('#merchant_list div').length !== 0) {
      setTimeout(home.afterFillMerchantList, 100);
      return;
    }
    home.isScrolling = false;
    home.selectMerchant($('#merchant_list span').first(), 0);
    home.enhanceMerchantList();
    home.play();
  };

  home.hold = function() {
    ++home.holdCount;
  };

  home.play = function() {
    --home.holdCount;
    if (home.holdCount > 0) {
      return;
    }
    if (home.isScrolling) {
      return;
    }
    if (home.timer !== null) {
      clearInterval(home.timer);
    }
    home.timer = setInterval(function() {
      if (home.holdCount > 0) {
        return;
      }
      var next = $('#merchant_list .current').next();
      if (next.length === 0) {
        next = $('#merchant_list span').first();
      }
      home.selectMerchant(next, $('#merchant_list span').index(next));
      home.preloadSlide();
    }, 5000);
    home.preloadSlide();
  };

  home.preloadSlide = function() {
    var next = home.currentMerchantIndex + 1;
    if (typeof home.slideshow.merchantList[next] === 'undefined') {
      next = 1;
    }
    var src = '/+/img/slide/' + home.slideshow.merchantList[next][2] + '/0.jpg';
    if ($.inArray(src, home.slideCache) === -1) {
      home.slideCache.push(src);
      new Image().src = src;
    }
  };

  $(function() {
    if (typeof huobiwanjia.argumentList.page !== 'undefined') {
      home.page = huobiwanjia.argumentList.page;
    }
    home.merchantListCache[home.page] = home.slideshow.merchantList;
    home.slideCache.push($('#slide img').attr('src'));
    /* focus input */
    $('#header input').focus();
    /* slide enhancement */
    $('#slide_wrapper').hover(home.hold, home.play);
    $('#slide,#merchant').focusin(home.hold).focusout(home.play);
    /* slide list enhancement */
    home.initializeList('slide_list');
    home.enhanceSlideList();
    /* merchant list enhancement */
    home.initializeList('merchant_list');
    home.enhanceMerchantList();
    /* scroll enhancement */
    home.initializeList('scroll');
    home.enhanceScroll();
    /* auto play */
    home.play();
  });

  return home;
}();