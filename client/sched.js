Conference = function($conference, data, options ) {  
    var $ = jQuery;
    var settings = $.extend( {}, options);
    var container = $('<div class="container"/>');
    
    data.sort(Conference.date_sort_asc);
    
    $.each(data, function callback(index, eventData) {
      var event = $(
        '<div class="event">'
      + '<div class="event_start"/>'
      + '<div class="event_end"/>'
      + '<div class="name"/>'
      + '<div class="clear:both"/>'
      + '<div class="description"/>'
      + '</div>'
      );

      
      $.each(conference_fields, function (k, v) {
        var $field = event.find('.' + k);
        $field.append(v.transform ? v.transform(eventData[k]) : eventData[k]);
        v.click && $field.click(v.click);
      });
      
      if (index > 0 && data[index - 1].event_start == eventData.event_start)
      {
        event.find('.event_start').remove();
      }
      
      container.append(event);
    });
    
    $conference.append(container);

};


Conference.transform_date = function (d) {return d.split(' ')[1];}
//Conference.transform_date = function (d) {return (new Date(d)).getHours() + ':'+(new Date(d)).getMinutes();}

Conference.name_click = function() {jQuery(this).parent().find('.description').toggle('fast');}

Conference.date_sort_asc = function (a, b) {
  if (a.event_start > b.event_start) return 1;
  if (a.event_start < b.event_start) return -1;
  return 0;
};

var conference_fields = {
  event_start: {
    transform: Conference.transform_date
  },
  event_end: {
    transform: Conference.transform_date
  },
  name: {
    click: Conference.name_click
  },
  description: {}
};