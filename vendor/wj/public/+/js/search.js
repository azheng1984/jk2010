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
      var current = $(this);
      if (current.is('span:not(.delimiter)')) {
        var text = current.text();
        keyName = text.substr(0, text.length - 1);
        search.propertyList[keyName] = [];
        return;
      }
      if (current.hasClass('tag')) {
        search.propertyList[keyName].push(encodeURIComponent(current.text()));
      }
    });
  };

  search.renderPriceRange = function() {
    query = huobiwanjia.argumentList;
    var priceFrom = typeof query.price_from !== 'undefined'
      ? query.price_from : '';
    var priceTo = typeof query.price_to !== 'undefined' ? query.price_to : '';
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
    $('#price_range input').each(search.adjustInput);
    $('#price_range input').keyup(search.adjustInput);
    //ie visibility hidden 和 display none 都会导致 tabindex 重置
    $('#price_range input').focusin(function() {
      if ($('#price_range_button').length !== 0) {
        return;
      }
      $('#price_range')
        .append('<span id="price_range_button" tabindex="0">确定</span>');
      $('#price_range_button').hover(
        function() {$(this).addClass('hover');},
        function() {$(this).removeClass('hover');}
      ).click(function() {
        $('#price_range').submit();
      }).keypress(function(e) {
        if(e.which === 13){
          $('#price_range').submit();
        }
      }).mousedown(function() {
        $(this).addClass('active');
      }).mouseout(function() {
        $(this).removeClass('active');
      });
    });
  };

  search.adjustInput = function() {
    input = $(this);
    if (input.val().length > 4) {
      input.addClass('long');
      return;
    }
    input.removeClass('long');
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
        var html = $(this).html();
        var valueName = html.replace(/<\/span>/gi, '').replace(/<span>/gi, '');
        html = html.replace(/<\/span>/gi, '</span><span class="gray">')
        .replace(/<span>/gi, '</span><span class="red">');
        $(this).replaceWith('<a href="'
          + search.getTagHref(keyName, valueName)
          + '"><span class="gray">' + html + '</span></a>');
      });
    });
  };

  search.toggleValuePath = function(valuePathList, valuePath) {
    if ($.inArray(valuePath, valuePathList) === -1) {
      valuePathList.push(valuePath);
      return valuePathList;
    }
    valueList = $.grep(valuePathList, function(item) {
      return valuePath !== item;
    });
    return valuePathList;
  };

  search.getTagHref = function(keyName, valueName) {
    if (keyName === '分类') {
      return encodeURIComponent(valueName) + '/' + search.queryString;
    }
    if (typeof search.propertyList[keyName] === 'undefined') {
      var href = window.location.pathname.split('/')[3];
      if (href !== '') {
        href = '../' + href + '&';
      }
      return href + encodeURIComponent(keyName) + '='
        + encodeURIComponent(valueName) + '/' + search.queryString;
    }
    var sectionList = [];
    for (var propertyName in search.propertyList) {
      var valuePathList = search.propertyList[propertyName];
      if (keyName === propertyName) {
        valuePathList =
          search.toggleValuePath(valuePathList, encodeURIComponent(valueName));
      }
      if (valueList.length !== 0) {
        sectionList.push(
          encodeURIComponent(propertyName) + '=' + valuePathList.join('&')
        );
      }
    }
    var result = '..';
    if (sectionList.length !== 0) {
      result += '/' + sectionList.join('&') + '/';
    }
    return result + search.queryString;
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
        //hasMore = true;
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
      html += '<li><span class="key"><span>'
        + data[index][0] + '</span></span></li>';
    }
    html += '</ol>';
    //hasMore = true;
    if (hasMore) {
      html += '<span class="more property"><span>更多属性</span></span>';
    }
    $('#result_wrapper').after(html + '</div>');
    $('#tag .key').each(function() {
      search.enhanceKey($(this));
    });
    search.enhanceMoreKey(2);
  };

  search.enhanceKey = function(key) {
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
        var hasMore = data.shift() > 20;
        for (var index = 0; index < data.length; ++index) {
          var item = data[index];
          html += '<li class="value"><a href="'
            + search.getTagHref(keyName, item[0]) +'">'
            + '<span>' + item[0] + '</span>' + item[1] + '</a></li>';
          //TODO: selected property
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
        search.enhanceMoreValue(current, keyName, 2);
      });
      current.parent().children('.load').fadeIn('fast');
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
            html += '<li class="value"><a href="'
              + search.getTagHref(keyName, item[0]) + '">'
              + '<span> ' + item[0] + '</span>' + item[1] + '</a></li>';
            //TODO: selected property
          }
          key.next().append(html);
          //hasMore = true;
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