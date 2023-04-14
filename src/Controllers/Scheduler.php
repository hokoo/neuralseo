<?php

namespace NeuralSEO\Controllers;

use const NeuralSEO\REQUEST_HOOK;
use const NeuralSEO\SLUG;

class Scheduler {
	public static function setupAction( int $postID ): int {
		return as_enqueue_async_action( REQUEST_HOOK, [ 'postID' => $postID ], SLUG );
	}
}
