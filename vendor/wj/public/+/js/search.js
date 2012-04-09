(function() {
  var search = {
    queryString: '',
    propertyList: {}
  };

  search.initialize = function() {
    /* query string */
    var argumentList = [];
    var query = huobiwanjia.argumentList;
    if (typeof query.price_from !== 'undefined') {
      argumentList.push('price_from=' + query.price_from);
    }
    if (typeof query.price_to !== 'undefined') {
      argumentList.push('price_to=' + query.price_to);
    }
    if (typeof query.sort !== 'undefined') {
      argumentList.push('sort=' + query.sort);
    }
    search.queryString = '';
    if (argumentList.length > 0) {
      search.queryString = '?' + argumentList.join('&');
    }
    /* property list */
    var keyName = null;
    $('#nav').children().each(function() {
      var self = $(this);
      var text = self.text();
      if (self.is('span:not(.delimiter)')) {
        keyName = text.substr(0, text.length - 1);
        search.propertyList[keyName] = [];
        return;
      }
      if (self.hasClass('tag')) {
        search.propertyList[keyName].push(encodeURIComponent(text));
      }
    });
  };

  search.renderPriceRange = function() {
    var query = huobiwanjia.argumentList;
    var priceFrom = typeof query.price_from === 'undefined'
      ? '' : query.price_from;
    var priceTo = typeof query.price_to === 'undefined' ? '' : query.price_to;
    var form =
      '<form id="price_range" action="."><label for="price_from">&yen;</label>';
    if (typeof query.sort !== 'undefined') {
      form += '<input name="sort" type="hidden" value="' + query.sort + '"/>';
    }
    form += '<input id="price_from" name="price_from" type="text" value="'
      + priceFrom + '" autocomplete="off"/><span>-</span>'
      + '<input id="price_to" name="price_to" type="text" value="'
      + priceTo + '" autocomplete="off"/>'
      + '<button tabIndex="-1" type="submit"></button></form>';
    $('#toolbar h2').after(form);
    var adjustInput = function() {
      var self = $(this);
      if (self.val().length > 4) {
        self.addClass('long');
        return;
      }
      self.removeClass('long');
    };
    $('#price_range input').each(adjustInput).keyup(adjustInput);
    //visibility hidden 和 display none 都会导致 ie 重置 tabindex
    $('#price_range input').focusin(function() {
      if ($('#price_range_button').length !== 0) {
        return;
      }
      $('#price_range')
        .append('<span id="price_range_button" tabindex="0">确定</span>');
      $('#price_range_button').bind('mouseenter mouseleave', function() {
        $(this).toggleClass('hover');
      }).click(function() {
        $('#price_range').submit();
      }).keypress(function(event) {
        if(event.which === 13){
          $('#price_range').submit();
        }
      }).mousedown(function() {
        $(this).addClass('active');
      }).mouseout(function() {
        $(this).removeClass('active');
      });
    });
  };

  search.enhanceProductTagList = function() {
    $('#result li').each(function() {
      var self = $(this);
      var valueList = self.children('.value');
      if (valueList.length === 0) {
        return;
      }
      var list = self.html().split(': ');
      if (list.length < 2) {
        return;
      }
      var keyName = list[0];
      valueList.each(function() {
        var self = $(this);
        var html = self.html();
        var valueName = html.replace(/<\/span>/gi, '').replace(/<span>/gi, '');
        html = html.replace(/<\/span>/gi, '</span><span class="gray">')
          .replace(/<span>/gi, '</span><span class="red">');
        self.replaceWith('<a href="' + search.getTagHref(keyName, valueName)
          + '"><span class="gray">' + html + '</span></a>');
      });
    });
  };

  search.toggleValuePath = function(valuePathList, valuePath) {
    if ($.inArray(valuePath, valuePathList) === -1) {
      valuePathList.push(valuePath);
      return valuePathList;
    }
    return $.grep(valuePathList, function(item) {
      return valuePath !== item;
    });
  };

  search.getTagHref = function(keyName, valueName) {
    if (keyName === '分类') {
      return encodeURIComponent(valueName) + '/' + search.queryString;
    }
    if (typeof search.propertyList[keyName] === 'undefined') {
      return search.appendTagHref(keyName, valueName);
    }
    var sectionList = [];
    for (var propertyName in search.propertyList) {
      var valuePathList = search.propertyList[propertyName];
      if (keyName === propertyName) {
        valuePathList =
          search.toggleValuePath(valuePathList, encodeURIComponent(valueName));
      }
      if (valuePathList.length !== 0) {
        sectionList.push(
          encodeURIComponent(propertyName) + '=' + valuePathList.join('&')
        );
      }
    }
    var href = '..';
    if (sectionList.length !== 0) {
      href += '/' + sectionList.join('&') + '/';
    }
    return href + search.queryString;
  };

  search.appendTagHref = function(keyName, valueName) {
    var href = window.location.pathname.split('/')[3];
    if (href !== '') {
      href = '../' + href + '&';
    }
    return href + encodeURIComponent(keyName) + '='
      + encodeURIComponent(valueName) + '/' + search.queryString;
  };

  search.loadTag = function(path, callback, more) {
    
  };

  search.renderTagList = function() {
    if ($('#no_result').length !== 0) {
      return;
    }
    $.getJSON(window.location.pathname + '?media=json', function(data) {
      var hasMore = data.shift() > 20;
      if (window.location.pathname.split('/').length === 3) {
        search.renderCategoryList(data, hasMore);
        return;
      }
      search.renderPropertyList(data, hasMore);
    });
  };

  search.renderCategoryList = function(data, hasMore) {
    hasMore = true;
    var html = '<div id="tag"><h2>分类:</h2><ol>';
    var length = data.length;
    for (var index = 0; index < length; ++index) {
      var item = data[index];
      html += '<li class="value"><a href="'
        + search.getTagHref('分类', item[0]) + '"><span>'
        + item[0] + '</span>' + item[1] + '</a></li>';
    }
    html += '</ol>';
    if (hasMore) {
      html += '<span class="more"><span>更多</span></span>';
    }
    $('#result_wrapper').after(html + '</div>');
    search.enhanceMoreCategory(2);
  };

  search.enhanceMoreCategory = function(page) {
    $('#tag .more').click(function() {
      $(this).replaceWith('<span class="load">正在加载…</span>');
      var uri = window.location.pathname + '?media=json&page=' + page;
      $.getJSON(uri, function(data) {
        var html = '';
        var hasMore = data.shift() > page * 20;
        for (var index = 0; index < data.length; ++index) {
          var item = data[index];
          html += '<li class="value"><a href="'
            + encodeURIComponent(item[0]) + '/' + search.queryString
            + '"><span>' + item[0] + '</span>' + item[1] + '</a></li>';
        }
        $('#tag .load').remove();
        var wrapper = $('#tag ol');
        wrapper.append(html);
        if (hasMore === false) {
          return;
        }
        if (page === 5) {
          wrapper.append('<li>…</li>');
          return;
        }
        wrapper.after('<span class="more"><span>更多</span></span>');
        search.enhanceMoreCategory(++page);
      });
      $('#tag .load').fadeIn('fast');
    });
  };

  search.renderPropertyList = function(data, hasMore) {
    var html = '<div id="tag"><h2>属性:</h2><ol>';
    var length = data.length;
    for (var index = 0; index < length; ++index) {
      var multiple = data[index][1] === 0 ? '' : ' multiple';
      html += '<li><span class="key' + multiple + '"><span>'
        + data[index][0] + '</span></span></li>';
    }
    html += '</ol>';
    if (hasMore) {
      html += '<span class="more property"><span>更多属性</span></span>';
    }
    $('#result_wrapper').after(html + '</div>');
    $('#tag .key').each(function() {
      search.enhanceKey($(this));
    });
    search.enhanceMoreKey(2);
  };

  search.renderValue = function(keyName, valueName, productAmount) {
    if (typeof search.propertyList[keyName] !== 'undefined'
      && $.inArray(
        encodeURIComponent(valueName), search.propertyList[keyName]
      ) !== -1) {
      return '<li class="value"><a class="selected" href="'
        + search.getTagHref(keyName, valueName) +'">'+ valueName + '</a></li>';
    }
    return '<li class="value"><a href="'
      + search.getTagHref(keyName, valueName) +'">'
      + '<span>' + valueName + '</span>' + productAmount + '</a></li>';
  };

  search.enhanceKey = function(key) {
    key.click(function() {
      if (key.hasClass('open')) {
        key.removeClass('open');
        key.nextAll().hide();
        return;
      }
      key.addClass('open');
      if (key.next('ol').length > 0) {
        key.nextAll().show();
        return;
      }
      var keyName = key.text();
      if (search.propertyList[keyName] && key.hasClass('multiple') === false) {
        key.after('<ol>' + search.renderValue(
              keyName, decodeURIComponent(search.propertyList[keyName][0])
          ) + '</ol>');
        return;
      }
      key.after('<span class="load">正在加载…</span>');
      var keyName = key.text();
      var uri = window.location.pathname + '?key='
        + encodeURIComponent(keyName) + '&media=json';
      $.getJSON(uri, function(data) {
        key.parent().children('.load').remove();
        var isHidden = false;
        if (key.hasClass('open') === false) {
          isHidden = true;
        }
        var html = '<ol';
        if (isHidden) {
          html += ' class="hidden"';
        }
        html += '>';
        var hasMore = data.shift() > 20;
        for (var index = 0; index < data.length; ++index) {
          var item = data[index];
          html += search.renderValue(keyName, item[0], item[1]);
        }
        html += '</ol>';
        key.after(html);
        if (hasMore === false) {
          return;
        }
        var hidden = '';
        if (isHidden) {
          hidden = ' hidden';
        }
        key.next().after('<span class="more'
          + hidden + '"><span>更多</span></span>');
        search.enhanceMoreValue(key, keyName, 2);
      });
      key.parent().children('.load').fadeIn('fast');
    });
  };

  search.enhanceMoreValue = function(key, keyName, page) {
    key.parent().children('.more').click(function() {
      var current = $(this);
      current.replaceWith('<span class="load">正在加载…</span>');
      var uri = window.location.pathname + '?key='
        + encodeURIComponent(keyName) + '&media=json&page=' + 1;
      $.getJSON(uri, function(data) {
          current.remove();
          var html = '';
          if (data.length === 0) {
            return;
          }
          var hasMore = data.shift() > 20 * page;
          for (var index = 0; index < data.length; ++index) {
            var item = data[index];
            search.renderValue(keyName, item[0], item[1]);
            html += search.renderValue(keyName, item[0], item[1]);
          }
          key.next().append(html);
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
          search.enhanceMoreValue(key, keyName, ++page);
      });
      key.parent().children('.load').fadeIn('fast');
    });
  };

  search.enhanceMoreKey = function(page) {
    $('.more.property').click(function() {
      var current = $(this);
      current.replaceWith('<span class="load property">正在加载…</span>');
      var uri = window.location.pathname + '?page=' + page + '&media=json';
      $.getJSON(uri, function(data) {
        current.remove();
        var hasMore = data.shift() > 20 * page;
        var html = '';
        for (var index = 0; index < data.length; ++index) {
          html += '<li class="new"><span class="key"><span>'
            + data[index][0] + '</span></span></li>';
        }
        var ol = $('#tag').children('ol');
        ol.append(html);
        var list = ol.children('.new');
        list.each(function() {
          search.enhanceKey($(this).children('span'));
        });
        list.removeClass('new');
        if (hasMore === false) {
          return;
        }
        if (page === 5) {
          ol.append('<li>…</li>');
          return;
        }
        ol.after('<span class="more property"><span>更多属性</span></span>');
        search.enhanceMoreKey(++page);
      });
      $('.load.property').fadeIn('fast');
    });
  };

  $(function() {
    search.initialize();
    search.renderPriceRange();
    search.enhanceProductTagList();
    search.renderTagList();
  });
})();

/* product click tracking
 *****************************/