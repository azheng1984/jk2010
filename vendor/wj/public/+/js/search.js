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
  $('#result_wrapper').after('<div id="tag"><h2>分类:</h2><ol><li><a href=""><span>礼品</span> 23</a></li></ol></div>');
});