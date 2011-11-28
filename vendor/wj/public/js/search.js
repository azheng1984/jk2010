$(function() {
  $uri = window.location.pathname + '?anchor=' + window.location.hash.replace('#', '') + '&media=json';
  $.get($uri, function(data) {
    $('#filter').html(data);
    //$('.result').html(data);
    //alert('Load was performed.');
  });
  var isHover = false;
  $('#result ol li').hover(function() {
    if (isHover == true) {
      return;
    }
    isHover = true;
    $(this).append('<div id="hover"><div class="toolbar"><a href="javascript:void(0)">同款</a> <a href="javascript:void(0)">关注</a></div><div class="tag"><a href="javascript:void(0)">分类: 手机</a></div>'
    + '</div>');
    $('#hover').fadeIn();
  }, function() {
    $('#hover').remove();
    isHover = false;
  });
});
