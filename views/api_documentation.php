<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <div class="col-md-8 mb-3 mb-md-0">

        <div class="card card-shadow">
            <div class="card-body">
                <h4><?= $language->api_documentation->header ?></h4>
                <div><?php printf($language->api_documentation->subheader) ?></div>

                <hr />

                <span class="text-muted"><?= $language->api_documentation->display->text1 ?></span>

                <h5 class="mt-4"><?= $language->api_documentation->display->api_key ?></h5>
                <input type="text" class="form-control clickable" value="<?= $account->api_key ?>" onclick="this.select();">

                <h5 class="mt-4"><?= $language->api_documentation->display->api_url ?></h5>
                <input type="text" class="form-control clickable" value="<?= $settings->url . 'api' ?>" onclick="this.select();">

                <h5 class="mt-4"><?= $language->api_documentation->display->format ?></h5>
                <input type="text" class="form-control clickable" value="<?= $settings->url . 'api?api_key={YOUR_API_KEY}&username={SOURCE_ACCOUNT_USERNAME}&source=instagram' ?>" onclick="this.select();">


                <h5 class="mt-4"><?= $language->api_documentation->display->method ?></h5>
                <code>
                    GET
                </code>

                <h5 class="mt-4"><?= $language->api_documentation->display->url_params ?></h5>
                <span class="text-muted"><?= $language->api_documentation->display->url_params_help ?></span>

                <h6 class="mt-2"><?= $language->api_documentation->display->required ?></h6>
                <ul>
                    <li>api_key={YOUR_API_KEY}</li>
                    <li>username={SOURCE_ACCOUNT_USERNAME}</li>
                    <li>source={SOURCE_NAME}</li>
                </ul>
                <span class="text-muted"><?= $language->api_documentation->display->source_help ?></span>


                <h5 class="mt-4"><?= $language->api_documentation->display->success_response ?></h5>
                <span class="text-muted"><?= $language->api_documentation->display->success_response_help ?></span>

                <ul>
                    <li><strong>Code:</strong> <code>200</code></li>
                    <li><strong>Content:</strong></li>
                </ul>

                <pre id="api_sample_response">
                    {"id":"1818","instagram_id":"25945306","username":"badgalriri","full_name":"badgalriri","description":"READ my cover story at @voguemagazine","website":"http:\/\/ri-hanna.io\/vogue","followers":"62212664","following":"1304","uploads":"4278","added_date":"2018-05-05 18:33:53","last_check_date":"2018-05-05 18:33:53","profile_picture_url":"https:\/\/instagram.fsbz1-1.fna.fbcdn.net\/vp\/667a62925a82cf1445a7e800239ff35b\/5B792186\/t51.2885-19\/11032926_1049846535031474_260957621_a.jpg","is_private":"0","is_verified":"1","average_engagement_rate":"3.35","details":{"total_likes":20654521,"total_comments":178430,"average_comments":"17,843.00","average_likes":"2,065,452.10","top_hashtags":{"SAVAGEX":1,"OnTheReg":1,"DAMN":1},"top_mentions":{"voguemagazine":3,"savagexfenty":2,"mertalas":2,"macpiggott":2,"fentybeauty":1,"redhotnails":1,"lisaeldridgemakeup":1,"yusefhairnyc":1,"tonnegood":1,"louboutinworld":1,"voguemagazine's":1,"jenniferfisherjewelry":1,"albertaferretti":1,"nnadibynature":1,"lynn_ban":1},"top_posts":{"BiSKCKiDFt7":"6.24","BiUCt-Cj1pL":"5.22","BiUJjkOjLkL":"4.59"}},"access":true}
                </pre>

                <h5 class="mt-4"><?= $language->api_documentation->display->failed_response ?></h5>
                <span class="text-muted"><?= $language->api_documentation->display->failed_response_help ?></span>

                <ul>
                    <li><strong>Code:</strong> <code>403</code></li>
                    <li><strong>Content:</strong></li>
                </ul>

                <pre id="api_sample_response">
{
    "access": false,
    "message": "Your api key is not authorized to make this request."
}                </pre>

                <h5 class="mt-4"><?= $language->api_documentation->display->php_example ?></h5>
                <span class="text-muted"><?= $language->api_documentation->display->php_example_help ?></span>

                <pre class="mt-4">
$instagram_username = "badgirlriri";
$api_key = "<?= $account->api_key ?>";
$response = file_get_contents("<?= $settings->url ?>api?api_key=$api_key&username=$instagram_username&source=instagram");
$data = json_decode($response);

print_r($data);
                </pre>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <?php require VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php' ?>
    </div>
</div>

<script>
    $(document).ready(() => {

        let string = JSON.stringify(JSON.parse($('#api_sample_response').html().trim()), null, 4);

        $('#api_sample_response').html(string);

    })
</script>
