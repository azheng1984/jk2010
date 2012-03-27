var huobiwanjia = {};

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
  huobiwanjia.queryString = {};
  if (location.search != '') {
    var queryString = location.search.charAt(0) === '?' ?
      location.search : location.search.substring(1);
    var regex = /([^=&]+)(=([^&]*))?/g;
    while (match = regex.exec(queryString)) {
      var key = decodeURIComponent(match[1].replace(/\+/g,' '));
      var value = decodeURIComponent(match[3].replace(/\+/g,' '));
      huobiwanjia.queryString[key] = value;
    }
  }
});