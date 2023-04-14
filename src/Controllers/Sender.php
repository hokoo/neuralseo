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

	public static function processRequest( $data ) {
		$requestData = RequestData::fromArray( $data );

		try {
			$result = get_client()->requestData( $data );
		} catch ( RequestFailed $exception ) {
			$result = false;
		}

		if ( empty( $result ) ) {
			$actionID = Scheduler::setupAction( $data );
			StatusManager::actionScheduled( $actionID, $requestData->postID );
			return;
		}

		StatusManager::actionPerformed( $requestData->postID );
	}
}
