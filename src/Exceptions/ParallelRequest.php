<?php

namespace NeuralSEO\Exceptions;

use Throwable;

class ParallelRequest extends Exception {
	public function __construct( $postID = 0, $code = 1, Throwable $previous = null ) {
		parent::__construct( sprintf( __( "Another request is already being performed now for the post %s.", 'neuralseo' ), $postID ), $code, $previous );
	}
}
