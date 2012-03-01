$(function() {
  if ($('#breadcrumb .section').length === 0) {
    return;
  }
  //TODO build selected list via breadcrumb
  //TODO separate path & query string
  $('#result p .link_list').each(function() {
    var self = $(this);
    var propertyList = [];
    var list = self.html().split('。');
    for (var index  = 0; index < list.length; ++index) {
      var list2 = list[index].split('…');
      for (var index2  = 0; index2 < list2.length; ++index2) {
        if (index2 !== list2.length - 1) {
          propertyList.push(['…', list2[index2]]);
          continue;
        }
        propertyList.push(['。', list2[index2]]);
       }
    }
    var html = '';
    for (var index  = 0; index < propertyList.length; ++index) {
      var property = propertyList[index];
      var list = property[1].split('：');
      if (list.length !== 2) {
        if (property[1] !== '') {
          html += property[1] + property[0];
        }
        continue;
      }
      var name = list[0];
      var valueList = list[1].split('；');
      html += name + '：';
      for (var index2  = 0; index2 < valueList.length; ++index2) {
        var value = valueList[index2];
        value = value.replace(/<\/span>/gi, '</span><span class="gray">')
          .replace(/<span>/gi, '</span><span class="red">');
        //TODO:append path if not selected
        html += '<a href="#"><span class="gray">' + value + '</span></a>';
        if (index2 !== valueList.length - 1) {
          html += '；';
        }
      }
      html += property[0];
    }
    self.html(html);
  });
});
$(function() {
  if ($('#result').length !== 0) {
    //TODO
    $('#result_wrapper').after('<div id="tag"><h2>分类:</h2><ol><li><a href=""><span>礼品</span> 23</a></li></ol></div>');
  }
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
    $('#price_range').append('<a id="price_range_button" href="javascript:$(\'#price_range\').submit()">确定</a>');
  });
});