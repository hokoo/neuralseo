<?php

namespace NeuralSEO;

use iTRON\wpConnections\Client;

class Factory {
	public static function getConnectionsClient(): Client {
		static $connectionsClient;

		if ( empty( $connectionsClient ) ) {
			$connectionsClient = new Client( SLUG );
		}

		return $connectionsClient;
	}
}
