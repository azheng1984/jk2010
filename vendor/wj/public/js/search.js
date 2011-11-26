$(function() {
  $uri = window.location.pathname + '?anchor=' + window.location.hash.replace('#', '') + '&media=json';
  $.get($uri, function(data) {
    $('#filter').html(data);
    //$('.result').html(data);
    //alert('Load was performed.');
  });
});