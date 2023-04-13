<?php

namespace NeuralSEO\Controllers;

use NeuralSEO\Models\Status;
use const NeuralSEO\POST_STATUS_META;

class StatusManager {
	public static function actionScheduled( int $actionID, int $postID ) {
		$status = new Status();
		$status->actionID = $actionID;
		$status->setPending();
		self::updatePostStatus( $postID, $status );
	}

	public static function actionPerformed( int $postID ) {
		$status = new Status();
		$status->setInProgress();
		self::updatePostStatus( $postID, $status );
	}

	public static function dataReceived( int $postID ) {
		$status = new Status();
		$status->setUpdated();
		$status->lastUpd = time();
		self::updatePostStatus( $postID, $status );
	}

	private static function updatePostStatus( int $postID, Status $status ){
		update_post_meta( $postID, POST_STATUS_META, $status->toArray() );
	}
}
