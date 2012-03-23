huobiwanjia.home = {};
/* focus input
 *****************************/
$(function() {
  $('#header input').focus();
});

/*
 * slide list
 *****************************/
$(function() {
  var index = 0;
  $('#slide_list a').each(function() {
    var id = 'slide-' + index;
    $(this).replaceWith('<span id="' + id + '" class="item"></span>');
    var item = $('#' + id);
    item.hover(function() {
      $(this).addClass('hover');
    }, function() {
      $(this).removeClass('hover');
    });
    item.click(function() {
      alert('ajax');
    });
    ++index;
  });
});

/* merchant list
 *****************************/
$(function() {
  var index = 0;
  $('#merchant_list a').each(function() {
    var id = 'merchant-' + index;
    $(this).replaceWith('<span id="' + id + '" class="item">'+$(this).html()+'</span>');
    $('#' + id).hover(function() {
      $(this).addClass('hover');
    }, function() {
      $(this).removeClass('hover');
    });
    ++index;
  });
  $('#down').replaceWith('<span id="down" class="full"></a>');
  $('#down').hover(function() {
    $(this).addClass('hover');
  }, function() {
    $(this).removeClass('hover');
  });
});

/* merchant click tracking
 *****************************/
