<?php

namespace NeuralSEO\Controllers;

use const NeuralSEO\ACTIVE_TASK_POST_META;
use const NeuralSEO\REQUEST_HOOK;
use const NeuralSEO\SLUG;

class Scheduler {
	
	/**
	 * Schedules API request to Neural API.
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public static function enqueue_data_request( int $post_id ) {

		// @todo Item data
		$data = [
			'title'         => 'Foo',
			'description'   => '',
			'language'      => 'en',
			'id'            => $post_id,
			'attributes'    => [
				'Color' => ['white','black'],
				'Sizes' => ['S','M','XXL'],
				'Length'=> [10,11,12,15],
				// etc.
			]
		];

		$task_id = as_enqueue_async_action( REQUEST_HOOK, $data, SLUG );

		update_post_meta( $post_id, ACTIVE_TASK_POST_META, $task_id );
	}
}
