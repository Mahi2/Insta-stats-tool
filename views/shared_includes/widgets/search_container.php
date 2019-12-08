<?php defined('ALTUMCODE') || die() ?>

<div class="search-container d-flex flex-column align-items-center justify-content-center">

    <h3 class="font-weight-bolder text-dark mb-5"><?= $language->global->menu->search_title ?></h3>
    <form class="form-inline d-inline-flex justify-content-center search_form" action="" method="GET">
        <?php if(count($sources) > 1): ?>
        <div class="dropdown my-2 mr-2">
            <button class="btn btn-light index-source-button dropdown-toggle border-0" data-source="<?= reset($sources) ?>" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="<?= $language->{reset($sources)}->global->icon ?>"></i> <?= $language->{reset($sources)}->global->name ?></button>

            <div class="dropdown-menu">
                <?php foreach($sources as $source): ?>
                    <a class="dropdown-item source-select-item" href="#" data-source="<?= $source ?>"><i class="fab fa-<?= $source ?>"></i> <?= ucfirst($source) ?></a>
                <?php endforeach ?>
            </div>
        </div>
        <?php endif ?>

        <div class="my-2 index-input-div">
            <i class="fa fa-search text-black-50 index-search-input-icon"></i>
            <input class="form-control mr-2 index-search-input border-0 form-control-lg source_search_input" type="text" placeholder="<?= $language->global->menu->search_placeholder ?>" aria-label="<?= $language->global->menu->search_placeholder ?>">
        </div>

        <button type="submit" class="btn btn-<?= reset($sources) ?> index-submit-button border-0 d-inline-block my-2"><?= $language->global->search ?></button>
    </form>

</div>



<script defer>
    $(document).ready(() => {
        <?php if(count($sources) > 1): ?>
        $('.source-select-item').on('click', (event) => {
            let $this = $(event.currentTarget);
            let source = $this.data('source');
            let source_content = $this.html();

            $this.closest('form').find('.index-source-button').html(source_content).attr('data-source', source);

            switch(source) {
            <?php if($plugins->exists_and_active('instagram')): ?>
                case 'instagram':
                    $this.closest('form').find('.source_search_input').attr('placeholder', '<?= $language->instagram->global->search_placeholder ?>');
                    break;
            <?php endif ?>

            <?php if($plugins->exists_and_active('facebook')): ?>
                case 'facebook':
                    $this.closest('form').find('.source_search_input').attr('placeholder', '<?= $language->facebook->global->search_placeholder ?>');
                    break;
            <?php endif ?>

            <?php if($plugins->exists_and_active('youtube')): ?>
                case 'youtube':
                    $this.closest('form').find('.source_search_input').attr('placeholder', '<?= $language->youtube->global->search_placeholder ?>');
                    break;
            <?php endif ?>

            <?php if($plugins->exists_and_active('twitter')): ?>
                case 'twitter':
                    $this.closest('form').find('.source_search_input').attr('placeholder', '<?= $language->twitter->global->search_placeholder ?>');
                    break;
            <?php endif ?>
        }

        /* Change the class of the submit button */
        $this.closest('form').find('button[type="submit"]').removeClass('btn-facebook btn-youtube btn-twitter btn-instagram').addClass(`btn-${source}`);

        event.preventDefault();
    });
    <?php endif ?>

        $('.search_form').on('submit', (event) => {

            let source = $(event.currentTarget).closest('form').find('.index-source-button').length ? $(event.currentTarget).closest('form').find('.index-source-button').attr('data-source') : <?= json_encode(reset($sources)) ?>;
            let search_input = $(event.currentTarget).find('.source_search_input').val();
            let username_array = [];
            let is_full_url = false;

            switch(source) {
                case 'instagram':
                    search_input.split('/').forEach((string) => {
                        if(string.trim() != '') {
                            username_array.push(string);

                            if(string.includes('instagram.com')) {
                                is_full_url = username_array.length - 1;
                            }
                        }
                    });
                    break;

                case 'facebook':
                    search_input.split('/').forEach((string) => {
                        if(string.trim() != '') {
                            username_array.push(string);

                            if(string.includes('facebook.com')) {
                                is_full_url = username_array.length - 1;
                            }
                        }
                    });
                    break;

                case 'youtube':
                    search_input.split('/').forEach((string) => {
                        if(string.trim() != '') {
                            username_array.push(string);
                        }
                    });
                    break;

                case 'twitter':
                    search_input.split('/').forEach((string) => {
                        if(string.trim() != '') {
                            username_array.push(string);

                            if(string.includes('twitter.com')) {
                                is_full_url = username_array.length - 1;
                            }
                        }
                    });
                    break;
            }


            let username = is_full_url !== false ? username_array[is_full_url + 1] : username_array[username_array.length - 1];

            if(username.length > 0) {

                setTimeout(() => {
                    $('body').fadeOut(() => {
                        $('body').html('<div class="vw-100 vh-100 d-flex align-items-center"><div class="col-2 text-center mx-auto" style="width: 3rem; height: 3rem;"><div class="spinner-grow"><span class="sr-only">Loading...</span></div></div></div>').show();
                    });

                    setTimeout(() => window.location.href = `<?= $settings->url ?>report/${username}/${source}`, 100)
                }, 0)

            }

            event.preventDefault();
        });
    })
</script>
