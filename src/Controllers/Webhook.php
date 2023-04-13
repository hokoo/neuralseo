<?php

namespace NeuralSEO\Controllers;


/**
 * @todo
 * Register REST API hook for receiving results.
 */

use iTRON\wpConnections\Exceptions\ConnectionWrongData;
use iTRON\wpConnections\Exceptions\MissingParameters;
use iTRON\wpConnections\Exceptions\RelationNotFound;
use iTRON\wpPostAble\Exceptions\wppaSavePostException;

class Webhook {
	private General $general;
	private string $namespace = 'neuralseo/v1';
	private string $base = '/respond/';

	public function init( General $general ) {
		$this->general = $general;
		add_action( 'rest_api_init', [ $this, 'registerRestRoutes' ], 10 );
	}

	/**
	 * Receives data from Neural API.
	 * The Chain B starts here.
	 *
	 * @throws RelationNotFound
	 * @throws ConnectionWrongData
	 * @throws MissingParameters
	 * @throws wppaSavePostException
	 */
	public function process( \WP_REST_Request $request ) {
		$post_id = $request->get_param( 'postID' );
		$language = $request->get_param( 'language' );
		$data = $request->get_param( 'data' );

		$this->general->dataManager->processResults( $post_id, $language, $data );
	}

	public function registerRestRoutes() {
		register_rest_route(
			$this->namespace,
			$this->base . '(?P<postID>[\d]+)',
			[
				'args'  => [
					'postID' => [
						'description'   => 'Post ID',
						'type'          => 'integer',
						'required'      => 'true',
					],
					'data'  => [
						'description'   => 'Post meta data',
						'type'          => 'array',
						'required'      => 'true',
					],
					'language'  => [
						'description'   => 'Post data language',
						'type'          => 'string',
						'required'      => 'true',
					]
				],
				[
					'methods'   => \WP_REST_Server::CREATABLE,
					'callback'  => [ $this, 'process' ],
					// @todo permission_callback
					'permission_callback' => '__return_true',
				]
			]
		);
	}
}
