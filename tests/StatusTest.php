<?php

namespace NeuralSEO\Tests;

use NeuralSEO\Models\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase {

	public static function setUpBeforeClass(): void {
		define( "NeuralSEO\POST_STATUS_META", 'nseo_post_status' );
		parent::setUpBeforeClass();
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

	public function testFromArray() {
		$data = [
			'status'        => Status::PENDING,
			'actionID'      => 777,
			'lastUpd'       => 77,
			'lastRequest'   => 7,
		];

		$status = Status::fromArray( $data );
		self::assertEquals( $data, $status->toArray() );
	}
}
