<?php

namespace NeuralApi;

use NeuralApi\Client\INeuralClient;
use NeuralSEO\Exceptions\RequestFailed;

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

	/**
	 * @throws RequestFailed
	 */
	function requestData( array $data ): bool {
		if ( 0 ) {
			throw new RequestFailed();
		}

		return true;
	}
}
