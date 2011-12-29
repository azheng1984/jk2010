$(function() {
  var isHover = false;
  $('#result ol li').hover(function() {
    if (isHover == true) {
      return;
    }
    isHover = true;
    $(this).append('<div id="hover"><div class="toolbar"><a href="javascript:void(0)"><span> </span>同款</a> <a href="javascript:void(0)"><span class="heart"> </span>关注</a></div><div class="tag"><a href="javascript:void(0)">分类: 手机</a></div>'
    + '</div>');
  }, function() {
    $('#hover').remove();
    isHover = false;
  });
});