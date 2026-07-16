jQuery(document).ready(function($) {
    var todayDate = moment().startOf('day');
    var YM = todayDate.format('YYYY-MM');
    var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
    var TODAY = todayDate.format('YYYY-MM-DD');
    var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

    $('#igfae-events-calendar').fullCalendar({
        //defaultDate: TODAY,
        firstDay:1,
        themeSystem: 'standard',
        editable: false,
        navLinks: false,
        eventLimit: true, // allow "more" link when too many events
        timeFormat: 'H:mm',
        locale: IGFAE.params.locale,
        displayEventEnd: true,

        header: {
          left: 'prev,next today',
          center: 'title',
          right: ''//'month,agendaWeek,agendaDay,listMonth'
        },
        
        eventColor: '#065ebe',
        events: {
            
            url: IGFAE.params.ajaxurl,
            type: 'POST',
            data: {
                action: 'igfae_events_calendar'
            },
            error: function() {
                console.log('there was an error while fetching events!');
            },
            cache: true
        },

        eventRender: function(eventObj, $el) {
            $el.popover({
                title: eventObj.title,
                content: renderTemplate(eventObj.start, eventObj.end, eventObj.place, eventObj.speaker, eventObj.fullDay, eventObj.url),
                trigger: 'hover',
                placement: 'top',
                container: 'body',
                html: true
            });
        },
    });

    function renderTemplate(start, end, place, speaker, fullDay, url) {
        var result = '<dl class="event-meta">';
        if(start && end){
            if( end.diff(start, 'days') > 0 ) {
                result += '<dt><strong>'+IGFAE.translation.from+':</strong></dt><dd>'+start.format('DD/MM/YYYY HH:mm')+'</dd>';
                result += '<dt><strong>'+IGFAE.translation.to+':</strong></dt><dd>'+end.format('DD/MM/YYYY HH:mm')+'</dd>';
            }
        } else {
            if (start && !fullDay) {
                result += '<dt><strong>'+IGFAE.translation.from+':</strong></dt><dd>'+start.format('HH:mm')+' hs</dd>';
            }

            if (end && !fullDay) {
                result += '<dt><strong>'+IGFAE.translation.to+':</strong></dt><dd>'+end.format('HH:mm')+' hs</dd>';
            }
        }
        
        if (place) {
            result += '<dt><strong>'+IGFAE.translation.place+':</strong></dt><dd>'+place+'</dd>';
        }

        if (speaker) {
            result += '<dt><strong>'+IGFAE.translation.speaker+':</strong></dt><dd>'+speaker+'</dd>'
        }

        result += '</dl>'; 

        /*
        if (url) {
            result += '<a class="btn btn-filled" href="'+speaker+'"> Show more </a>';
        }
        */
        return result;
    };
});