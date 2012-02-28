var suggestionCache = {};
var currentAjaxQuery = null;
var currentQuery = null;
var suggestionTimeoutId = null;
var hoverQuery = null;
var stopHiddenSuggestion = false;

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
  suggestionTimeoutId = null;
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
  $('#suggestion').remove();
  $('#header').append(html);
  $('#suggestion li a').hover(
    function(){
      hoverQuery = $(this).find('.query').val();
      $('#suggestion li.hover').removeClass('hover');
      $(this).parent().addClass('hover');
      stopHiddenSuggestion = true;
    },
    function(){
      $('#suggestion li.hover').removeClass('hover');
      hoverQuery = null;
      stopHiddenSuggestion = false;
    }
  );
}

function highlight(query, keywordList) {
  return query;
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

function initializeQuerySuggestion() {
  $('#header input').keydown(function(event){
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
  $('#header input').keyup(function(evnet) {
    //query = $('#header input').val().trim();
    $('#header').append('dfasdf <br/>');
    return;
    if (query == currentQuery || query == hoverQuery) {
      return;
    }
    currentQuery = query;
    if (suggestionTimeoutId == false) {
      return;
    }
    if (suggestionTimeoutId != null) {
      clearTimeout(suggestionTimeoutId);
      suggestionTimeoutId = null;
    }
    suggestionTimeoutId = setTimeout(getSuggestion, 600);
  });
  $('#header input').focusin(function() {
    currentQuery = $('#header input').val().trim();
  });
  $('#header input').focusout(function() {
    currentQuery = null;
    if (stopHiddenSuggestion != false) {
      return;
    }
    $('#suggestion').remove();
  });
}

function getSuggestion() {
  $('#header').append('getSuggestion');
  suggestionTimeoutId = false;
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

$(function() {
  initializeQueryForm();
});





/*




function suggest(query, data) {
  var text = '<div id="suggestion"><ul>';
  if (typeof(data) == 'undefined') {
    return;
  }
  $.each(data, function(index, value){
    text += '<li><a href="/' + encodeURIComponent(index) + '/"><span class="query">' + index + '</span><span class="product_amount">' + value + '</span></a></li>';
  });
  text += '</ul></div>';
  $('#header').append(text);
}
var suggestion = null;
function getSuggestion() {
  if (suggestion == false) {
    var query = $('#header input').attr('value');
    if (query == '') {
      return;
    }
    var uri = 'http://q.dev.huobiwanjia.com/' + encodeURIComponent(query);
    $.ajax({
      url:uri,
      cache:true,
      dataType:'jsonp',
      jsonp:false
    });
    suggestion = true;
    return;
  }
  if (suggestion == null) {
    suggestion = false;
  }
}
$(function() {
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
  var previousValue = $('#header input').val();
  var timeoutId = null;
  $('#header input').keyup(function() {
    if (previousValue != $(this).val()) {
      getSuggestion();
      previousValue = $(this).val();
      if (timeoutId != null) {
        clearTimeout(timeoutId);
      }
      timeoutId = setTimeout(function() {
        timeoutId = null;
        $('#header').append('<p>' + previousValue + '</p>');
      }, 600);
    }
  });
  $('#header input').click(function() {
    getSuggestion();
  });
  $('#header input').focusout(function() {
    if (suggestion) {
      $('#suggestion').remove();
      suggestion = null;
    }
  });
});
*/