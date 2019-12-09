<?php defined('ALTUMCODE') || die();

if($plugins->exists_and_active($source)) {
    require_once $plugins->require($source, 'views/compare');
}

?>

<input type="hidden" id="source" value="<?= $source ?>" />

<script>
    $(document).ready(() => {

        $('#compare_search_form').on('submit', (event) => {
            let source = $('#source').val();
            let user_one = $('#user_one').val();
            let user_two = $('#user_two').val();


            let user_one_array = [];
            let user_two_array = [];

            user_one.split('/').forEach((string) => {
                if(string.trim() != '') user_one_array.push(string);
            });

            user_two.split('/').forEach((string) => {
                if(string.trim() != '') user_two_array.push(string);
            });

            let username_one = user_one_array[user_one_array.length-1];
            let username_two = user_two_array[user_two_array.length-1];


            if(username_one.length > 0 || username_two.length > 0) {

                $('body').addClass('animated fadeOut');
                setTimeout(() => {
                    window.location.href = `<?= $settings->url ?>compare/${source}/${username_one}/${username_two}`;
                }, 70)

            }

            event.preventDefault();
        });
    })
</script>