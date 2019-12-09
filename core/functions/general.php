<?php
defined('ALTUMCODE') || die();
use PHPMailer\PHPMailer\PHPMailer;

function add_event($hook, $function) {
    global $actions;

    // create an array of function handlers if it doesn't already exist
    if(!isset($actions[$hook]))
        $actions[ $hook ] = array();

    // append the current function to the list of function handlers
    $actions[$hook][] = $function;
}

function perform_event($hook) {
    global $actions;

    if(isset($actions[$hook])) {
        // call each function handler associated with this hook
        foreach($actions[$hook] as $function)
            call_user_func($function);
    }
}

function get_settings() {
	global $database;
	global $config;

    $settings = $database->query("SELECT * FROM `settings` WHERE `id` = 1")->fetch_object();
    $settings->url = $config['url'];

    /* Parse the email templates quickly */
    $activation_email_template = json_decode($settings->activation_email_template);
    $settings->activation_email_template_subject = $activation_email_template->subject;
    $settings->activation_email_template_body 	= $activation_email_template->body;

    $lost_password_email_template = json_decode($settings->lost_password_email_template);
    $settings->lost_password_email_template_subject = $lost_password_email_template->subject;
    $settings->lost_password_email_template_body 	= $lost_password_email_template->body;

    $credentials_email_template = json_decode($settings->credentials_email_template);
    $settings->credentials_email_template_subject = $credentials_email_template->subject;
    $settings->credentials_email_template_body 	= $credentials_email_template->body;

    return $settings;
}

function url($append = '') {
    global $settings;

    return $settings->url . $append;
}

function generate_email_template($email_template_subject_array = [], $email_template_subject, $email_template_body_array = [], $email_template_body) {

    $email_template_subject = str_replace(
        array_keys($email_template_subject_array),
        array_values($email_template_subject_array),
        $email_template_subject
    );

    $email_template_body = str_replace(
        array_keys($email_template_body_array),
        array_values($email_template_body_array),
        $email_template_body
    );

    return (object) [
    	'subject' => $email_template_subject,
		'body' => $email_template_body
	];
}

function send_server_mail($to, $from, $title, $content) {

	$headers = "From: " . strip_tags($from) . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    /* Check if receipient is array or not */
    $to_processed = $to;

    if(is_array($to)) {
        $to_processed = '';

        foreach($to as $address) {
            $to_processed .= ',' . $address;
        }

    }

	return mail($to_processed, $title, $content, $headers);
}

function sendmail($to, $title, $content, $test = false) {
	global $settings;

	if(!empty($settings->smtp_host)) {

		try {
            $mail = new PHPMailer;
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->SMTPDebug = 0;

            if($settings->smtp_encryption != '0') {
                $mail->SMTPSecure = $settings->smtp_encryption;
            }

            $mail->SMTPAuth = $settings->smtp_auth;
            $mail->isHTML(true);

            $mail->Host = $settings->smtp_host;
            $mail->Port = $settings->smtp_port;
            $mail->Username = $settings->smtp_user;
            $mail->Password = $settings->smtp_pass;

            $mail->setFrom($settings->smtp_from, $settings->title);
            $mail->addReplyTo($settings->smtp_from, $settings->title);

            /* Check if receipient is array or not */
            if(is_array($to)) {
            	foreach($to as $address) {
                    $mail->addAddress($address);
                }
			} else {
                $mail->addAddress($to);
            }

            $mail->Subject = $title;

            /* Template and content preparing */
            $email_template_raw = file_get_contents(VIEWS_ROUTE . 'extra/email.html');

            $replacers = [
                '{{CONTENT}}'   => $content,
                '{{URL}}'       => $settings->url,
                '{{TITLE}}'     => $settings->title
            ];

            $email_template = str_replace(
                array_keys($replacers),
                array_values($replacers),
                $email_template_raw
            );

            $mail->Body = $email_template;

            $send = $mail->send();

            return $test ? $mail : $send;

        } catch (Exception $e) {

            return $test ? $mail : false;

        }

	} else {
	    return send_server_mail($to, $settings->smtp_from, $title, $content);
	}

}


function parse_url_parameters() {
	return (isset($_GET['page'])) ? explode('/', filter_var(rtrim($_GET['page'], '/'), FILTER_SANITIZE_URL)) : [];
}

function redirect($new_page = '') {
	global $settings;

	header('Location: ' . $settings->url . $new_page);
	die();
}

