<?php

namespace NeuralApi;

use NeuralApi\Client\INeuralClient;

class DevClient implements INeuralClient {

	function setAuth( string $login, string $apiKey ) {}

	function getAccount() {
		return [
			'login'     => 'Client Name',
			'status'    => 'active',
			'package'   => [
				'name'  => 'Pro 10000',
				'rests' => '5999',
			]
		];
	}

	function requestData( array $data ) {
		return 200;
	}
}
