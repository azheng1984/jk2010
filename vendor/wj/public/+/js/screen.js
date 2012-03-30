var huobiwanjia = function() {
  var args = {};
  if (location.search !== '') {
    var queryString = location.search.charAt(0) === '?' ?
       location.search.substring(1) : location.search;
    var regex = /([^=&]+)(=([^&]*))?/g;
    while (match = regex.exec(queryString)) {
      var key = decodeURIComponent(match[1].replace(/\+/g,' '));
      var value = decodeURIComponent(match[3].replace(/\+/g,' '));
      args[key] = value;
    }
  }
  return { queryString: args };
}();

/* search suggestion
 *****************************/
$(function() {
  $('#header input').attr('autocomplete', 'off');
});

/* tracking function
 *****************************/
huobiwanjia.trackPageview = function() {
  $.ajax({
    url:'http://tracking.' + document.domain + '/',
    cache:true,
    dataType:'jsonp',
    jsonp:false
  });
};

/* page tracking
 *****************************/
$(function() {
  //huobiwanjia.trackPageview();
});

/* parse query string
 *****************************/
$(function() {
  
});