function trim_value(&$value) {
	$value = trim($value);
}

function colorful_number($number, $append = '', $decimals = 0) {
    if($number > 0) {
        return '<span style="color: #28a745 !important;">+' . nr($number, $decimals) . $append .'</span>';
    }
	elseif($number < 0) {
        return '<span style="color: #dc3545 !important;">' . nr($number, $decimals) . $append . '</span>';

    } else {
        return '-';
    }
}

function colorful_number_icon($number, $tooltip = false) {
    $tooltip_html = $tooltip ? 'data-toggle="tooltip" data-title="' . $tooltip . '"' : null;

    if($number > 0) {
        return '<span ' . $tooltip_html . ' style="color: #28a745 !important;"><i class="fa fa-arrow-up"></i></span>';
    }
    elseif($number < 0) {
        return '<span ' . $tooltip_html . ' style="color: #dc3545 !important;"><i class="fa fa-arrow-down"></i></span>';

    } else {
        return null;
    }
}

function sign_number($number, $decimals = 0) {
    return $number > 0 ? '+' . nr($number, $decimals) : nr($number, $decimals);
}

function get_percentage_difference($main, $second) {

    if($main < 1) {
        $main = 0;
    }

    $difference = $second - $main;

    if($difference == 0) {
        return 0;
    }

    if($main == 0) {
        return 100;
    }

    $result = ($difference / $main) * 100;

    return $result;

}

function validate_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);

    return $d && $d->format($format) === $date;
}

function filter_banned_words($value) {
	global $settings;

	$words = explode(',', $settings->banned_words);
	array_walk($words, 'trim_value');

	foreach($words as $word) {
		$value = str_replace($word, str_repeat('*', strlen($word)), $value);
	}

	return $value;
}

function get_gravatar($email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = []) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";

    if($img) {
        $url = '<img src="' . $url . '"';

        foreach ($atts as $key => $val) {
            $url .= ' ' . $key . '="' . $val . '"';
        }

        $url .= ' />';
    }

    return $url;
}

function generate_slug($string, $delimiter = '_') {

    /* Convert accents characters */
    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

    /* Replace all non words characters with the specified $delimiter */
    $string = preg_replace('/\W/', $delimiter, $string);

    /* Check for double $delimiters and remove them so it only will be 1 delimiter */
    $string = preg_replace('/_+/', '_', $string);

    /* Remove the $delimiter character from the start and the end of the string */
    $string = trim($string, $delimiter);

    return $string;
}

function generate_string($length) {
	$characters = str_split('abcdefghijklmnopqrstuvwxyz0123456789');
	$content = '';

	for($i = 1; $i <= $length; $i++) {
		$content .= $characters[array_rand($characters, 1)];
	}

	return $content;
}

function nr($number, $decimals = 0, $extra = false) {
    global $language;

    if($extra) {

        if(!is_array($extra) || (is_array($extra) && in_array('B', $extra))) {

            if($number > 999999999) {
                return floor($number / 1000000000) . 'B';
            }

        }

        if(!is_array($extra) || (is_array($extra) && in_array('M', $extra))) {

            if($number > 999999) {
                return floor($number / 1000000) . 'M';
            }

        }

        if(!is_array($extra) || (is_array($extra) && in_array('K', $extra))) {

            if($number > 999) {
                return floor($number / 1000) . 'K';
            }

        }

    }

    if($number == 0) {
        return 0;
    }

    return number_format($number, $decimals, $language->global->number->decimal_point, $language->global->number->thousands_separator);
}

function resize($file_name, $path, $width, $height, $center = false) {
	/* Get original image x y*/
	list($w, $h) = getimagesize($file_name);

	/* calculate new image size with ratio */
	$ratio = max($width/$w, $height/$h);
	$h = ceil($height / $ratio);
	$x = ($w - $width / $ratio) / 2;
	$w = ceil($width / $ratio);
	$y = 0;
	if($center) $y = 250 + $h/1.5;

	/* read binary data from image file */
	$imgString = file_get_contents($file_name);

	/* create image from string */
	$image = imagecreatefromstring($imgString);
	$tmp = imagecreatetruecolor($width, $height);
	imagecopyresampled($tmp, $image,
	0, 0,
	$x, $y,
	$width, $height,
	$w, $h);

	/* Save image */
	imagejpeg($tmp, $path, 100);

	return $path;
	/* cleanup memory */
	imagedestroy($image);
	imagedestroy($tmp);
}

