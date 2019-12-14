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

/* Facebook Login / Register */
if($settings->facebook_login) {

    $facebook = new Facebook\Facebook([
        'app_id' => $settings->facebook_app_id,
        'app_secret' => $settings->facebook_app_secret,
        'default_graph_version' => 'v2.2',
    ]);

    $facebook_helper = $facebook->getRedirectLoginHelper();
    $facebook_login_url = $facebook->getRedirectLoginHelper()->getLoginUrl($settings->url . 'login/facebook', ['email', 'public_profile']);

    if($method == 'facebook') {
        try {
            $facebook_access_token = $facebook_helper->getAccessToken($settings->url . 'login/facebook');
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            $_SESSION['error'][] = 'Graph returned an error: ' . $e->getMessage();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            $_SESSION['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage();
        }
    }

    if(isset($facebook_access_token)) {
        /* The OAuth 2.0 client handler helps us manage access tokens */
        $facebook_oAuth2_client = $facebook->getOAuth2Client();

        /* Get the access token metadata from /debug_token */
        $facebook_token_metadata = $facebook_oAuth2_client->debugToken($facebook_access_token);

        /* Validation */
        $facebook_token_metadata->validateAppId($settings->facebook_app_id);
        $facebook_token_metadata->validateExpiration();

        if(!$facebook_access_token->isLongLived()) {
            /* Exchanges a short-lived access token for a long-lived one */
            try {
                $facebook_access_token = $facebook_oAuth2_client->getLongLivedAccessToken($facebook_access_token);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $_SESSION['error'][] = 'Error getting long-lived access token: ' . $facebook_helper->getMessage();
            }
        }

        try {
            $response = $facebook->get('/me?fields=id,name,email', $facebook_access_token);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $_SESSION['error'][] = 'Graph returned an error: ' . $e->getMessage();
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            $_SESSION['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage();
        }

        if(isset($response)) {
            $facebook_user = $response->getGraphUser();
            $facebook_user_id = $facebook_user->getId();
            $email = $facebook_user->getField('email');
            /* If the user is already in the system, log him in */
            if($account = Database::get(['user_id'], 'users', ['facebook_id' => $facebook_user_id])) {
                $_SESSION['user_id'] = $account->user_id;
                redirect($redirect);
            }

            /* Check if the user already exists with the specific email */
            else if($account = Database::get(['user_id'], 'users', ['email' => $email])) {

                Database::update('users', ['facebook_id' => $facebook_user_id], ['user_id' => $account->user_id]);

                /* Log the user in and redirect him */
                $_SESSION['user_id'] = $account->user_id;
                $_SESSION['success'][] = $language->register->success_message->login;
                redirect($redirect);

            }

            /* Create a new account */
            else {
                /* Generate a random username */
                $username = generate_slug($facebook_user->getName());

                /* Check if email is actually not null */
                if(is_null($email)) {
                    $_SESSION['error'][] = $language->register->error_message->email_is_null;
                }

                /* Error checks */
                if(Database::exists('email', 'users', ['email' => $email])) {
                    $_SESSION['error'][] = $language->register->error_message->email_exists;
                }

                /* If the user already exists, generate a new username with some random characters */
                while(Database::exists('username', 'users', ['username' => $username])) {
                    $username = generate_slug($facebook_user->getName()) . rand(100,999);
                }


                if(empty($_SESSION['error'])) {
                    $generated_password = generate_string(8);
                    $password 	= password_hash($generated_password, PASSWORD_DEFAULT);
                    $name = $facebook_user->getName();
                    $active = 1;
                    $api_key = md5($email.$username);

                    /* Insert the user into the database */
                    $stmt = $database->prepare("INSERT INTO `users` (`username`, `password`, `email`, `name`, `active`, `date`, `facebook_id`, `api_key`, `points`, `email_reports`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param('ssssssssss', $username, $password, $email, $name, $active, $date, $facebook_user_id, $api_key, $settings->store_user_default_points, $settings->email_reports_default);
                    $stmt->execute();
                    $stmt->close();

                    /* Prepare the email */
                    $email_template = generate_email_template(
                        [
                            '{{NAME}}' => $name,
                            '{{WEBSITE_TITLE}}' => $settings->title
                        ],
                        $settings->credentials_email_template_subject,
                        [
                            '{{ACCOUNT_USERNAME}}' => $username,
                            '{{ACCOUNT_PASSWORD}}' => $generated_password,
                            '{{WEBSITE_LINK}}' => $settings->url,
                            '{{NAME}}' => $name,
                            '{{WEBSITE_TITLE}}' => $settings->title
                        ],
                        $settings->credentials_email_template_body
                    );

                    /* Send the user an email with his new details */
                    sendmail($email, $email_template->subject, $email_template->body);

                    /* Log the user in and redirect him */
                    $_SESSION['user_id'] = Database::simple_get('user_id', 'users', ['facebook_id' => $facebook_user_id]);
                    $_SESSION['success'][] = $language->register->success_message->login;
                    redirect($redirect);
                }
            }
        }
    }
}