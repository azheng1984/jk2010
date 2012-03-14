/* focus input
 *****************************/
$(function() {
  $('#header input').focus();
});
/*
 * activity slideshow
 *****************************/

/* merchant list
 *****************************/
$(function() {
  var more = $('#more');
  if (more.length === 0) {
    return;
  }
  more.remove();
  $('#list_wrapper').after('<span id="more"><span>更多</span></span>');
  $('#more').hover(function() {
    $(this).css('background-position', '-275px -106px')
      .css('cursor', 'pointer');
    $('#more span').css('color', '#111').css('border-color', '#111');
  }, function() {
    $(this).css('background-position', '-105px -106px')
      .css('cursor', 'default');
    $('#more span').css('color', '#555').css('border-color', '#555');
  });
  $('#more').mouseup(function() {
    alert('load');
  });
});

/* merchant click tracking
 *****************************/