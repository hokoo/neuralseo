<?php

namespace NeuralSEO\Models;

use const NeuralSEO\POST_STATUS_META;

class Status {
	public string $metaKey;
	public string $status;
	public int $actionID = 0;
	public int $lastUpd = 0;

	public function __construct() {
		$this->metaKey = POST_STATUS_META;
		$this->setUndefined();
	}

	public function getHumanDate(): string {
		return date_i18n( 'd-M-Y G:i:s', $this->lastUpd );
	}

	public function setUndefined() {
		$this->status = 'undefined';
	}

	public function setPending() {
		$this->status = 'pending';
	}

	public function setUpdated() {
		$this->status = 'updated';
	}

	public function setInProgress() {
		$this->status = 'in progress';
	}

	/**
	 * @return array
	 */
	public function toArray(): array {
		extract( get_object_vars( $this ) );
		return compact( 'status', 'actionID', 'lastUpd' );
	}
}
