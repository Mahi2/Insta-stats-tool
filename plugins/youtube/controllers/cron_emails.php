<?php
defined('ALTUMCODE') || die();

$emails_result = $database->query("
        SELECT
          'UNLOCKED_REPORTS' AS `type`,
          `users`.`user_id`,
          `users`.`email`,
          `youtube_users`.`id`,
          `youtube_users`.`title`,
          `youtube_users`.`subscribers`,
          `youtube_users`.`views`,
          `youtube_users`.`videos`,
          `youtube_users`.`added_date`,
          `youtube_users`.`last_check_date`,
          `email_reports`.`date`
        FROM
             `users`
        INNER JOIN
            `unlocked_reports`
        ON
            `unlocked_reports`.`user_id` = `users`.`user_id`
        LEFT JOIN
            `youtube_users`
        ON
            `unlocked_reports`.`source_user_id` = `youtube_users`.`id`
        LEFT JOIN
            (SELECT `user_id`, `source_user_id`, `source`, MAX(`date`) AS `date` FROM `email_reports` WHERE `source` = 'YOUTUBE' GROUP BY `user_id`, `source_user_id`) AS `email_reports`
        ON
            `users`.`user_id` = `email_reports`.`user_id`
            AND `youtube_users`.`id` = `email_reports`.`source_user_id`
        WHERE
            (`email_reports`.`date` IS NULL OR TIMESTAMPDIFF({$timestampdiff}, `email_reports`.`date`, '{$date}') > {$timestampdiff_compare})
            AND `users`.`email_reports` = '1'
            AND TIMESTAMPDIFF({$timestampdiff}, `youtube_users`.`added_date`, '{$date}') > {$timestampdiff_compare}
            AND `unlocked_reports`.`source` = 'YOUTUBE'
        LIMIT 1
    ");


/* If we have no results and the favorites system is enabled, try to find emails to send there too */
if($emails_result->num_rows == 0 && $settings->email_reports_favorites) {

    $emails_result = $database->query("
            SELECT
              'FAVORITES' AS `type`,
              `users`.`user_id`,
              `users`.`email`,
              `youtube_users`.`id`,
              `youtube_users`.`title`,
              `youtube_users`.`subscribers`,
              `youtube_users`.`views`,
              `youtube_users`.`videos`,
              `youtube_users`.`added_date`,
              `youtube_users`.`last_check_date`,
              `email_reports`.`date`
            FROM
                 `users`
            INNER JOIN
                 `favorites`
            ON
                `favorites`.`user_id` = `users`.`user_id`
            LEFT JOIN
                `youtube_users`
            ON
                `favorites`.`source_user_id` = `youtube_users`.`id`
            LEFT JOIN
                (SELECT `user_id`, `source_user_id`, `source`, MAX(`date`) AS `date` FROM `email_reports` WHERE `source` = 'YOUTUBE' GROUP BY `user_id`, `source_user_id`) AS `email_reports`
            ON
                `users`.`user_id` = `email_reports`.`user_id`
                AND `youtube_users`.`id` = `email_reports`.`source_user_id`
            WHERE
                (`email_reports`.`date` IS NULL OR TIMESTAMPDIFF({$timestampdiff}, `email_reports`.`date`, '{$date}') > {$timestampdiff_compare})
                AND `users`.`email_reports` = '1'
                AND TIMESTAMPDIFF({$timestampdiff}, `youtube_users`.`added_date`, '{$date}') > {$timestampdiff_compare}
                AND `favorites`.`source` = 'YOUTUBE'
            LIMIT 1
        ");

}

while($result = $emails_result->fetch_object()) {

    /* Get the previous log so that we can compare the current with the previous */
    $previous = $database->query("SELECT `subscribers`, `views`, `date` FROM `youtube_logs` WHERE `youtube_user_id` = {$result->id} AND TIMESTAMPDIFF({$timestampdiff}, `date`, '{$result->last_check_date}') > {$timestampdiff_compare} ORDER BY `id` DESC LIMIT 1")->fetch_object();

    $new_subscribers = $result->subscribers - $previous->subscribers;
    $new_views = $result->views - $previous->views;
    $new_videos = $result->videos - $previous->videos;

    $email_title = sprintf(
        $new_subscribers > 0 ? $language->youtube->email_report->title_plus : $language->youtube->email_report->title_minus,
        $result->title,
        nr($new_subscribers)
    );

    $email_content = '';

    $email_content .= '<h2>' . sprintf($language->youtube->email_report->content->header, $result->title) . '</h2>';
    $email_content .= '<p>' . sprintf($language->youtube->email_report->content->text_one, (new DateTime($previous->date))->format($language->global->date->datetime_format), (new DateTime($result->last_check_date))->format($language->global->date->datetime_format)) . '</p>';
    $email_content .= '<br /><br />';

    $email_content .= '<table>';
    $email_content .= '<thead>';
    $email_content .= '<tr>';
    $email_content .= '<td></td>';
    $email_content .= '<td>' . $language->youtube->email_report->content->previous . '</td>';
    $email_content .= '<td>' . $language->youtube->email_report->content->latest . '</td>';
    $email_content .= '</tr>';
    $email_content .= '</thead>';

    $email_content .= '<tbody>';
    $email_content .= '<tr>';
    $email_content .= '<td>' . $language->youtube->email_report->content->subscribers . '</td>';
    $email_content .= '<td>' . nr($previous->subscribers) . '</td>';
    $email_content .= '<td>' . nr($result->subscribers) . ' ( '. colorful_number($new_subscribers) . ' )</td>';
    $email_content .= '</tr>';

    $email_content .= '<tr>';
    $email_content .= '<td>' . $language->youtube->email_report->content->views . '</td>';
    $email_content .= '<td>' . nr($previous->views) . '</td>';
    $email_content .= '<td>' . nr($result->views) . ' ( '. colorful_number($new_views) . ' )</td>';
    $email_content .= '</tr>';

    $email_content .= '<tr>';
    $email_content .= '<td>' . $language->youtube->email_report->content->videos . '</td>';
    $email_content .= '<td>' . nr($previous->videos) . '</td>';
    $email_content .= '<td>' . nr($result->videos) . ' ( '. colorful_number($new_videos) . ' )</td>';
    $email_content .= '</tr>';

    $email_content .= '</tbody>';
    $email_content .= '</table>';

    $email_content .= '<br /><br />';

    $email_content .= '
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
          <tbody>
            <tr>
              <td align="left">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                  <tbody>
                    <tr>
                      <td><a href="' . $settings->url . 'report/' . $result->title . '/youtube" class="btn btn-primary">' . $language->youtube->email_report->content->button . '</a></td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
        ';

    $email = sendmail($result->email, $email_title, $email_content);

    $status = $email ? 'SUCCESS' : 'FAILED';

    if(DEBUG) { echo 'YouTube Email Report: ' . $status . ' - to ' . $result->email . ' for ' . $result->title; echo '<br />'; }

    Database::insert('email_reports', [
        'source_user_id' => $result->id,
        'user_id' => $result->user_id,
        'date' => $date,
        'status' => $status,
        'source' => 'YOUTUBE'
    ]);

}
