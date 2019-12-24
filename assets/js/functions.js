let favorite = (event) => {
    let $event = $(event.currentTarget);
    let source_user_id = $event.data('id');
    let source = $event.data('source');
    $event.fadeOut();

    $.ajax({
        type: 'POST',
        url: 'favorites',
        data: {
            source_user_id,
            source
        },
        success: (data) => {
            if(data.status == 'error') {
                alert('Please try again later, something is not working properly.');
            }

            else if(data.status == 'success') {
                $event.fadeIn().html(data.details.html);
            }
        },
        dataType: 'json'
    });

}