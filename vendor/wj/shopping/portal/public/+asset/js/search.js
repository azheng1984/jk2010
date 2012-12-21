(function() {
  var search = {
    queryString: '',
    propertyList: {}
  };

  search.initialize = function() {
    /* query string */
    var argumentList = [], query = huobiwanjia.argumentList;
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
      var self = $(this), text = self.text();
      if (self.is('span:not(.next)')) {
        keyName = text.substr(0, text.length - 1);
        search.propertyList[keyName] = [];
        return;
      }
      if (self.is('.tag') && text !== '同款') {
        search.propertyList[keyName].push(encodeURIComponent(text));
      }
    });
  };

  search.renderPriceRange = function() {
    var form =
      '<form id="price_range" action="."><label for="price_from">&yen;</label>',
      query = huobiwanjia.argumentList,
      priceFrom = typeof query.price_from === 'undefined' ?
        '' : query.price_from,
      priceTo = typeof query.price_to === 'undefined' ? '' : query.price_to;
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
      }).keydown(function(event) {
        if(event.which === 13){
          $('#price_range').submit();
        }
      }).mousedown(function() {
        $(this).addClass('no_outline');
      }).mouseout(function() {
        $(this).removeClass('no_outline');
      });
    });
  };

  search.enhanceProductTagList = function() {
    $('#result li').each(function() {
      var self = $(this), valueList = self.children('.value');
      if (valueList.length === 0) {
        return;
      }
      var list = self.html().split(': ');
      if (list.length < 2) {
        return;
      }
      var keyName = list[0];
      valueList.each(function() {
        var self = $(this),
          html = self.html(),
          valueName = html.replace(/<\/span>/gi, '').replace(/<span>/gi, '');
        html = html.replace(/<\/span>/gi, '</span><span class="gray">')
          .replace(/<span>/gi, '</span><span class="red">');
        self.replaceWith('<a href="' + search.getTagHref(keyName, valueName)
          + '"><span class="gray">' + html + '</span></a>');
      });
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
    var href = '../';
    if (sectionList.length !== 0) {
      href += sectionList.join('&') + '/';
    }
    return href + search.queryString;
  };

  search.appendTagHref = function(keyName, valueName) {
    var sectionList = window.location.pathname.split('/'), href = '';
    if (sectionList.length === 5) {
      href = '../' + sectionList[3] + '&';
    }
    return href + encodeURIComponent(keyName) + '='
      + encodeURIComponent(valueName) + '/' + search.queryString;
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

  search.renderTagList = function() {
    if ($('#no_result').length !== 0) {
      return;
    }
    if (window.location.pathname.split('/').length === 3) {
      search.getTag(null, 1, search.renderCategoryList);
      return;
    }
    search.getTag(null, 1, search.renderPropertyList);
  };

  search.getTag = function(keyName, page, render) {
    var path = window.location.pathname,
      url = path.substr(0, path.lastIndexOf("/") + 1) + '?media=json';
    if (keyName !== null) {
      url += '&key=' + encodeURIComponent(keyName);
    }
    if (page !== 1) {
      url += '&page=' + page;
    }
    $.getJSON(url, function(data) {
      var hasMore = data.shift() > page * 20;
      render(data, hasMore);
    });
  };

  search.renderCategoryList = function(data, hasMore) {
    $('#result_wrapper').after('<div id="tag"><h2>分类:</h2><ol>'
      + search.renderValueList('分类', data) + '</ol></div>');
    search.enhanceCategoryList(hasMore, 1);
  };

  search.renderValueList = function(keyName, data) {
    var html = '';
    $.each(data, function(index, item) {
      html += search.renderValue(keyName, item[0], item[1]);
    });
    return html;
  };

  search.renderValue = function(keyName, valueName, productAmount) {
    if (typeof search.propertyList[keyName] !== 'undefined'
      && $.inArray(
        encodeURIComponent(valueName), search.propertyList[keyName]
      ) !== -1) {
      return '<li class="value"><a class="selected" href="'
        + search.getTagHref(keyName, valueName) +'">'
        + valueName + '<span></span></a></li>';
    }
    return '<li class="value"><a href="'
      + search.getTagHref(keyName, valueName) +'">'
      + '<span>' + valueName + '</span>' + productAmount + '</a></li>';
  };

  search.enhanceCategoryList = function(hasMore, page) {
    if (hasMore === false) {
      return;
    }
    var wrapper = $('#tag ol');
    if (page === 5) {
      wrapper.append('<li>…</li>');
      return;
    }
    wrapper.after('<span class="more" tabindex="0"><span>更多</span></span>');
    search.enhanceMoreCategory(page);
  };

  search.enhanceMoreCategory = function(page) {
    search.enhanceMore($('#tag .more'), page, function (more, page) {
      more.replaceWith('<span class="load">正在加载…</span>');
      var load = $('#tag .load');
      search.getTag(null, ++page, function(data, hasMore) {
        load.remove();
        $('#tag ol').append(search.renderValueList('分类', data));
        search.enhanceCategoryList(hasMore, page);
      });
      load.fadeIn('fast');
    });
  };

  search.enhanceMore = function(more, page, click) {
    more.click(function() {
      click(more, page);
    }).bind('mouseenter mouseleave', function() {
      more.toggleClass('more_hover');
    }).keypress(function(event) {
      if(event.which === 13){
        click(more, page);
      }
    });
  };

  search.renderPropertyList = function(data, hasMore) {
    $('#result_wrapper').after('<div id="tag"><h2>属性:</h2><ol>'
      + search.renderKeyList(data) + '</ol></div>');
    search.enhanceKeyList(hasMore, 1);
  };

  search.renderKeyList = function(data) {
    var html = '';
    $.each(data, function(index, item) {
      var multiple = item[1] === 0 ? '' : ' multiple';
      html += '<li class="new"><span class="key' + multiple
        + '" tabindex="0"><span>' + item[0] + '</span></span></li>';
    });
    return html;
  };

  search.enhanceKeyList = function(hasMore, page) {
    $('#tag > ol > .new').each(function() {
      $(this).removeClass('new').children().click(function() {
        var self = $(this);
        search.toggleKey(self);
        search.toggleKeyHoverClass(self);
      }).keypress(function(event) {
        if(event.which === 13){
          search.toggleKey($(this));
        }
      }).mousedown(function() {
        $(this).addClass('no_outline').attr('hideFocus', true);
      }).focusout(function() {
        $(this).removeClass('no_outline').removeAttr('hideFocus');
      }).bind('mouseenter mouseleave', function() {
        //ie6 不支持 span:hover 和 .open.hover
        var self = $(this), className = 'hover';
        if (self.is('.open')) {
          className = 'open_hover';
        }
        self.toggleClass(className);
      });
    });
    if (hasMore === false) {
      return;
    }
    var ol = $('#tag').children('ol');
    if (page === 5) {
      ol.append('<li>…</li>');
      return;
    }
    ol.after('<span class="more" tabindex="0"><span>更多属性</span></span>');
    search.enhanceMoreKey(page);
  };

  search.toggleKey = function(key) {
    if (key.is('.open')) {
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
    if (search.propertyList[keyName] && key.is('.multiple') === false) {
      key.after('<ol>' + search.renderValue(
        keyName, decodeURIComponent(search.propertyList[keyName][0])
      ) + '</ol>');
      return;
    }
    search.loadPropertyValueList(key, keyName, 1, null);
  };

  search.toggleKeyHoverClass = function(key) {
    if (key.is('.open')) {
      key.removeClass('hover').addClass('open_hover');
      return;
    }
    key.removeClass('open_hover').addClass('hover');
  };

  search.loadPropertyValueList = function(key, keyName, page, more) {
    if (more === null) {
      more = key.after('<span></span>').next();
    }
    more.replaceWith('<span class="load">正在加载…</span>');
    var load = key.next('.load');
    search.getTag(keyName, 1, function(data, hasMore) {
      load.remove();
      var isHidden = key.is('.open') === false, html = '<ol';
      if (isHidden) {
        html += ' class="hidden"';
      }
      html += '>' + search.renderValueList(keyName, data)  +'</ol>';
      key.after(html);
      if (hasMore === false) {
        return;
      }
      var ol = key.next();
      if (page === 5) {
        ol.append('<li>…</li>');
        return;
      }
      var hidden = isHidden ? ' hidden' : '';
      ol.after('<span class="more'
        + hidden + '" tabindex="0"><span>更多</span></span>');
      search.enhanceMore(key.next('.more'), null, function(more) {
        search.loadPropertyValueList(key, keyName, page + 1, more);
      });
    });
    load.fadeIn('fast');
  };

  search.enhanceMoreKey = function(page) {
    search.enhanceMore($('#tag > .more'), page, function(more, page) {
      more.replaceWith('<span class="load">正在加载属性…</span>');
      var load = $('#tag > .load');
      search.getTag(null, ++page, function(data, hasMore) {
        load.remove();
        $('#tag').children('ol').append(search.renderKeyList(data));
        search.enhanceKeyList(hasMore, page);
      });
      load.fadeIn('fast');
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