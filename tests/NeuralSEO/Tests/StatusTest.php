<?php

namespace NeuralSEO\Tests;

use NeuralSEO\Models\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase {

	public function setUp(): void {
		define( "NeuralSEO\POST_STATUS_META", 'nseo_post_status' );
		parent::setUp();
	}

	public function testToArray() {
		$status = new Status();

		$timestamp = time();
		$status->setInProgress();
		$status->lastUpd = $timestamp;

		$array = $status->toArray();

		self::assertArrayHasKey( 'status', $array );
		self::assertArrayHasKey( 'actionID', $array );
		self::assertArrayHasKey( 'lastUpd', $array );
	}
}
