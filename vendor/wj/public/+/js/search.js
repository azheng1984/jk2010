huobiwanjia.search = function() {
  
}();

/* price range
 *****************************/

/* tag list
 *****************************/
$(function() {
  $.getJSON(window.location.pathname + '?media=json', function(data) {
    var totalFound = data.shift();
    var type  = window.location.pathname.split('/').length === 3
      ? 'category' : 'property';
    if (type === 'category') {
      var html = '';
      for (var index = 0; index < data.length; ++index) {
        html += '<li class="value"><a href="'
          + encodeURIComponent(data[index][0]) + '/"><span>' + data[index][0] + '</span> ' + data[index][1] + '</a></li>';
      }
      $('#result_wrapper').after('<div id="tag"><h2>分类:</h2><ol>' + html + '</ol><div class="more"><span>更多</span></div></div>');
      enhanceCategoryList();
      return;
    }
    var html = '';
    for (var index = 0; index < data.length; ++index) {
      html += '<li><span class="key"><span>' + data[index][0] + '</span></span></li>';
    }
    $('#result_wrapper').after('<div id="tag"><h2>属性:</h2><ol>' + html + '</ol><div class="more property"><span>更多属性</span></div></div>');
    $('#tag .key').click(function() {
      if ($(this).attr('class') === 'key open') {
        $(this).attr('class', 'key');
        $(this).parent().children('ol').remove();
        $(this).parent().children('div').remove();
        return;
      }
      var target = $(this);
      //TODO: 如果有属性选定，先判断 is_multiple 值，再决定是否发起请求
      var keyName = $(this).text();
      $uri2 = window.location.pathname + '?key=' + keyName + '&media=json';
      $.getJSON($uri2, function(data) {
        var html = '';
        for (var index = 1; index < data.length; ++index) {
          var item = data[index];
          //var tmp = getPropertyHref(keyName, item[0], true);
          var href = '/';//tmp[0];
          if (window.location.pathname.split('/').length > 4) {
            href = '../' + href;
          }
          if (true) {
            html += '<li><span class="value"><a href="' + href + '">'+ '<span>' + item[0] + '</span> ' + item[1] + '</a></li>';
          } else {
            html += '<li><span class="value selected"><a href="' + href + '">'+ '<span>' + item[0] + '</span> ' + '</a></li>';
          }
        }
        target.after('<ol>' + html + '</ol><div class="more"><span>更多</span></div>').attr('class', 'key open');
        target = null;
      });
    });
  });
});
var categoryLoadCount = 0;
function enhanceCategoryList() {
  $('#tag .more').click(function() {
    $(this).replaceWith('<div id="load">正在加载…</div>');
    ++categoryLoadCount;
    $.getJSON(window.location.pathname + '?media=json', function(data){
      var html = '';
      for (var index = 1; index < data.length; ++index) {
        html += '<li class="value" style="display:none"><a href="'
          + encodeURIComponent(data[index][0]) + '/"><span>' + data[index][0] + '</span> ' + data[index][1] + '</a></li>';
      }
      $('#load').fadeOut('fast', function(){
        $(this).remove();
        if (categoryLoadCount < 5) {
          $('#tag ol').append(html).after('<div class="more"><span>更多</span></div>');
          $('#tag li:hidden').fadeIn('slow');
          enhanceCategoryList();
          return;
        }
        $('#tag li').last().after(html + '<li>…</li>');
        $('#tag li:hidden').fadeIn('slow');
      });
    });
  });
}
var propertyList = {};
var level = null;
function getPropertyHref(keyName, valueName, removeFlag) {
  if (level === null) {
    level = 0;
    var name = null;
    var valueList = null;
    $('h1').children().each(function() {
      if ($(this).hasClass('section')) {
        ++level;
        return;
      }
      if ($(this).is('span')) {
        if (name !== null) {
          propertyList[name] = valueList;
        }
        name = $(this).text().substr(0, $(this).text().length - 1);
        valueList = [];
        return;
      }
      valueList.push(encodeURIComponent($(this).text()));
    });
    if (name !== null) {
      propertyList[name] = valueList;
    }
  }
  if (typeof propertyList[keyName] === 'undefined') {
    var href = propertyListPath;
    if (level === 2) {
      href += '&';
    }
    result = href + encodeURIComponent(keyName) + '=' + encodeURIComponent(valueName) + '/' + queryString;
    if ((typeof removeFlag) !== 'undefined') {
      result = [result, false];
    }
    return result;
  }
  var isRemove = false;
  sectionList = [];
  for (var propertyName in propertyList) {
    var valueList = propertyList[propertyName];
    var valuePath = encodeURIComponent(valueName);
    if (keyName === propertyName && $.inArray(valuePath, valueList) !== -1) {
      isRemove = true;
      valueList = $.grep(valueList, function(value) {
        return value != valuePath;
      });
    } else {
      valueList = valueList.slice(0);
      valueList.push(valuePath);
    }
    if (valueList.length !== 0) {
      sectionList.push(
        encodeURIComponent(propertyName) + '=' + valueList.join('&')
      );
    }
  }
  result = '';
  if (sectionList.length !== 0) {
    result = sectionList.join('&') + '/';
  }
  result = result + queryString;
  if ((typeof removeFlag) !== 'undefined') {
    result = [result, isRemove];
  }
  return result;
}

/* product link list
 *****************************/

/* product property highlight
 *****************************/

/* product click tracking
 *****************************/