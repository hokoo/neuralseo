<?php

namespace NeuralSEO\Controllers;

use NeuralSEO\Exceptions\ExcessRequest;
use NeuralSEO\Exceptions\RequestFailed;
use NeuralSEO\Models\RequestData;
use function NeuralSEO\get_client;
use const NeuralSEO\REQUEST_HOOK;

class Sender {

	public static function init() {

		/**
		 * The Chain B starts here.
		 * Process AS action sending request to Neural API.
		 */
		add_action( REQUEST_HOOK, [ self::class, 'processRequest' ], 10, 2 );
	}

	/**
	 * @throws RequestFailed
	 */
	public static function processRequest( $data ) {
		$requestData = RequestData::fromArray( $data );


		$result = get_client()->requestData( $data );
		if ( ! $result ) {
			// @todo reschedule action
			return;
		}


		StatusManager::actionPerformed( $requestData->postID );
	}
}
