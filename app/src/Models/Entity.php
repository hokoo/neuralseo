<?php

namespace NeuralSEO\Models;

use iTRON\wpPostAble\Exceptions\wppaCreatePostException;
use iTRON\wpPostAble\Exceptions\wppaLoadPostException;
use iTRON\wpPostAble\wpPostAble;
use iTRON\wpPostAble\wpPostAbleTrait;

abstract class Entity implements wpPostAble {
	use wpPostAbleTrait;

	/**
	 * @throws wppaLoadPostException
	 * @throws wppaCreatePostException
	 */
	public function __construct( int $description_id = 0 ) {
		$this->wpPostAble( $this->post_type, $description_id );
	}

	public function setLanguage( string $language ) {
		$this->setParam( 'language', $language );
	}

	public function getLanguage() {
		return $this->getParam( 'language' );
	}
}
