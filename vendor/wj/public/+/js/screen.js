var suggestionCache = {};
var currentAjaxQuery = null;
var currentQuery = null;
var suggestionIntervalId = null;
var hoverQuery = null;
var stopHiddenSuggestion = false;

function getSuggestion() {
  currentAjaxQuery = currentQuery;
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
}

function suggest(query, data) {
  if (typeof(suggestionCache[currentAjaxQuery]) == 'undefined') {
    suggestionCache[currentAjaxQuery] = [query, data];
  }
  if (currentQuery == currentAjaxQuery) {
    renderSuggestion(query, data);
  }
  if (currentQuery != currentAjaxQuery && currentQuery != null) {
    getSuggestion();
    return;
  }
  currentAjaxQuery = null;
}

function renderSuggestion(query, data) {
  if (typeof(data) == 'undefined') {
    $('#suggestion').remove();
    return;
  }
  var html = '<div id="suggestion"><ul>';
  $.each(data, function(index, value) {
    html += '<li><a href="/' + encodeURIComponent(index)
      + '/"><span class="query">' + highlight(index, query)
      + '</span><span class="product_amount">' + value
      + '</span></a></li>';
  });
  html += '</ul></div>';
  $('#header').append(html);
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
  $('#suggestion').css('display', 'block');
}

function highlight(query, keywordList) {
  return query;
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

function checkQueryInput() {
  query = $.trim($('#header input').val());
  if (query == currentQuery || query == hoverQuery) {
    return;
  }
  currentQuery = query;
  getSuggestion();
}

function initializeQueryForm() {
  $('#header input').attr('autocomplete', 'off');
  $('#header form').bind('submit', function() {
    query = encodeURIComponent($.trim($('#header input').attr('value')))
      .replace(/%20/g, '+') + '/';
    if (query == '%2B/') {
      query = '';
    }
    window.location = '/' + query;
    return false;
  });
  initializeQuerySuggestion();
}

function initializeQuerySuggestion() {
  $('#header input').keydown(function(event) {//ubuntu firefox 输入中文不会触发
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
  });
  $('#header input').focusin(function() {
    currentQuery = $.trim($('#header input').val());
    suggestionIntervalId = setInterval(checkQueryInput, 1000);
  });
  $('#header input').focusout(function() {
    currentQuery = null;
    clearInterval(suggestionIntervalId);
    if (stopHiddenSuggestion != false) {
      return;
    }
    $('#suggestion').remove();
  });
  /* ie6 刷新后默认和当前页面 focus 状态一致 */
  if(typeof document.body.style.maxHeight === "undefined") {
    $('#header input').blur();/* 如果遇到 bug，blur 失效 */
  }
}

$(function() {
  initializeQueryForm();
});