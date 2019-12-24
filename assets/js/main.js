$(document).ready(() => {
	/* Submit disable after 1 click */
	$('[type=submit][name=submit]').on('click', (event) => {
		$(event.currentTarget).addClass('disabled');

		setTimeout(() => {
            $(event.currentTarget).removeClass('disabled');
        }, 3000)
	});