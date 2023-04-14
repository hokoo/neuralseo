<?php

namespace NeuralSEO\Models;

use const NeuralSEO\POST_STATUS_META;

class Status {
	const UNDEFINED = 'undefined';
	const PENDING = 'pending';
	const UPDATED = 'updated';
	const INPROGRESS = 'in progress';

	public string $metaKey;
	public string $status;

	/**
	 * AS action ID related to last data updating request.
	 */
	public int $actionID = 0;

	/**
	 * Last data updating by REST API datetime.
	 * Timestamp.
	 */
	public int $lastUpd = 0;

	/**
	 * Last data updating request datetime.
	 * Timestamp.
	 */
	public int $lastRequest = 0;

	public function __construct() {
		$this->metaKey = POST_STATUS_META;
		$this->setUndefined();
	}

	public function getHumanDate(): string {
		return date_i18n( 'd-M-Y G:i:s', $this->lastUpd );
	}

	public function setUndefined() {
		$this->status = self::UNDEFINED;
	}

	public function setPending() {
		$this->status = self::PENDING;
	}

	public function setUpdated() {
		$this->status = self::UPDATED;
	}

	public function setInProgress() {
		$this->status = self::INPROGRESS;
	}

	/**
	 * @return array
	 */
	public function toArray(): array {
		extract( get_object_vars( $this ) );
		return compact( 'status', 'actionID', 'lastUpd', 'lastRequest' );
	}

	public static function fromArray( array $data ): Status {
		$instance = new self();
		$instance->status = $data['status'] ?? self::UNDEFINED;
		$instance->actionID = $data['actionID'] ?? $instance->actionID;
		$instance->lastUpd = $data['lastUpd'] ?? $instance->lastUpd;
		$instance->lastRequest = $data['lastRequest'] ?? $instance->lastRequest;

		return $instance;
	}
}