function formatBytes($bytes, $precision = 2) {
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;

    if(($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';

    } elseif(($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';

    } elseif(($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';

    } elseif(($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';

    } elseif($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

function string_resize($string, $maxchar, $append = '..') {
	$length = strlen($string);

	if($length > $maxchar) {
		$cutsize = -($length-$maxchar);
		$string  = substr($string, 0, $cutsize);
		$string  = $string . $append;
	}

	return $string;
}

function display_notifications() {
	global $language;

	$types = ['error', 'success', 'info'];
	foreach($types as $type) {
		if(isset($_SESSION[$type]) && !empty($_SESSION[$type])) {
			if(!is_array($_SESSION[$type])) $_SESSION[$type] = [$_SESSION[$type]];

			foreach($_SESSION[$type] as $message) {
				$csstype = ($type == 'error') ? 'danger' : $type;

				echo '
					<div class="alert alert-' . $csstype . ' animated fadeInDown">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>' . $language->global->message_type->$type . '</strong> ' . $message . '
					</div>
				';
			}
			unset($_SESSION[$type]);
		}
	}

}

function generate_chart_data(Array $main_array) {
    global $language;


    foreach($main_array as $key => $value) {

        /* Reverse the order if RTL */
        if($language->direction == 'rtl') {
            $value = array_reverse($value);
        }

        $main_array[$key] = '["' . implode('", "', $value) . '"]';

    }

    return $main_array;
}

function csv_exporter($array, $exclude = []) {

    $result = '';

    /* Get the total amount of columns */
    $columns_count = count((array) reset($array));

    /* Export the header */
    $i = 0;
    foreach(array_keys((array) reset($array)) as $value) {
        /* Check if not excluded */
        if(!in_array($value, $exclude)) {
            $result .= $i++ !== $columns_count - 1 ? $value . ',' : $value;
        }
    }

    foreach($array as $row) {
        $result .= "\n";

        $i = 0;
        foreach($row as $key => $value) {
            /* Check if not excluded */
            if(!in_array($key, $exclude)) {
                $result .= $i++ !== $columns_count - 1 ? '"' . $value . '"' . ',' : '"' . $value . '"';
            }
        }
    }

    return $result;
}

function csv_link_exporter($csv) {
    return 'data:application/csv;charset=utf-8,' . urlencode($csv);
}

function get_random_user_agent() {
    /* Function created in order to get a random user agent to spoof */

    $user_agents = [
        'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 4.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.6 Safari/537.11',
        'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.26 Safari/537.11',
        'Mozilla/5.0 (Windows NT 6.0) yi; AppleWebKit/345667.12221 (KHTML, like Gecko) Chrome/23.0.1271.26 Safari/453667.1221',
        'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.17 Safari/537.11',
        'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.94 Safari/537.4',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_0) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/537.4',
        'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/22.0.1207.1 Safari/537.1',
        'Mozilla/5.0 (X11; CrOS i686 2268.111.0) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.6 (KHTML, like Gecko) Chrome/20.0.1092.0 Safari/536.6',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
        'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0',
        'Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/31.0',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20130401 Firefox/31.0',
        'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20120101 Firefox/29.0',
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/29.0',
        'Mozilla/5.0 (X11; OpenBSD amd64; rv:28.0) Gecko/20100101 Firefox/28.0',
        'Mozilla/5.0 (X11; Linux x86_64; rv:28.0) Gecko/20100101  Firefox/28.0',
        'Mozilla/5.0 (Windows NT 6.1; rv:27.3) Gecko/20130101 Firefox/27.3',
        'Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:27.0) Gecko/20121011 Firefox/27.0',
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:25.0) Gecko/20100101 Firefox/25.0',
        'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
        'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.3 Safari/534.53.10',
        'Mozilla/5.0 (iPad; CPU OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko ) Version/5.1 Mobile/9B176 Safari/7534.48.3',
        'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; de-at) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',
        'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',
        'Mozilla/5.0 (Windows; U; Windows NT 6.1; tr-TR) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Windows; U; Windows NT 6.1; ko-KR) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr-FR) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Windows; U; Windows NT 6.1; cs-CZ) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Windows; U; Windows NT 6.0; ja-JP) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_5_8; zh-cn) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_5_8; ja-jp) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; ja-jp) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27',
        'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; zh-cn) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27'
    ];

    return $user_agents[array_rand($user_agents)];

}
