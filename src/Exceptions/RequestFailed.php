<?php

namespace NeuralSEO\Exceptions;

use Throwable;

class RequestFailed extends Exception {
	public function __construct( $message = "", $code = 0, Throwable $previous = null ) {
		parent::__construct( 'HTTP request to API failed. ' . $message, $code, $previous );
	}
}
