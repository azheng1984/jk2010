/* search suggestion
 *****************************/

/* click tracking function
 *****************************/

/* page tracking
 *****************************/
$(function() {
  $.ajax({
    url:'http://tracking.' + document.domain + '/',
    cache:true,
    dataType:'jsonp',
    jsonp:false
  });
});