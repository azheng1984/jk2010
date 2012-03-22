var huobiwanjia = {};
/* search suggestion
 *****************************/

/* tracking function
 *****************************/
function trackPageview() {
  $.ajax({
    url:'http://tracking.' + document.domain + '/',
    cache:true,
    dataType:'jsonp',
    jsonp:false
  });
}

/* page tracking
 *****************************/
$(function() {
  //trackPageview();
});