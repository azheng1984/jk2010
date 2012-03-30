huobiwanjia.search = function() {
  var args = [];
  var query = huobiwanjia.queryString;
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
  var level = 0;
  var name = null;
  var valueList = [];
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
  return {
    queryString: queryString,
    level: level,
    propertyList: propertyList
  };
}();

/* price range
 *****************************/

/* tag list
 *****************************/
$(function() {
  $.getJSON(window.location.pathname + '?media=json', function(data) {
    var hasMore = data.shift() > 20;
    var type = window.location.pathname.split('/').length === 3
      ? 'category' : 'property';
    if (type === 'category') {
      huobiwanjia.search.buildCategoryList(data, hasMore);
      return;
    }
    huobiwanjia.search.buildPropertyList(data, hasMore);
  });
});

huobiwanjia.search.buildCategoryList = function(data, hasMore) {
  hasMore= true;
  var html = '<div id="tag"><h2>分类:</h2><ol>';
  for (var index = 0; index < data.length; ++index) {
    var item = data[index];
    html += '<li class="value"><a href="'
      + encodeURIComponent(item[0]) + '/'
      + huobiwanjia.search.queryString + '"><span>'
      + item[0] + '</span> ' + item[1] + '</a></li>';
  }
  html += '</ol>';
  if (hasMore) {
    html += '<span class="more"><span>更多</span></span>';
  }
  $('#result_wrapper').after(html + '</div>');
  huobiwanjia.search.enhanceMoreCategory(2);
};

huobiwanjia.search.enhanceMoreCategory = function(page) {
  $('#tag .more').click(function() {
    $(this).replaceWith('<span class="load">正在加载…</span>');
    var uri = window.location.pathname + '?media=json&page=' + page;
    $.getJSON(uri, function(data) {
      var html = '';
      var hasMore = data.shift() > page * 20;
      for (var index = 0; index < data.length; ++index) {
        var item = data[index];
        html += '<li class="value"><a href="'
          + encodeURIComponent(item[0]) + '/'
          + huobiwanjia.search.queryString
          + '"><span>' + item[0] + '</span> ' + item[1] + '</a></li>';
      }
      
      $('#tag .load').remove();
      $('#tag ol').append(html);
      hasMore = true;
      if (hasMore === false) {
        return;
      }
      if (huobiwanjia.search.categoryPage === 5) {
        $('#tag ol').append('<li>…</li>');
        return;
      }
      $('#tag ol').after('<span class="more"><span>更多</span></span>');
      huobiwanjia.search.enhanceMoreCategory(++page);
    });
    $('#tag .load').fadeIn('fast');
  });
};

huobiwanjia.search.buildPropertyList = function(data, hasMore) {
  var html = '<div id="tag"><h2>属性:</h2><ol>';
  for (var index = 0; index < data.length; ++index) {
    html += '<li><span class="key"><span>'
      + data[index][0] + '</span></span></li>';
  }
  html += '</ol>';
  hasMore = true;
  if (hasMore) {
    html += '<span class="more property"><span>更多属性</span></span>';
  }
  $('#result_wrapper').after(html + '</div>');
  $('#tag .key').each(function() {
    huobiwanjia.search.enhanceKey($(this));
  });
  huobiwanjia.search.enhanceMoreKey(2);
};

huobiwanjia.search.enhanceKey = function(key) {
  key.click(function() {
    var current = $(this);
    if (current.hasClass('key open')) {
      current.removeClass('open');
      current.nextAll().hide();
      return;
    }
    current.addClass('open');
    if (current.next('ol').length > 0) {
      current.nextAll().show();
      return;
    }
    current.after('<span class="load">正在加载…</span>');
    var keyName = current.text();
    var uri = window.location.pathname + '?key='
      + encodeURIComponent(keyName) + '&media=json';
    $.getJSON(uri, function(data) {
      //alert('x');
      current.parent().children('.load').remove();
      var isHidden = false;
      if (key.hasClass('open') === false) {
        isHidden = true;
      }
      var html = '<ol';
      if (isHidden) {
        html += ' class="hidden"';
      }
      html += '>';
      var hasMore = true;data.shift() > 20;
      for (var index = 0; index < data.length; ++index) {
        var item = data[index];
        html += '<li class="value"><a href="'
          + huobiwanjia.search.getTagHref(keyName, item[0]) +'">'
          + '<span> ' + item[0] + '</span> ' + item[1] + '</a></li>';
      }
      html += '</ol>';
      current.after(html);
      if (hasMore === false) {
        return;
      }
      var hidden = '';
      if (isHidden) {
        hidden = ' hidden';
      }
      current.next().after('<span class="more'
        + hidden + '"><span>更多</span></span>');
      huobiwanjia.search.enhanceMoreValue(current, keyName, 2);
    });
    current.parent().children('.load').fadeIn('fast');
  });
};

