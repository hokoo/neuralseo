<?php

namespace NeuralApi\Client;

interface INeuralClient {
	function setAuth( string $login, string $apiKey );

	function getAccount();

	/**
	 *
	 * @param array     $data {
	 *  @type   string  $id         Post ID
	 *  @type   string  $title      Post title
	 *  @type   string  $attributes Attributes as plain text
	 *  @type   string  $language   Language of required texts
	 *  @type   int     $num        Number of variants
	 * }
	 * @return mixed
	 */
	function requestData( array $data );
}
