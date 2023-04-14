<?php

namespace NeuralSEO;

use ActionScheduler;

function getActionStatusByID( int $actionID ) {
	// Make sure Action Scheduler is loaded
	if ( ! class_exists('ActionScheduler') ) {
		return false;
	}

	// Get the action from the ActionScheduler store
	$action = ActionScheduler::store()->fetch_action( $actionID );

	// Check whether the action exists or not.
	if ( $action instanceof \ActionScheduler_NullAction ) {
		return false;
	}

	try {
		$status = ActionScheduler::store()->get_status( $actionID );
	} catch ( \Throwable $exception ) {
		return false;
	}

	return $status;
}

function isActionPending( int $actionID ): bool {
	return 'pending' === getActionStatusByID( $actionID );
}
