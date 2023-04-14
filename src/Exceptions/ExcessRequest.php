<?php

namespace NeuralSEO\Exceptions;

use Throwable;

class ExcessRequest extends Exception {
	public function __construct( $postID = 0, $code = 0, Throwable $previous = null ) {
		parent::__construct( "There is active request for the post $postID already.", $code, $previous );
	}
}
