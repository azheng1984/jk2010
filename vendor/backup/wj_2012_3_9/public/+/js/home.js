$(function() {
  $('#header input').focus();
});
//TODO:merchant list pagination enhancement
$(function() {
  $('#more').click(function() {
    return false;
  });
});


$(function() {
  $('#merchant td a').each(function() {
    $(this).bind('mousedown', function(button) {
      if (button.which == 1) {
        alert($(this).attr('href'));
        //商家 url + session，通过  jsonp 的方式发送到 tracking 服务器 click.huobiwanjia.com
      }
    });
  });
});