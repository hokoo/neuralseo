<?php

namespace NeuralSEO;

use NeuralApi\DevClient;

function get_client() {
	static $client;

	if ( empty( $client ) ) {
		$client = new DevClient();
		$client->setAuth( Settings::getAccountLogin(), Settings::getAccountKey() );
	}

	return $client;
}


