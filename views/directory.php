<?php defined('ALTUMCODE') || die() ?>

<div class="directory-container">

    <div class="container text-center text-md-left">
        <h1 class="directory-heading text-light text-shadow"><?= $language->directory->header ?></h1>

        <p class="lead pt-1 text-light text-shadow"><?= $language->directory->subheader ?></p>
    </div>

</div>

<div class="container mt-5">

    <div>
        <?php display_notifications() ?>
    </div>

    <div id="loading" class="text-center">
        <div class="lds-ripple"><div></div><div></div></div>
    </div>

    <div id="filters"></div>

    <div id="results"></div>

    <input type="hidden" name="directory_pagination" value="<?= $settings->directory_pagination ?>" />
    <input type="hidden" name="global_form_token" value="<?= Security::csrf_get_session_token('global_form_token') ?>" />
    <input type="hidden" name="directory_ajax" value="<?= $settings->url . $route . 'directory_ajax' ?>" />
</div>

<script>
    let has_more_results = false;

    let custom_get = (start = 0, concat_html = true, get_filters = false, filters = {}) => {
        let results = $('#results');

        let global_form_token = $('input[name="global_form_token"]').val();
        let limit = $('input[name="directory_pagination"]').val();
        let url = $('input[name="directory_ajax"]').val();

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                start,
                limit,
                filters,
                global_form_token
            },
            success: (data) => {
                if(data.status == 'error') {
                    alert('Please try again later, something is not working properly.');
                }

                else if(data.status == 'success') {
                    /* Checks on show more button */
                    if(has_more_results) {
                        $('#show_more').remove();
                    }

                    $('#loading').hide();

                    /* Concat or no */
                    if(concat_html) {
                        let result = $(data.details.html).hide();
                        results.append(result);
                        result.fadeIn('slow');
                    } else {
                        results.html(data.details.html).hide().fadeIn('slow');
                    }

                    /* Filters */
                    if(get_filters) {
                        $('#filters').html(data.details.filters_html);
                    }

                    /* Refresh tooltips */
                    $('[data-toggle="tooltip"]').tooltip();

                    /* Checks on show more button */
                    if(data.details.has_more) {
                        has_more_results = true;
                        start = parseInt(start) + parseInt(limit);
                    }

                    /* Show more event handling */
                    $('#show_more').off().on('click', (event) => {

                        $(event.currentTarget).attr('disabled', true);

                        custom_get(start, true, false, filters);

                        event.preventDefault();
                    });

                    $('.dropdown-menu[data-no-toggle]').on('click.bs.dropdown', (e) => { e.stopPropagation(); e.preventDefault(); });

                    /* Filters */
                    let delay_timer = null;

                    /* order_by_filter filter */
                    $('select[name="order_by_filter"]').off().on('change', (event) => {

                        let order_by_filter = $(event.currentTarget).find(':selected').val();

                        filters = { ...filters, order_by_filter };

                        /* Refresh the results */
                        custom_get(0, false, false, filters);

                    });

                    /* order_by_type filter */
                    $('select[name="order_by_type"]').off().on('change', (event) => {

                        let order_by_type = $(event.currentTarget).find(':selected').val();

                        filters = { ...filters, order_by_type };

                        /* Refresh the results */
                        custom_get(0, false, false, filters);

                    });

                    /* Bio filter */
                    $('input[name="bio_filter"]').off().on('keyup', (event) => {

                        if(delay_timer) {
                            clearTimeout(delay_timer);
                        }

                        delay_timer = setTimeout(() => {

                            let bio_filter = $(event.currentTarget).val().trim();

                            if(bio_filter != '') {
                                filters = {...filters, bio_filter};

                                /* Refresh the results */
                                custom_get(0, false, false, filters);
                            }

                        }, 350);

                    });

                    /* followers_from_filter filter */
                    $('select[name="followers_from_filter"]').off().on('change', (event) => {

                        let followers_from_filter = $(event.currentTarget).find(':selected').val();

                        filters = { ...filters, followers_from_filter };

                        /* Refresh the results */
                        custom_get(0, false, false, filters);

                    });

                    /* followers_to_filter filter */
                    $('select[name="followers_to_filter"]').off().on('change', (event) => {

                        let followers_to_filter = $(event.currentTarget).find(':selected').val();

                        filters = { ...filters, followers_to_filter };

                        /* Refresh the results */
                        custom_get(0, false, false, filters);

                    });


                    /* engagement_from_filter filter */
                    $('select[name="engagement_from_filter"]').off().on('change', (event) => {

                        let engagement_from_filter = $(event.currentTarget).find(':selected').val();

                        filters = { ...filters, engagement_from_filter };

                        /* Refresh the results */
                        custom_get(0, false, false, filters);

                    });

                    /* engagement_to_filter filter */
                    $('select[name="engagement_to_filter"]').off().on('change', (event) => {

                        let engagement_to_filter = $(event.currentTarget).find(':selected').val();

                        filters = { ...filters, engagement_to_filter };

                        /* Refresh the results */
                        custom_get(0, false, false, filters);

                    });

                    /* Active filters */
                    $('#active_filters').html(data.details.active_filters_html).hide().fadeIn('slow');


                    $('#bio_filter_remove').off().on('click', (event) => {
                        event.preventDefault();

                        let { bio_filter, ...new_filters} = filters;

                        filters = new_filters;

                        /* Refresh the results */
                        custom_get(0, false, true, filters);

                    });

                    $('#order_by_filter_remove').off().on('click', (event) => {

                        let { order_by_filter, ...new_filters} = filters;

                        filters = new_filters;

                        /* Refresh the results */
                        custom_get(0, false, true, filters);

                        event.preventDefault();
                    });

                    $('#order_by_type_remove').off().on('click', (event) => {

                        let { order_by_type, ...new_filters} = filters;

                        filters = new_filters;

                        /* Refresh the results */
                        custom_get(0, false, true, filters);

                        event.preventDefault();
                    });

                    $('#followers_from_filter_remove').off().on('click', (event) => {

                        let { followers_from_filter, ...new_filters} = filters;

                        filters = new_filters;

                        /* Refresh the results */
                        custom_get(0, false, true, filters);

                        event.preventDefault();
                    });

                    $('#followers_to_filter_remove').off().on('click', (event) => {

                        let { followers_to_filter, ...new_filters} = filters;

                        filters = new_filters;

                        /* Refresh the results */
                        custom_get(0, false, true, filters);

                        event.preventDefault();
                    });

                    $('#engagement_from_filter_remove').off().on('click', (event) => {

                        let { engagement_from_filter, ...new_filters} = filters;

                        filters = new_filters;

                        /* Refresh the results */
                        custom_get(0, false, true, filters);

                        event.preventDefault();
                    });

                    $('#engagement_to_filter_remove').off().on('click', (event) => {

                        let { engagement_to_filter, ...new_filters} = filters;

                        filters = new_filters;

                        /* Refresh the results */
                        custom_get(0, false, true, filters);

                        event.preventDefault();
                    });



                }
            },
            dataType: 'json'
        });
    };



    $(document).ready(() => {

        custom_get(0, false, true);

    })
</script>