<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

if(!empty($_POST)) {
    $content = preg_split("/(\,|\n)/", $_POST['content']);
    $last_checked_date = (new \DateTime())->modify('-1 years')->format($language->global->date->datetime_format . ' H:i:s');
    $total_inserts = 0;
    $total_duplicates = 0;

    /* Make sure we don't get empty strings */
    foreach($content as $line) {

        if(!empty(trim($line))) {

            /* Insert to database if not existing already */
            if(!$exists = Database::exists('username', 'instagram_users', ['username' => trim($line)])) {

                Database::insert('instagram_users', [
                    'username' => trim($line),
                    'full_name' => $language->report->state->not_checked_full_name,
                    'description' => $language->report->state->not_checked_description,
                    'added_date' => $date,
                    'last_check_date' => $last_checked_date
                ]);

                $total_inserts++;

            } else {

                $total_duplicates++;

            }
        }

    }

    if($total_inserts > 0) {
        $_SESSION['success'][] = sprintf($language->admin_instagram_users_import->success_message->imported, $total_inserts);
    }

    if($total_duplicates > 0) {
        $_SESSION['info'][] = sprintf($language->admin_instagram_users_import->info_message->duplicates, $total_duplicates);
    }
}