<?php

class Response {

	public static function json($message, $status = 'success', $details = []) {
        if(!is_array($message) && $message) $message = [$message];

		echo json_encode(
			[
				'message' 	=> $message,
				'status' 	=> $status,
				'details'	=> $details,
			]
		);
	}

    public static function simple_json($response) {

        echo json_encode($response);

    }
}