var huobiwanjia = function() {
  var screen = {
    query: null,
    ajaxQuery: null,
    timer: null,
    isHoverSuggestion: false,
    suggestionCache: {},
  };

  screen.initializeSuggestion = function() {
    //ubuntu firefox 输入中文不会触发 keydown，不能用来检查数据
    $('#header input').keydown(function(event) {
      var suggestion = $('#suggestion');
      if (suggestion.length === 0) {
        return;
      }
      if (event.which === 27) {
        suggestion.hide();
        return;
      }
      if (event.which === 38) {
        screen.up();
        return false;
      }
      if (event.which === 40) {
        screen.down();
        return false;
      }
    }).focusin(function() {
      screen.timer = setInterval(screen.startSuggestion, 1000);
    }).focusout(function() {
      clearInterval(screen.timer);
      $('#suggestion').hide();
    });
    /* ie 刷新后默认和当前页面 focus 状态一致 */
    if($.browser.msie) {
      $('#header input').blur();/* 如果遇到 ie6 的 bug，blur 失效 */
    }
  };

  screen.startSuggestion = function() {
    var query = $.trim($('#header input').val());
    if (query === screen.query || screen.isHoverSuggestion) {
      return;
    }
    screen.query = query;
    if (query === '') {
      $('#suggestion').hide();
      return;
    }
    if (typeof screen.suggestionCache[query] !== 'undefined') {
      var cache = screen.suggestionCache[query];
      screen.renderSuggestion(cache[0], cache[1]);
      return;
    }
    if (screen.ajaxQuery === null) {
      screen.ajaxQuery = query;
      var uri = 'http://q.dev.huobiwanjia.com/' + encodeURIComponent(query);
      $.ajax({
        url: uri,
        cache: true,
        dataType: 'jsonp',
        jsonp: false
      });
    }
  };

  screen.suggest = function(keywordList, data) {
    screen.suggestionCache[screen.ajaxQuery] = [keywordList, data];
    if (screen.query === screen.ajaxQuery) {
      screen.renderSuggestion(keywordList, data);
    }
    screen.ajaxQuery = null;
  };

  screen.renderSuggestion = function(keywordList, data) {
    if (data === null) {
      data = {};
    }
    var suggestion = $('#suggestion');
    if (suggestion.length !== 1) {
      $('#header').append('<ul id="suggestion"></ul>');
      suggestion = $('#suggestion');
    }
    var html = '';
    console.log(data);
    $.each(data, function(text, amount) {
      if (text.replace(' ', '') === keywordList.replace(' ', '')) {
        return;
      }
      var liHtml = text;
      if (keywordList !== '') {
        liHtml = screen.highlight(text, keywordList.split(' '));
      }
      html += '<li><a href="/' + encodeURIComponent(text)
        + '/"><span class="query">' + liHtml
        + '</span><span class="product_amount">' + amount + '</span></a></li>';
    });
    if (html === '') {
      suggestion.hide();
      return;
    }
    suggestion.html(html).show();/* display block 触发 ie6 渲染 */
    $('#suggestion li a').hover(
      function() {
        $('#suggestion li.hover').removeClass('hover');
        $(this).parent().addClass('hover');
        screen.isHoverSuggestion = true;
      },
      function() {
        $('#suggestion li.hover').removeClass('hover');
        screen.isHoverSuggestion = false;
      }
    );
    //$('#suggestion').css('display', 'block');/* 触发 ie6 渲染 */
  };

  screen.highlight = function(query, keywordList) {
    var positionList = {};
    for (var index = 0; index < keywordList.length; ++index) {
      var offset = 0;
      while (-1 !== (offset = query.indexOf(keywordList[index], offset))) {
        if (typeof positionList[offset] === 'undefined') {
          positionList[offset] = keywordList[index].length;
        }
        offset = offset + keywordList[index].length;
      }
    }
    var keyList = [];
    for(var key in positionList) {
      keyList.push(key);
    }
    amount = keyList.length;
    if (amount === 0) {
      return query;
    }
    keyList.sort();
    var result = '';
    offset = 0;
    for (var index = 0; index < keyList.length; ++index) {
      var start = parseInt(keyList[index]);
      var next = start + positionList[start];
      if (next <= offset) {
        continue;
      }
      if (start < offset) {
        positionList[start] = positionList[start] + start - offset;
        start = offset;
      }
      result += query.substr(offset, start - offset) + '<em>'
        + query.substr(start, positionList[start]) + '</em>';
      offset = next;
    }
    if (offset < query.length) {
      result += query.substr(offset);
    }
    return result;
  };

  screen.up = function() {
    var target = null;
    var previous = null;
    $('#suggestion li').each(function() {
      if (target !== null) {
        return;
      }
      var current = $(this);
      if (current.hasClass('hover')) {
        current.removeClass('hover');
        if (previous == null) {
          target = false;
          return;
        }
        target = previous;
      }
      previous = current;
    });
    if (target == null) {
      target = previous;
    }
    if (target != false) {
      target.addClass('hover');
      hoverQuery = target.find('.query').text();
      $('#header input').val(hoverQuery);
      return;
    }
    hoverQuery = null;
    $('#header input').val(screen.query);
  };

  screen.down = function() {
    var current = null;
    $('#suggestion li').each(function() {
      if (current != null) {
        return;
      }
      if ($(this).hasClass('hover')) {
        current = $(this);
        current.removeClass('hover');
      }
    });
    var target = null;
    if (current == null) {
      target = $('#suggestion li').first();
    }
    if (target == null && current != null) {
      target = current.next();
    }
    if (target != null && target.length != 0) {
      target.addClass('hover');
      hoverQuery = target.find('.query').text();
      $('#header input').val(hoverQuery);
      return;
    }
    hoverQuery = null;
    $('#header input').val(screen.query);
  };

  $(function() {
    screen.query = $.trim($('#header input').val());
    $('#header input').attr('autocomplete', 'off');
    $('#header form').bind('submit', function() {
      var query = encodeURIComponent($.trim($('#header input').attr('value')))
        .replace(/%20/g, '+') + '/';
      if (query === '%2B/') {
        query = '';
      }
      if (query === '/') {
        query = '';
      }
      window.location = '/' + query;
      return false;
    });
    screen.initializeSuggestion();
  });

  return { screen: screen, argumentList: function() {
    var argumentList = {};
    var search = window.location.search;
    if (search !== '') {
      var queryString = search.charAt(0) === '?' ? search.substring(1) : search,
        regex = /([^=&]+)(=([^&]*))?/g;
      for (;;) {
        var match = regex.exec(queryString);
        if (match === null) {
          break;
        }
        argumentList[decodeURIComponent(match[1].replace(/\+/g,' '))] =
          decodeURIComponent(match[3].replace(/\+/g,' '));
      }
    }
    return argumentList;
  }() };
}();

/* tracking function
 *****************************/
huobiwanjia.trackPageview = function() {
  $.ajax({
    url:'http://tracking.' + document.domain + '/',
    cache:true,
    dataType:'jsonp',
    jsonp:false
  });
};

/* page tracking
 *****************************/
$(function() {
  //huobiwanjia.trackPageview();
});