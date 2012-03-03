$(function() {
  if ($('#result').length !== 0) {
    //TODO:ajax load tag list
    $uri = window.location.pathname + '?media=json';
    $.getJSON($uri, function(data) {
      //使用 js 渲染，剔除缓存重复（缓存造成）
      $html  = '<ol>';
      var deep = window.location.pathname.split('/').length;
      if (deep === 3) {
        for (var index = 0; index < data.length; ++index) {
          $html += '<li class="value"><a href=""><span>' + data[index][0] + '</span> ' + data[index][1] + '</a></li>';
        }
      }
      if (deep > 3) {
        for (var index = 0; index < data.length; ++index) {
          $html += '<li class="key"><a href="javascript:void(0)">' + data[index] + '</a></li>';
        }
      }
      $html += '</ol>';
      $('#result_wrapper').after('<div id="tag"><h2>分类:</h2><ol><li>' + $html + '</li></ol></div>');
      $('#tag .key').mouseup(function() {
        if ($(this).attr('class') === 'key open') {
          $(this).attr('class', 'key');
          $(this).parent().children('ol').remove();
          return;
        }
        //var target = $(this);
        //TODO: 如果有属性选定，先判断 is_multiple 值，再决定是否发起请求
        $uri2 = window.location.pathname + '?key=' + $(this).text() + '&media=json';
        $(this).attr('id', 'target');
        $.getJSON($uri2, function(data) {
          $('#target').append('<ol><li>'+ data[0][0] + '</li></ol>').attr('class', 'key open');
        });
      });
    });
  }
});
var query = {};
var tmp = window.location.pathname.split('/');
var propertyListPath = tmp[3];
if (location.search != '') {
  var qs = location.search;
  if (qs.charAt(0) == '?') qs= qs.substring(1);
  var re = /([^=&]+)(=([^&]*))?/g;
  while (match= re.exec(qs)) {
    var key = decodeURIComponent(match[1].replace(/\+/g,' '));
    var value = decodeURIComponent(match[3].replace(/\+/g,' '));
    query[key] = value;
  }
}
var args = [];
if (typeof(query['price_from']) !== 'undefined') {
  args.push('price_form=' + query['price_from']);
}
if (typeof(query['price_to']) !== 'undefined') {
  args.push('price_to=' + query['price_to']);
}
if (typeof(query['sort']) !== 'undefined') {
  args.push('sort=' + query['sort']);
}
var queryString = '';
if (args.length > 0) {
  queryString = '?' + args.join('&');
}
var propertyList = {};
var level = null;
function getPropertyHref(keyName, valueName) {
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
        name = $(this).text();
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
    return href + encodeURIComponent(keyName) + '=' + encodeURIComponent(valueName) + '/' + queryString;
  }
  $sectionList = [];
  for (var propertyName in propertyList) {
    $sectionList.push(encodeURIComponent(propertyName) + '=' + propertyList[propertyName].join('&'));
    if (propertyName === keyName) {
      pathPrefix += '&' + encodeURIComponent(valueName);
    }
  }
  return $sectionList.join('&') + '/' + queryString;
}
$(function() {
  $('#result .link_list').each(function() {
    var self = $(this);
    var propertyList = self.children();
    var html = '';
    for (var index  = 0; index < propertyList.length; ++index) {
      //TODO：删除结尾的 “...”
      var property = $(propertyList[index]);
      var list = property.html().split('：');
      if (list.length !== 2) {
        if (property !== '') {
          html += '<li>' + property.html() + '</li>';
        }
        continue;
      }
      var name = list[0];
      var valueList = list[1].split('；');
      html += '<li>' + name + '：';
      for (var index2  = 0; index2 < valueList.length; ++index2) {
        var value = valueList[index2];
        var href = getPropertyHref(name.replace(/<\/span>/gi, '')
            .replace(/<span>/gi, ''), value.replace(/<\/span>/gi, '')
            .replace(/<span>/gi, ''));
        value = value.replace(/<\/span>/gi, '</span><span class="gray">')
          .replace(/<span>/gi, '</span><span class="red">');
        html += '<a href="' + href + '"><span class="gray">'
          + value + '</span></a>';
        if (index2 !== valueList.length - 1) {
          html += '；';
        }
      }
      html += '</li>';
    }
    self.html(html);
  });
});
$(function() {
  var query = {};
  if (location.search != '') {
    var qs = location.search;
    if (qs.charAt(0) == '?') qs= qs.substring(1);
    var re = /([^=&]+)(=([^&]*))?/g;
    while (match= re.exec(qs)) {
      var key = decodeURIComponent(match[1].replace(/\+/g,' '));
      var value = decodeURIComponent(match[3].replace(/\+/g,' '));
      query[key] = value;
    }
  }
  var priceFrom = typeof(query['price_from']) !== 'undefined' ? query['price_from'] : '';
  var priceTo = typeof(query['price_to']) !== 'undefined' ? query['price_to'] : '';
  var form = '<form id="price_range" action="."><label for="price_from">&yen;</label>';
  if (typeof(query['sort']) !== 'undefined') {
    form += '<input name="sort" type="hidden" value="' + query['sort'] + '"/>';
  }
  form += '<input id="price_from" name="price_from" type="text" value="' + priceFrom + '" autocomplete="off"/><span>-</span>' +
    '<input name="price_to" type="text" value="' + priceTo + '" autocomplete="off"/>' +
    '<button tabIndex="-1" type="submit"></button></form>';
  $('#toolbar h2').after(form);
  function adjustInput() {
    if ($(this).val().length > 4) {
      $(this).css('width', '60px');
      return;
    }
    $(this).css('width', '30px');
  }
  $('#price_range input').each(adjustInput);
  $('#price_range input').keyup(adjustInput);
  $('#price_range input').focusin(function() {
    if ($('#price_range_button').length != 0) {
      return;
    }
    //TODO:显示目标链接（源代码对用来来说是干扰，至少要隐藏）
    $('#price_range').append('<a id="price_range_button" href="javascript:$(\'#price_range\').submit()">确定</a>');
  });
});
$(function() {
  $('#result h3 a').each(function() {
    $(this).bind('mousedown', function(button) {
      if (button.which == 1) {
        alert($(this).parent().parent().find('img').attr('src'));
        //从 img src 获取产品 id 信息 + session 信息，通过 jsonp 的方式发送到 tracking 服务器
      }
    });
  });
  $('#result .image a').each(function() {
    $(this).bind('mousedown', function(button) {
      if (button.which == 1) {
        alert($(this).parent().parent().find('img').attr('src'));
        //从 img src 获取产品 id 信息 + session 信息，通过 jsonp 的方式发送到 tracking 服务器
      }
    });
  });
});