(function($) {
    $('input[name=pcc_event_oc_event_link]').change((event) => {
        let inputValue = event.target.value;

        if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(inputValue)){

            inputValue = inputValue.replace('https://opencollective.com/','');
            let urlArray = inputValue.split('/');
            
            if (urlArray.indexOf('events') > -1) {
                let eventSlug = urlArray[urlArray.indexOf('events') + 1];
                let eventJsonUrl = `https://opencollective.com/${urlArray[0]}/events/${eventSlug}.json`;
                
                $.getJSON(eventJsonUrl, (data) => {
                    if ('id' in data) {
                        $('input[name=pcc_event_oc_event_id]').val(data.id);
                    }
                });
            }
        }
        
    });
})(jQuery);