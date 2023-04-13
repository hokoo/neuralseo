<?php

namespace NeuralSEO\Controllers;

use NeuralSEO\Models\Status;
use const NeuralSEO\POST_STATUS_META;
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
	public static function enqueueDataRequest( int $post_id ) {

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

		$status = new Status();
		$status->actionID = $task_id;
		$status->setPending();

		update_post_meta( $post_id, POST_STATUS_META, $status->toArray() );
	}
}
