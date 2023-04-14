<?php
/**
 * Checks whether request status is actual or outdated.
 */

namespace NeuralSEO\Controllers;

use function NeuralSEO\isActionPending;

class StatusCheckManager {

	/**
	 * Checks and corrects post request status if necessary.
	 * Returns `true` if status has been corrected, otherwise returns `false`.
	 *
	 * @param int $postID
	 *
	 * @return bool
	 */
	public static function ensureCorrect( int $postID ): bool {
		$postStatus = StatusManager::getPostStatus( $postID );

		if ( StatusManager::isPending( $postID ) ) {
			// AS action should exist and have 'pending' status, otherwise set undefined.
			if ( ! isActionPending( $postStatus->actionID ) ) {
				// This is not desirable behavior, because the reason of its is unknown.
				// So, it would be appropriate to @todo make a record to log about.
				$postStatus->setUndefined();
				StatusManager::setPostStatus( $postID, $postStatus );
				return true;
			}
		}

		if ( StatusManager::isInProgress( $postID ) ) {
			// There is no reliable way to know whether this status actual or outdated.
			// It might be ok to @todo evaluate status establishment time.
		}

		return false;
	}
}
