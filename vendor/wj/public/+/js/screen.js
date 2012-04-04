var huobiwanjia = function() {
  var argumentList = {};
  if (location.search !== '') {
    var queryString = location.search.charAt(0) === '?' ?
       location.search.substring(1) : location.search;
    var regex = /([^=&]+)(=([^&]*))?/g;
    for (;;) {
      var match = regex.exec(queryString);
      if (match === null) {
        break;
      }
      argumentList[decodeURIComponent(match[1].replace(/\+/g,' '))] =
        decodeURIComponent(match[3].replace(/\+/g,' '));
    }
  }
  return {
    argumentList: argumentList,
//    suggestionCache: {},
//    currentAjaxQuery: null,
    currentQuery: null,
    suggestionTimer: null,
    isHoverSuggestion: null,
  };
}();

/* search suggestion
 *****************************/
$(function() {
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
  huobiwanjia.initializeSuggestion();
});

huobiwanjia.initializeSuggestion = function() {
  //ubuntu firefox 输入中文不会触发 keydown，不能用来检查数据
  $('#header input').keydown(function(event) {
    if ($('#suggestion').length == 0) {
      return;
    }
    if (event.which == 27) {
      $('#suggestion').remove();
      return;
    }
    if (event.which == 38) {
      up();
      return false;
    }
    if (event.which == 40) {
      down();
      return false;
    }
  }).focusin(function() {
    huobiwanjia.currentQuery = $.trim($('#header input').val());
    huobiwanjia.suggestionTimer = setInterval(
      huobiwanjia.checkQueryInput, 1000
    );
  }).focusout(function() {
    huobiwanjia.currentQuery = null;
    clearInterval(huobiwanjia.suggestionTimer);
    $('#suggestion').remove();
  });
  /* ie 刷新后默认和当前页面 focus 状态一致 */
  if($.browser.msie) {
    $('#header input').blur();/* 如果遇到 ie6 的 bug，blur 失效 */
  }
};

huobiwanjia.checkQueryInput = function() {
  query = $.trim($('#header input').val());
  if (query === huobiwanjia.currentQuery || isHoverSuggestion) {
    return;
  }
  currentQuery = query;
  getSuggestion();
};

huobiwanjia.getSuggestion = function() {
  huobiwanjia.currentAjaxQuery = huobiwanjia.currentQuery;
  if (currentAjaxQuery == '') {
    suggest(currentAjaxQuery);
    return;
  }
  if (typeof(suggestionCache[currentAjaxQuery]) == 'undefined') {
    var uri = 'http://q.dev.huobiwanjia.com/'
      + encodeURIComponent(currentAjaxQuery);
    $.ajax({
      url:uri,
      cache:true,
      dataType:'jsonp',
      jsonp:false
    });
    return;
  }
  suggest(
    suggestionCache[currentAjaxQuery][0], suggestionCache[currentAjaxQuery][1]
  );
};

function suggest(segmentList, data) {
  if (typeof(suggestionCache[currentAjaxQuery]) == 'undefined') {
    suggestionCache[currentAjaxQuery] = [segmentList, data];
  }
  if (currentQuery == currentAjaxQuery) {
    renderSuggestion(segmentList, data);
  }
  if (currentQuery != currentAjaxQuery && currentQuery != null) {
    getSuggestion();
    return;
  }
  currentAjaxQuery = null;
}

function renderSuggestion(segmentList, data) {
  if (typeof(data) == 'undefined') {
    $('#suggestion').remove();
    return;
  }
  var html = '';
  $.each(data, function(index, value) {
    if (index.replace(' ', '') === segmentList.replace(' ', '')) {
      return;
    }
    var text = index;
    if (segmentList !== '') {
      text = highlight(index, segmentList.split(' '));
    }
    html += '<li><a href="/' + encodeURIComponent(index)
      + '/"><span class="query">' + text
      + '</span><span class="product_amount">' + value
      + '</span></a></li>';
  });
  if (html === '') {
    return;
  }
  $('#suggestion').remove();
  $('#header').append('<div id="suggestion"><ul>' + html + '</ul></div>');
  $('#suggestion li a').hover(
    function(){
      $('#suggestion li.hover').removeClass('hover');
      $(this).parent().addClass('hover');
      stopHiddenSuggestion = true;
    },
    function(){
      $('#suggestion li.hover').removeClass('hover');
      stopHiddenSuggestion = false;
    }
  );
  $('#suggestion').css('display', 'block');/* 触发 ie6 渲染 */
}

function highlight(query, keywordList) {
  var positionList = {};
  for (var index = 0; index < keywordList.length; ++index) {
    var offset = 0;
    while (-1 !== (offset = query.indexOf(keywordList[index], offset))) {
      if (typeof(positionList[offset]) === 'undefined') {
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
}

function up() {
  var target = null;
  var previous = null;
  $('#suggestion li').each(function() {
    if (target != null) {
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
  $('#header input').val(currentQuery);
}

function down() {
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
  $('#header input').val(currentQuery);
}

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