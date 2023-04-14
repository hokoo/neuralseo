<?php

namespace NeuralSEO;

use ActionScheduler;

function get_action_status_by_id( $action_id ) {
	// Make sure Action Scheduler is loaded
	if (!class_exists('ActionScheduler')) {
		return false;
	}

	// Get the action from the ActionScheduler store
	$action = ActionScheduler::store()->fetch_action($action_id);

	// Check if the action exists
	if ($action) {
		return ActionScheduler::store()->get_status($action_id);
	} else {
		return false;
	}
}