huobiwanjia.search.enhanceMoreValue = function(key, keyName, page) {
  var more = key.parent().children('.more');
  more.click(function() {
    more.replaceWith('<span class="load">正在加载…</span>');
    var uri = window.location.pathname + '?key='
      + encodeURIComponent(keyName) + '&page=' + 1 + '&media=json&t=' + Date.now();
    $.getJSON(uri, function(data) {
        key.parent().children('.load').remove();
        var html = '';
        if (data.length === 0) {//TODO:move to error
          return;
        }
        var hasMore = data.shift() > 20 * page;
        for (var index = 0; index < data.length; ++index) {
          var item = data[index];
          html += '<li class="value"><a href="'
            + huobiwanjia.search.getTagHref(keyName, item[0]) +'">'
            + '<span> ' + item[0] + '</span> ' + item[1] + '</a></li>';
        }
        key.next().append(html);
        //key.next().children('.hidden').fadeIn('fast');
        hasMore = true;
        if (hasMore === false) {
          return;
        }
        if (page === 5) {
          key.next('ol').append('<li>…</li>');
          return;
        }
        var hidden = '';
        if (key.hasClass('open') === false) {
          hidden = ' hidden';
        }
        key.next('ol').after('<span class="more'
          + hidden +'"><span>更多</span></span>');
        huobiwanjia.search.enhanceMoreValue(key, keyName, ++page);
    });
    key.parent().children('.load').fadeIn('fast');
  });
};

huobiwanjia.search.enhanceMoreKey = function(page) {
  $('.more.property').click(function() {
    $(this).replaceWith('<span class="load property">正在加载…</span>');
    var uri = window.location.pathname + '?page=' + 1 + '&media=json';
    $.getJSON(uri, function(data) {
      $('.load.property').fadeOut('fast', function() {
        $(this).remove();
        var hasMore = data.shift() > 20 * page;
        var html = '';
        for (var index = 0; index < data.length; ++index) {
          html += '<li class="hidden"><span class="key"><span>'
            + data[index][0] + '</span></span></li>';
        }
        $('#tag').children('ol').append(html);
        $('#tag').children('ol').children('.hidden').each(function() {
          huobiwanjia.search.enhanceKey($(this).children('span'));
        });
        $('#tag').children('ol').children('.hidden').fadeIn('fast');
        if (hasMore === false) {
          return;
        }
        if (page === 5) {
          $('#tag').children('ol').append('<li>…</li>');
          return;
        }
        $('#tag').children('ol').after(
          '<span class="more property"><span>更多属性</span></span>'
        );
        huobiwanjia.search.enhanceMoreKey(++page);
      });
    });
  });
};

huobiwanjia.search.getTagHref = function(keyName, valueName) {
  var propertyList = huobiwanjia.search.propertyList;
  var level = huobiwanjia.search.level;
  var queryString = huobiwanjia.search.queryString;
  if (typeof propertyList[keyName] === 'undefined') {
    var href = window.location.pathname.split('/')[3];
    if (level === 2) {
      href += '&';
    }
    return href + encodeURIComponent(keyName) + '=' + encodeURIComponent(valueName) + '/' + queryString;
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
  if (isRemove) {
    result = '..'.result;
  }
  return result;
};

/* product link list
 *****************************/

/* product click tracking
 *****************************/