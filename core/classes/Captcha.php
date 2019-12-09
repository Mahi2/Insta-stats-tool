<?php

class Captcha {

	/* Configuration Variables */
	public $image_width = 120;
	public $image_height = 30;
	public $text_length = 6;
	public $lines = 6;
	public $background_color = [255, 255, 255];
	public $text_color = [0, 0, 0];
	public $lines_color = [63, 63, 63];

	private $recaptcha = false;
	private $recaptcha_public_key = false;
	private $recaptcha_private_key = false;
	private $captcha_location = 'get-captcha';

	public function __construct($recaptcha = false, $public_key = false, $private_key = false) {

		/* Determine if its needed to show the recaptcha or not */
		if($recaptcha && $public_key && $private_key) {

			$this->recaptcha = true;
			$this->recaptcha_public_key = $public_key;
			$this->recaptcha_private_key = $private_key;

		}

	}


	/* Custom valid function for both the normal captcha and the recaptcha */
    public function is_valid() {

		if($this->recaptcha) {

			$recaptcha = new \ReCaptcha\ReCaptcha($this->recaptcha_private_key);
		    $response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

			return ($response->isSuccess());

		} else {

			return ($_POST['captcha'] == $_SESSION['captcha']);

		}
	}

	/* Display function based on the captcha settings ( normal captcha or recaptcha ) */
    public function display() {
        global $language;

		if($this->recaptcha) {
            echo '<div class="g-recaptcha" data-sitekey="' . $this->recaptcha_public_key . '"></div>';
        } else {
            echo '<img src="' . $this->captcha_location . '" id="captcha" alt="' . $language->global->accessibility->captcha_image_alt . '" /><input type="text" name="captcha" class="form-control form-control-border" aria-label="' . $language->global->accessibility->captcha_input . '" required="required" />';
        }

	}

	/* Generating the captcha image */
	public function process() {

		/* Initialize the image */
		header('Content-type: image/png');

		/* Generate the text */
		$text = null;

		for($i = 1; $i <= $this->text_length; $i++) $text .= mt_rand(1, 9) . ' ';

		/* Store the generated text in Sessions */
		$_SESSION['captcha'] = str_replace(' ', '', $text);

		/* Create the image */
 		$image = imagecreate($this->image_width, $this->image_height);

 		/* Define the background color */
 		imagecolorallocate($image, $this->background_color[0], $this->background_color[1], $this->background_color[2]);

 		/* Start writing the text */
 		imagestring($image, 5, 7, 7, $text, imagecolorallocate($image, $this->text_color[0], $this->text_color[1], $this->text_color[2]));

 		/* Generate lines */
 		for($i = 1; $i <= $this->lines; $i++) imageline($image, mt_rand(1, $this->image_width), mt_rand(1, $this->image_height), mt_rand(1, $this->image_width), mt_rand(1, $this->image_height), imagecolorallocate($image, $this->lines_color[0], $this->lines_color[1], $this->lines_color[2]));

 		/* Output the image */
		imagepng($image, null, 9);

	}


}