<?php

namespace NeuralSEO;

use iTRON\wpConnections\Client;
use NeuralApi\Client\INeuralClient;
use NeuralApi\DevClient;

class Factory {
	public static function getNeuralClient(): INeuralClient {
		static $client;

		if ( empty( $client ) ) {
			$client = new DevClient();
			$client->setAuth( Settings::getAccountLogin(), Settings::getAccountKey() );
		}

		return $client;
	}

	public static function getConnectionsClient(): Client {
		static $connectionsClient;

		if ( empty( $connectionsClient ) ) {
			$connectionsClient = new Client( SLUG );
		}

		return $connectionsClient;
	}
}
