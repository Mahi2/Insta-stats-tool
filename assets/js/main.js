$(document).ready(() => {
	/* Submit disable after 1 click */
	$('[type=submit][name=submit]').on('click', (event) => {
		$(event.currentTarget).addClass('disabled');

		setTimeout(() => {
            $(event.currentTarget).removeClass('disabled');
        }, 3000)
    });
    
    /* Confirm delete handler */
	$('body').on('click', '[data-confirm]', (event) => {
		let message = $(event.currentTarget).attr('data-confirm');

		if(!confirm(message)) return false;
    });
    
    /* Enable tooltips everywhere */
	$('[data-toggle="tooltip"]').tooltip();
});