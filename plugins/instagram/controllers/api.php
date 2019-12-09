<?php
defined('ALTUMCODE') || die();

/* Get details of the medias of the account if existing */
$data->media = [];

if(!$data->is_private) {
    $instagram_media_result = $database->query("SELECT * FROM `instagram_media` WHERE `instagram_user_id` = '{$source_user_id}' ORDER BY `created_date` DESC LIMIT {$settings->instagram_calculator_media_count}");

    if($instagram_media_result->num_rows) {
        while ($media = $instagram_media_result->fetch_object()) {

            unset($media->id);

            $media->mentions = json_decode($media->mentions);
            $media->hashtags = json_decode($media->hashtags);

            $data->media[] = $media;
        }
    }
}
