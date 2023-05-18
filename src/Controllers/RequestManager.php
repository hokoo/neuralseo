<?php

namespace NeuralSEO\Controllers;

use iTRON\WP_Lock\WP_Lock;
use iTRON\WP_Lock\WP_Lock_Backend_DB;
use iTRON\wpConnections\Exceptions\ConnectionWrongData;
use iTRON\wpConnections\Exceptions\MissingParameters;
use iTRON\wpConnections\Exceptions\RelationNotFound;
use iTRON\wpPostAble\Exceptions\wppaSavePostException;
use NeuralSEO\Exceptions\Exception;
use NeuralSEO\Exceptions\ExcessRequest;
use NeuralSEO\Exceptions\ParallelRequest;
use NeuralSEO\Exceptions\RequestFailed;
use NeuralSEO\Factory;
use NeuralSEO\Models\RequestData;
use NeuralSEO\Settings;

class RequestManager {

	/**
	 * Listens for the requests from Frontend (actually from anywhere).
	 *
	 * @throws ExcessRequest
	 * @throws ParallelRequest
	 */
	public static function processRequestTriggering( $postID ) {
		// Ensure correct result in threads.
		$lock = new WP_Lock( 'nseo_request_triggering:' . $postID, new WP_Lock_Backend_DB() );
		if ( ! $lock->acquire( WP_Lock::WRITE, false, 0 ) ) {
			throw new ParallelRequest( $postID );
		}

		// First, check for current status.
		// Make checking if action has not failed or removed.
		StatusCheckManager::ensureCorrect( $postID );
		if ( StatusManager::isActive( $postID ) ) {
			// There is active request for this post. Skip.
			$lock->release();
			throw new ExcessRequest( $postID );
		}

		$actionID = Scheduler::setupAction( $postID );
		StatusManager::actionScheduled( $actionID, $postID );

		$lock->release();
	}

	/**
	 * Process AS action sending request to Neural API.
	 */
	public static function processRequestAction( $postID ) {
		$requestData = new RequestData( $postID );

		try {
			$result = Factory::getNeuralClient()->requestData( $requestData->toArray() );
		} catch ( RequestFailed $exception ) {
			$result = false;
		}

		if ( empty( $result ) ) {
			$actionID = Scheduler::setupAction( $postID );
			StatusManager::actionScheduled( $actionID, $postID );
			return;
		}

		StatusManager::actionPerformed( $postID );
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

	public static function processAjaxRequest() {
		if ( ! current_user_can( Settings::MANAGE_CAPS ) ) {
			wp_send_json_error( [ 'message' => __( 'You have no permissions to do this.', 'neuralseo' ), 'code' => 401 ] );
		}

		try {
			do_action( 'nseo/request/triggered', $_POST['post_id'] );
		} catch ( Exception $exception ) {
			wp_send_json_error( [ 'message' => $exception->getMessage(), 'code' => $exception->getCode() ] );
		}

		wp_send_json_success( [ 'message' => __( 'Request has been sent successfully.', 'neuralseo' ) ] );
	}
}
