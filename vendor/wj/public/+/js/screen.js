var huobiwanjia = function() {
  var screen = {
    query: null,
    ajaxQuery: null,
    suggestionQuery: null,
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
        clearInterval(screen.timer);
        screen.timer = null;
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
      if (suggestion.is(':visible')) {
        screen.checkSelection();
      }
      if (screen.timer === null) {
        screen.timer = setInterval(screen.startSuggestion, 1000);
      }
    }).focusin(function() {
      screen.timer = setInterval(screen.startSuggestion, 1000);
    }).focusout(function() {
      clearInterval(screen.timer);
      if (screen.isHoverSuggestion === false) {
        $('#suggestion').hide();
      }
    });
    /* ie 刷新后默认和当前页面 focus 状态一致 */
    if($.browser.msie) {
      $('#header input').blur();/* 如果遇到 ie6 的 bug，blur 失效 */
    }
  };

  screen.checkSelection = function() {
    var hover = $('#suggestion li.hover');
    if (hover.find('span:first').text() !== $.trim($('#header input').val())) {
      hover.removeClass('hover');
      screen.isHoverSuggestion = false;
    }
  };

  screen.startSuggestion = function() {
    if (screen.isHoverSuggestion) {
      return;
    }
    screen.query = $.trim($('#header input').val());
    if (screen.query === screen.suggestionQuery) {
      $('#suggestion').show();
      return;
    }
    var query = screen.query;
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
    $.each(data, function(text, amount) {
      if (text.replace(' ', '') === keywordList.replace(' ', '')) {
        return;
      }
      var innerHtml = text;
      if (keywordList !== '') {
        innerHtml = screen.highlight(text, keywordList.split(' '));
      }
      html += '<li><a href="/' + encodeURIComponent(text)
        + '/"><span class="query">' + innerHtml
        + '</span><span class="product_amount">' + amount + '</span></a></li>';
    });
    if (html === '') {
      suggestion.hide();
      return;
    }
    screen.suggestionQuery = screen.query;
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
    var length = keywordList.length;
    for (var index = 0; index < length; ++index) {
      var offset = 0;
      for(;;) {
        offset = query.indexOf(keywordList[index], offset);
        if (offset === -1) {
          break;
        }
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
    if (keyList.length === 0) {
      return query;
    }
    keyList.sort();
    var result = '';
    offset = 0;
    length = keyList.length;
    for (var index = 0; index < length; ++index) {
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
    return result + query.substr(offset);
  };

  screen.show = function() {
    if (screen.query !== screen.suggestionQuery) {
      return false;
    }
    $('#suggestion').show();
  };

  screen.move = function(target) {
    if (target.length === 0) {
      screen.isHoverSuggestion = false;
      $('#header input').val(screen.query);
      return;
    }
    screen.isHoverSuggestion = true;
    target.addClass('hover');
    $('#header input').val(target.find('span:first').text());
  };

  screen.up = function() {
    if (screen.show() === false) {
      return;
    }
    var current = $('#suggestion li.hover').removeClass('hover');
    var previous = current.prev();
    if (current.length === 0) {
      previous = $('#suggestion li').last();
    }
    screen.move(previous);
  };

  screen.down = function() {
    if (screen.show() === false) {
      return;
    }
    var current = $('#suggestion li.hover').removeClass('hover');
    var next = current.next();
    if (current.length === 0) {
      next = $('#suggestion li').first();
    }
    screen.move(next);
    return;
  };

  $(function() {
//    screen.query = $.trim($('#header input').val());
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