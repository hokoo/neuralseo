<?php

namespace NeuralSEO\Exceptions;

use Throwable;

class ExcessRequest extends Exception {
	public function __construct( $postID = 0, $code = 3, Throwable $previous = null ) {
		parent::__construct( sprintf( __( "Another active request already exists for the post %s.", 'neuralseo' ), $postID ), $code, $previous );
	}
}
