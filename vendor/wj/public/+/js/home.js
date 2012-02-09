$(function(){
  $('#header input').focus();
  $('#merchant').after('<div id="more"><a href="javascript:void(0)">更多商店</a> 1/12</div>');
  $('#more').click(function(){
    //TODO: append merchant list
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