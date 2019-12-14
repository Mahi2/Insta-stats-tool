<?php
defined('MAHIDCODE') || die();
User::logged_in_redirect();

$method	= (isset($parameters[0])) ? $parameters[0] : false;
$redirect = 'dashboard';

if(isset($_GET['redirect']) && $redirect = $_GET['redirect']) {
    //
}

/* Default values */
$login_username = '';

/* instagram login / register handler */
if($settings->instagram_login) {

    $instagram = new MetzWeb\Instagram\Instagram([
        'apiKey'      => $settings->instagram_client_id,
        'apiSecret'   => $settings->instagram_client_secret,
        'apiCallback' => $settings->url . 'login/instagram'
    ]);

    $instagram_login_url = $instagram->getLoginUrl();

    if($method == 'instagram') {
        $instagram_data = $instagram->getOAuthToken($_GET['code']);

        if(isset($instagram_data->error_message)) {
            $_SESSION['error'][] = 'Instagram Auth Error: ' . $instagram_data->error_message;
        }

        if(empty($_SESSION['error'])) {

            /* If the user is already in the system, log him in */
            if($account = Database::get(['user_id'], 'users', ['instagram_id' => $instagram_data->user->id])) {
                $_SESSION['user_id'] = $account->user_id;
                redirect($redirect);
            }

            /* Create a new account */
            else {
                /* Generate a random username */
                $username = generate_slug($instagram_data->user->username);

                /* Error checks */

                /* If the user already exists, generate a new username with some random characters */
                while(Database::exists('username', 'users', ['username' => $username])) {
                    $username = generate_slug($instagram_data->user->username) . rand(100, 999);
                }

                if(empty($_SESSION['error'])) {
                    $generated_password = generate_string(8);
                    $password = password_hash($generated_password, PASSWORD_DEFAULT);
                    $description = $instagram_data->user->bio;
                    $name = $instagram_data->user->full_name;
                    $email = '';
                    $active = 1;
                    $api_key = md5($email . $username);

                    /* Insert the user into the database */
                    $stmt = $database->prepare("INSERT INTO `users` (`username`, `password`, `email`, `name`, `active`, `date`, `instagram_id`, `api_key`, `points`, `email_reports`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param('ssssssssss', $username, $password, $email, $name, $active, $date, $instagram_data->user->id, $api_key, $settings->store_user_default_points, $settings->email_reports_default);
                    $stmt->execute();
                    $stmt->close();
/* Prepare the email */
$email_template = generate_email_template(
    [
        '{{NAME}}'              => $name,
        '{{WEBSITE_TITLE}}'     => $settings->title
    ],
    $settings->credentials_email_template_subject,
    [
        '{{ACCOUNT_USERNAME}}'  => $username,
        '{{ACCOUNT_PASSWORD}}'  => $generated_password,
        '{{WEBSITE_LINK}}'      => $settings->url,
        '{{NAME}}'              => $name,
        '{{WEBSITE_TITLE}}'     => $settings->title
    ],
    $settings->credentials_email_template_body
);

/* Send the user an email with his new details */
sendmail($email, $email_template->subject, $email_template->body);

/* Log the user in and redirect him */
$_SESSION['user_id'] = Database::simple_get('user_id', 'users', ['instagram_id' => $instagram_data->user->id]);
$_SESSION['success'][] = $language->register->success_message->login;
redirect();
}
}
}

}
}