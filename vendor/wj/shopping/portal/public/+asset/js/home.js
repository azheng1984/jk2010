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
      var self = $(this), classList = [], classAttr = self.attr('class');
      if (classAttr !== undefined) {
        classList = [classAttr];
      }
      self.attr('href') === undefined ?
        classList.push('current') : classList.push('item');
      self.replaceWith(
        '<span class="' + classList.join(' ') + '">' + self.html() + '</span>'
      );
    });
  };

  home.enhanceSlideList = function() {
    home.enhanceList($('#slide_list').children(), home.selectSlide);
    $('#next img').hover(function() {$(this).attr('src', '/+/img/arrow_hover.png');}, function() {$(this).attr('src', '/+/img/arrow.png')});
  };

  home.enhanceList = function(list, click, isHold) {
    list.each(function(index) {
      var self = $(this);
      if (self.is('.current') === false) {
        self.attr('tabindex', '0');
      }
      self.mousedown(function() {
        self.addClass('no_outline').attr('hideFocus', true);//ie 6 不支持 outline
      }).mouseout(function() {
        self.removeClass('no_outline').removeAttr('hideFocus');
      }).keypress(function(e) {
        if(e.which == 13) {
          click(self, index);
        }
      }).hover(
        function() {
          if (self.is('.current') === false) {
            self.addClass('hover');
          }
        },
        function() {self.removeClass('hover');}
      ).click(function() {click(self, index);});
      if (isHold === undefined) {
        self.focusin(home.hold).focusout(home.play);
      }
    });
  };

  home.selectSlide = function(span, index) {
    if (span !== null) {
      $('#slide_list .current').attr('class', 'item').attr('tabindex', 0);
      span.attr('class', 'current no_outline').removeAttr('tabindex');
    }
    var merchant = home.slideshow.merchantList[home.currentMerchantIndex],
      src = '/+/img/slide/' + merchant[2] + '/' + index + '.jpg';
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
    $('#merchant_list').children().focus(home.hold);
  };

  home.selectMerchant = function(span, index) {
    if (home.isScrolling) {
      return;
    }
    home.currentMerchantIndex = index;
    var merchant = home.slideshow.merchantList[index];
    $('#merchant_list .current').attr('class', 'item').attr('tabindex', 0);
    span.attr('class', 'current').removeAttr('tabindex');
    $('#merchant').html('<span id="merchant_name">' + merchant[0] + '</span> <a target="_blank" rel="nofollow">去逛逛 ›</a><span id="slide_list"></span></span>');
    $('#merchant a').attr('href', 'http://' + merchant[1]);
    var html = '';
    if (merchant[3].length > 1) {
      html = '<span class="current">1</span>';
      var length = merchant[3].length;
      for (var index = 2; index <= length; ++index) {
        html += '<span class="item">' + index + '</span>';
      }
    }
    $('#slide_list').html(html);
    home.selectSlide(null, 0);
    home.enhanceSlideList();
  };

  home.enhanceScroll = function() {
    var list = $('#scroll span');
    home.enhanceList(list, home.executeScroll, false);
    var classList = ['previous', 'previous_small', 'small'];
    list.hover(function() {
      var self = $(this);
      $.each(classList, function(index, item) {
        if (self.hasClass(item)) {
          self.addClass(item + '_hover');
          return false;
        }
      });
    }, function() {
      var self = $(this);
      $.each(classList, function(index, item) {
        item = item + '_hover';
        if (self.hasClass(item)) {
          self.removeClass(item);
          return false;
        }
      });
    });
  };

  home.executeScroll = function(span) {
    var pageAmount = Math.ceil(home.slideshow.merchantAmount / 5),
      isPrevious = span.is('.previous, .previous_small');
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
      var html = '<span class="small previous_small"></span>'
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
    var html = '';
    $.each(home.slideshow.merchantList, function(index, merchant) {
      html += '<span class="item"><img src="/+/img/logo/'
        + merchant[2] + '.png"/></span>';
    });
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
    //home.play();

    $('#slogon a').each(function() {
      $(this).hover(function() {$(this).next().addClass('span_over')},function() {$(this).next().removeClass('span_over')});
    });
  });

  return home;
}();
