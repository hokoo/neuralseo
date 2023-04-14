<?php

namespace NeuralSEO\Controllers;

use iTRON\wpConnections\Exceptions\ConnectionWrongData;
use iTRON\wpConnections\Exceptions\MissingParameters;
use iTRON\wpConnections\Exceptions\RelationNotFound;
use iTRON\wpPostAble\Exceptions\wppaSavePostException;
use NeuralSEO\Exceptions\ExcessRequest;
use NeuralSEO\Exceptions\RequestFailed;
use NeuralSEO\Factory;
use NeuralSEO\Models\RequestData;

class RequestManager {

	/**
	 * Listens for the requests from Frontend (actually from anywhere).
	 *
	 * @throws ExcessRequest
	 */
	public static function processRequestTriggering( $postID ) {
		// @todo Ensure correct result in threads.

		// First, check for current status.
		if ( StatusManager::isActive( $postID ) ) {
			// There is active request for this post. Skip.
			// @todo Make checking if action has failed or removed.
			throw new ExcessRequest( $postID );
		}

		$data = new RequestData( $postID );
		$actionID = Scheduler::setupAction( $data->toArray() );
		StatusManager::actionScheduled( $actionID, $postID );
	}

	/**
	 * Process AS action sending request to Neural API.
	 */
	public static function processRequestAction( $data ) {
		$requestData = RequestData::fromArray( $data );

		try {
			$result = Factory::getNeuralClient()->requestData( $data );
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

	/**
	 * Receives data from Neural API.
	 *
	 * @throws RelationNotFound
	 * @throws ConnectionWrongData
	 * @throws MissingParameters
	 * @throws wppaSavePostException
	 */
	public static function processRequestRespond( \WP_REST_Request $request ) {
		$postID = $request->get_param( 'postID' );
		$language = $request->get_param( 'language' );
		$data = $request->get_param( 'data' );

		DataManager::processResults( $postID, $language, $data );
		StatusManager::dataReceived( $postID );
	}
}
