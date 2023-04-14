<?php

namespace NeuralSEO\Controllers;

class WebhookManager {
	const NAMESPACE = 'neuralseo/v1';
	const BASE = '/respond/';

	public static function registerRestRoutes() {
		register_rest_route(
			self::NAMESPACE,
			self::BASE . '(?P<postID>[\d]+)',
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
					'callback'  => [ RequestManager::class, 'processRequestRespond' ],
					// @todo permission_callback
					'permission_callback' => '__return_true',
				]
			]
		);
	}
}
