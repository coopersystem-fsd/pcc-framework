(function($) {
    
    let $inputEventUrl = $('input[name=pcc_event_oc_event_link]');
    let $inputEventId = $('input[name=pcc_event_oc_event_id]');
    let $inputEventEmbedUrl = $('input[name=pcc_event_oc_event_embed_link]');

    $inputEventUrl.change((event) => {
        let inputValue = event.target.value;
        $inputEventEmbedUrl.val('');

        if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(inputValue)){

            let urlArray = inputValue.split('/');
            let eventSlugIndex = urlArray.indexOf('events') + 1;
            let tierSlugIndex = urlArray.indexOf('contribute') + 1;
            
            if (eventSlugIndex > 0) {
                let eventSlug = urlArray[eventSlugIndex];

                if (tierSlugIndex > 0 && urlArray[tierSlugIndex] !== undefined && urlArray[tierSlugIndex] !== '') {
                    let tierSlug = urlArray[tierSlugIndex];
                    let embedLink = `https://opencollective.com/embed/${eventSlug}/contribute/${tierSlug}`;

                    $inputEventEmbedUrl.val(embedLink);
                }
                
                /**
                 * TODO: Check which ID will be used: the Event ID or Tier ID
                 */
                // let eventJsonUrl = `https://opencollective.com/${urlArray[0]}/events/${eventSlug}.json`;
                
                // $.getJSON(eventJsonUrl, (data) => {
                //     if ('id' in data) {
                //         $inputEventId.val(data.id);
                //     }
                // });
            }
        }
        
    });
})(jQuery);