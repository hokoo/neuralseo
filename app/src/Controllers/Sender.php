<?php

namespace NeuralSEO\Controllers;

use function NeuralSEO\get_client;
use const NeuralSEO\REQUEST_HOOK;

class Sender {
	public static function init() {
		add_action( REQUEST_HOOK, [ self::class, 'process_request' ], 10, 2 );
	}

	public static function process_request( ...$data ) {
		get_client()->requestData( $data );
	}
}
