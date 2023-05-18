<?php

namespace NeuralSEO\Controllers;

use NeuralSEO\Models\Status;
use const NeuralSEO\POST_STATUS_META;

class StatusManager {
	public static function actionScheduled( int $actionID, int $postID ) {
		$status = self::getPostStatus( $postID );
		$status->actionID = $actionID;
		$status->lastRequest = time();
		$status->setPending();
		self::setPostStatus( $postID, $status );
	}

	public static function actionPerformed( int $postID ) {
		$status = self::getPostStatus( $postID );
		$status->setInProgress();
		self::setPostStatus( $postID, $status );
	}

	public static function dataReceived( int $postID ) {
		$status = self::getPostStatus( $postID );
		$status->setUpdated();
		$status->lastUpd = time();
		self::setPostStatus( $postID, $status );
	}

	public static function isPending( int $postID ): bool {
		$status = self::getPostStatus( $postID );
		return $status->status === Status::PENDING;
	}

	public static function isInProgress( int $postID ): bool {
		$status = self::getPostStatus( $postID );
		return $status->status === Status::INPROGRESS;
	}

	public static function isActive( int $postID ): bool {
		return self::isPending( $postID ) || self::isInProgress( $postID );
	}

	public static function getPostStatus( int $postID ): Status {
		$data = is_array( $data = get_post_meta( $postID, POST_STATUS_META, true ) ) ? $data : [];
		return Status::fromArray( $data );
	}

	public static function setPostStatus( int $postID, Status $status ){
		update_post_meta( $postID, POST_STATUS_META, $status->toArray() );
	}
}
