$(function() {
  $('#result p .link_list').each(function() {
    var self = $(this);
    var html = '';
    $(self.html().split('。')).each(function(propertyIndex, property) {
      if (property == '') {
        return;
      }
      var list = property.split('：');
      if (list.length != 2) {
        html += property + '。';
        return;
      }
      html += list[0];
      var valueList = list[1];
      valueList = valueList.replace(/<\/span>/gi, '</span><span class="gray">');
      valueList = valueList.replace(/<span>/gi, '</span><span class="red">');
      html += '：<a href="#"><span class="gray">' + valueList + '</span></a>。';
    });
    self.html(html);
  });
  if ($('#result').length) {
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