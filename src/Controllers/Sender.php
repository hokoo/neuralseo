<?php

namespace NeuralSEO\Controllers;

use NeuralSEO\Exceptions\RequestFailed;
use NeuralSEO\Models\RequestData;
use function NeuralSEO\get_client;
use const NeuralSEO\REQUEST_HOOK;

class Sender {
	public static function init() {
		add_action( REQUEST_HOOK, [ self::class, 'processRequest' ], 10, 2 );
	}

	/**
	 * @throws RequestFailed
	 */
	public static function processRequest( $data ) {
		$result = get_client()->requestData( $data );
		if ( ! $result ) {
			// @todo reschedule action
			return;
		}

		$requestData = RequestData::fromArray( $data );
		StatusManager::actionPerformed( $requestData->postID );
	}
}
