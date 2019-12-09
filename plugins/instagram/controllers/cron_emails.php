<?php
defined('ALTUMCODE') || die();

$emails_result = $database->query("
        SELECT
          'UNLOCKED_REPORTS' AS `type`,
          `users`.`user_id`,
          `users`.`email`,
          `instagram_users`.`id`,
          `instagram_users`.`username`,
          `instagram_users`.`followers`,
          `instagram_users`.`following`,
          `instagram_users`.`uploads`,
          `instagram_users`.`average_engagement_rate`,
          `instagram_users`.`added_date`,
          `instagram_users`.`last_check_date`,
          `email_reports`.`date`
        FROM
             `users`
        INNER JOIN
            `unlocked_reports`
        ON
            `unlocked_reports`.`user_id` = `users`.`user_id`
        LEFT JOIN
            `instagram_users`
        ON
            `unlocked_reports`.`source_user_id` = `instagram_users`.`id`
        LEFT JOIN
            (SELECT `user_id`, `source_user_id`, `source`, MAX(`date`) AS `date` FROM `email_reports` WHERE `source` = 'INSTAGRAM' GROUP BY `user_id`, `source_user_id`) AS `email_reports`
        ON
            `users`.`user_id` = `email_reports`.`user_id`
            AND `instagram_users`.`id` = `email_reports`.`source_user_id`
        WHERE
            (`email_reports`.`date` IS NULL OR TIMESTAMPDIFF({$timestampdiff}, `email_reports`.`date`, '{$date}') > {$timestampdiff_compare})
            AND `users`.`email_reports` = '1'
            AND TIMESTAMPDIFF({$timestampdiff}, `instagram_users`.`added_date`, '{$date}') > {$timestampdiff_compare}
            AND `unlocked_reports`.`source` = 'INSTAGRAM'
        LIMIT 1
    ");


/* If we have no results and the favorites system is enabled, try to find emails to send there too */
if($emails_result->num_rows == 0 && $settings->email_reports_favorites) {

    $emails_result = $database->query("
            SELECT
              'FAVORITES' AS `type`,
              `users`.`user_id`,
              `users`.`email`,
              `instagram_users`.`id`,
              `instagram_users`.`username`,
              `instagram_users`.`followers`,
              `instagram_users`.`following`,
              `instagram_users`.`uploads`,
              `instagram_users`.`average_engagement_rate`,
              `instagram_users`.`added_date`,
              `instagram_users`.`last_check_date`,
              `email_reports`.`date`
            FROM
                 `users`
            INNER JOIN
                 `favorites`
            ON
                `favorites`.`user_id` = `users`.`user_id`
            LEFT JOIN
                `instagram_users`
            ON
                `favorites`.`source_user_id` = `instagram_users`.`id`
            LEFT JOIN
                (SELECT `user_id`, `source_user_id`, `source`, MAX(`date`) AS `date` FROM `email_reports` WHERE `source` = 'INSTAGRAM' GROUP BY `user_id`, `source_user_id`) AS `email_reports`
            ON
                `users`.`user_id` = `email_reports`.`user_id`
                AND `instagram_users`.`id` = `email_reports`.`source_user_id`
            WHERE
                (`email_reports`.`date` IS NULL OR TIMESTAMPDIFF({$timestampdiff}, `email_reports`.`date`, '{$date}') > {$timestampdiff_compare})
                AND `users`.`email_reports` = '1'
                AND TIMESTAMPDIFF({$timestampdiff}, `instagram_users`.`added_date`, '{$date}') > {$timestampdiff_compare}
                AND `favorites`.`source` = 'INSTAGRAM'
            LIMIT 1
        ");

}

while($result = $emails_result->fetch_object()) {

    /* Get the previous log so that we can compare the current with the previous */
    $previous = $database->query("SELECT `followers`, `following`, `uploads`, `average_engagement_rate`, `date` FROM `instagram_logs` WHERE `instagram_user_id` = {$result->id} AND TIMESTAMPDIFF({$timestampdiff}, `date`, '{$result->last_check_date}') > {$timestampdiff_compare} ORDER BY `id` DESC LIMIT 1")->fetch_object();

    $new_followers = $result->followers - $previous->followers;
    $new_following = $result->following - $previous->following;
    $new_uploads = $result->uploads - $previous->uploads;
    $new_average_engagement_rate = $result->average_engagement_rate - $previous->average_engagement_rate;

    $email_title = sprintf(
        $new_followers > 0 ? $language->instagram->email_report->title_plus : $language->instagram->email_report->title_minus,
        $result->username,
        nr($new_followers)
    );

    $email_content = '';

    $email_content .= '<h2>' . sprintf($language->instagram->email_report->content->header, $result->username) . '</h2>';
    $email_content .= '<p>' . sprintf($language->instagram->email_report->content->text_one, (new DateTime($previous->date))->format($language->global->date->datetime_format), (new DateTime($result->last_check_date))->format($language->global->date->datetime_format)) . '</p>';
    $email_content .= '<br /><br />';

    $email_content .= '<table>';
    $email_content .= '<thead>';
    $email_content .= '<tr>';
    $email_content .= '<td></td>';
    $email_content .= '<td>' . $language->instagram->email_report->content->previous . '</td>';
    $email_content .= '<td>' . $language->instagram->email_report->content->latest . '</td>';
    $email_content .= '</tr>';
    $email_content .= '</thead>';

    $email_content .= '<tbody>';
    $email_content .= '<tr>';
    $email_content .= '<td>' . $language->instagram->email_report->content->followers . '</td>';
    $email_content .= '<td>' . nr($previous->followers) . '</td>';
    $email_content .= '<td>' . nr($result->followers) . ' ( '. colorful_number($new_followers) . ' )</td>';
    $email_content .= '</tr>';

    $email_content .= '<tr>';
    $email_content .= '<td>' . $language->instagram->email_report->content->following . '</td>';
    $email_content .= '<td>' . nr($previous->following) . '</td>';
    $email_content .= '<td>' . nr($result->following) . ' ( '. colorful_number($new_following) . ' )</td>';
    $email_content .= '</tr>';

    $email_content .= '<tr>';
    $email_content .= '<td>' . $language->instagram->email_report->content->uploads . '</td>';
    $email_content .= '<td>' . nr($previous->uploads) . '</td>';
    $email_content .= '<td>' . nr($result->uploads) . ' ( '. colorful_number($new_uploads) . ' )</td>';
    $email_content .= '</tr>';

    $email_content .= '<tr>';
    $email_content .= '<td>' . $language->instagram->email_report->content->average_engagement_rate . '</td>';
    $email_content .= '<td>' . $previous->average_engagement_rate . '%</td>';
    $email_content .= '<td>' . $result->average_engagement_rate . '% ( '. colorful_number($new_average_engagement_rate, '', 2) . '% )</td>';
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
                      <td><a href="' . $settings->url . 'report/' . $result->username . '/instagram" class="btn btn-primary">' . $language->instagram->email_report->content->button . '</a></td>
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

    if(DEBUG) { echo 'Instagram Email Report: ' . $status . ' - to ' . $result->email . ' for ' . $result->username; echo '<br />'; }

    Database::insert('email_reports', [
        'source_user_id' => $result->id,
        'user_id' => $result->user_id,
        'date' => $date,
        'status' => $status,
        'source' => 'INSTAGRAM'
    ]);

}
