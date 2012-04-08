var huobiwanjia = function() {
  var suggestion = {
    query: null,
    ajaxQuery: null,
    timerQuery: null,
    timer: null,
    isPreventHidden: false,
    isHidden: false,
    isHover: false,
    cache: {},
  };

  suggestion.initialize = function() {
    $('#header input').attr('autocomplete', 'off');
    //ubuntu firefox 输入中文不会触发 keydown
    $('#header input').keydown(function(event) {
      var wrapper = $('#suggestion');
      if (wrapper.length === 0) {
        return;
      }
      if (event.which === 27) {
        suggestion.hide();
        suggestion.isHidden = true;
        return;
      }
      if (event.which === 38) {
        suggestion.move(suggestion.up);
        return false;
      }
      if (event.which === 40) {
        suggestion.move(suggestion.down);
        return false;
      }
      if (wrapper.is(':visible')) {
        suggestion.check();
      }
      suggestion.isHidden = false;
    }).focusin(function() {
      //chrome 在 window 获得焦点时会触发两次 focusin
      if (suggestion.timer === null) {
        suggestion.timer = setInterval(suggestion.start, 1000);
      }
      suggestion.isHidden = false;
    }).focusout(function() {
      clearInterval(suggestion.timer);
      suggestion.timer = null;
      if (suggestion.isPreventHidden === false) {
        suggestion.hide();
      }
    });
    /* ie 刷新后默认和当前页面 focus 状态一致 */
    if($.browser.msie) {
      $('#header input').blur();/* 如果遇到 ie6 的 bug，blur 失效 */
    }
  };

  suggestion.check = function() {
    if ($.trim($('#header input').val()) !== suggestion.query) {
      suggestion.hide();
    }
  };

  suggestion.hide = function() {
    $('#suggestion').hide();
    $('#suggestion li.hover').removeClass('hover');
    suggestion.isHover = false;
  };

  suggestion.start = function() {
    if (suggestion.isHover || suggestion.isHidden) {
      return;
    }
    suggestion.timerQuery = $.trim($('#header input').val());
    if (suggestion.timerQuery === suggestion.query) {
      $('#suggestion').show();
      return;
    }
    var query = suggestion.timerQuery;
    if (query === '') {
      suggestion.hide();
      return;
    }
    if (typeof suggestion.cache[query] !== 'undefined') {
      var cache = suggestion.cache[query];
      suggestion.render(query, cache[0], cache[1]);
      return;
    }
    if (suggestion.ajaxQuery === null) {
      suggestion.ajaxQuery = query;
      var uri = 'http://q.dev.huobiwanjia.com/' + encodeURIComponent(query);
      $.ajax({
        url: uri,
        cache: true,
        dataType: 'jsonp',
        jsonp: false
      });
    }
  };

  suggestion.execute = function(keywordList, data) {
    suggestion.cache[suggestion.ajaxQuery] = [keywordList, data];
    if ($.trim($('#header input').val()) === suggestion.ajaxQuery) {
      suggestion.render(suggestion.ajaxQuery, keywordList, data);
    }
    suggestion.ajaxQuery = null;
  };

  suggestion.render = function(query, keywordList, data) {
    if (data === null) {
      data = {};
    }
    var wrapper = $('#suggestion');
    if (wrapper.length !== 1) {
      $('#header').append('<ul id="suggestion"></ul>');
      wrapper = $('#suggestion');
    }
    var html = '';
    $.each(data, function(text, amount) {
      if (text.replace(/ /g, '') === keywordList.replace(/ /g, '')) {
        return;
      }
      var innerHtml = text;
      if (keywordList !== '') {
        innerHtml = suggestion.highlight(text, keywordList.split(' '));
      }
      html += '<li><a href="/' + encodeURIComponent(text)
        + '/"><span class="query">' + innerHtml
        + '</span><span class="product_amount">' + amount + '</span></a></li>';
    });
    if (html === '') {
      suggestion.hide();
      return;
    }
    suggestion.query = query;
    wrapper.html(html).show();/* display block 触发 ie6 渲染 */
    $('#suggestion li a').hover(
      function() {
        $('#suggestion li.hover').removeClass('hover');
        $(this).parent().addClass('hover');
        suggestion.isPreventHidden = true;
        suggestion.isHover = true;
      },
      function() {
        $('#suggestion li.hover').removeClass('hover');
        suggestion.isPreventHidden = false;
        suggestion.isHover = false;
      }
    ).click(function() {
      //firefox 后退时直接还原跳转前的页面
      suggestion.hide();
    });
  };

  suggestion.highlight = function(query, keywordList) {
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

  suggestion.move = function(getTarget) {
    if ($('#suggestion').is(':visible') === false) {
      return;
    }
    var target = getTarget();
    target.from.removeClass('hover');
    if (target.to.length === 0) {
      suggestion.isHover = false;
      $('#header input').val(suggestion.query);
      return;
    }
    suggestion.isHover = true;
    target.to.addClass('hover');
    $('#header input').val(target.to.find('span:first').text());
  };

  suggestion.up = function() {
    var current = $('#suggestion li.hover');
    var previous = current.prev();
    if (current.length === 0) {
      previous = $('#suggestion li').last();
    }
    return {from: current, to: previous};
  };

  suggestion.down = function() {
    var current = $('#suggestion li.hover');
    var next = current.next();
    if (current.length === 0) {
      next = $('#suggestion li').first();
    }
    return {from: current, to: next};
  };

  $(function() {
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
    suggestion.initialize();
  });

  return {argumentList: function() {
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
  }()};
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