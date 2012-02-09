function bindEvent() {
  $('#target:parent li').hover(
      function() {
        $(this).attr('class', 'current');
        $('.value_list .current .delete').show();
        $('.value_list .current .delete').mouseover(function() {
          $property = $(this).parent().children('a').first();
          if ($property.attr('class') == 'selected') {
            $property.attr('class', 'line-through selected');
            return;
          }
         $property.attr('class', 'line-through gray-color');
        });
        $('.value_list .current .delete').mouseout(function() {
          $property = $(this).parent().children('a').first();
          if ($property.attr('class') == 'line-through gray-color') {
            $property.attr('class', '');
            return;
          }
          $property.attr('class', 'selected');
        });
      },function() {
        $('.value_list .current .delete').off('mouseover');
        $('.value_list .current .delete').off('mouseout');
        $('.value_list .current .delete').hide();
        $('.value_list .current').attr('class', '');
      }
  );
}
$(function() {
  $uri = window.location.pathname + '?media=json';
  $.get($uri, function(data) {
    $('#result').after('<div id="filter">' + data + '</div>');
    $('#key_list .key').mouseup(function() {
      if ($(this).attr('class') === 'key open') {
        $(this).attr('class', 'key');
        $(this).parent().children('ol').remove();
        return;
      }
      $uri2 = window.location.pathname + '?key=' + $(this).text() + '&media=json';
      $(this).attr('id', 'target');
      $.get($uri2, function(data) {
        bindEvent();
        $('#target').after(data).attr('id', '').attr('class', 'key open');
      });
    });
    bindEvent();
  });
});