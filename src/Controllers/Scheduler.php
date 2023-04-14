<?php

namespace NeuralSEO\Controllers;

use const NeuralSEO\REQUEST_HOOK;
use const NeuralSEO\SLUG;

class Scheduler {
	public static function setupAction( array $data ): int {
		return as_enqueue_async_action( REQUEST_HOOK, $data, SLUG );
	}
}
