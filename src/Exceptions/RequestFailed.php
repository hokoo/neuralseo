<?php

namespace NeuralSEO\Exceptions;

use Throwable;

class RequestFailed extends Exception {
	public function __construct( $code = 2, Throwable $previous = null ) {
		parent::__construct( __( 'HTTP request failed to reach the API.', 'neuralseo' ), $code, $previous );
	}
}
