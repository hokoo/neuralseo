<?php

namespace NeuralSEO\Models;

use iTRON\wpPostAble\Exceptions\wppaCreatePostException;
use iTRON\wpPostAble\Exceptions\wppaLoadPostException;
use iTRON\wpPostAble\wpPostAble;
use iTRON\wpPostAble\wpPostAbleTrait;

abstract class Entity implements wpPostAble {
	use wpPostAbleTrait;

	protected string $_post_type;

	/**
	 * @throws wppaLoadPostException
	 * @throws wppaCreatePostException
	 */
	public function __construct( int $description_id = 0 ) {
		$this->wpPostAble( $this->_post_type, $description_id );
	}

	public function setLanguage( string $language ) {
		$this->setParam( 'language', $language );
	}

	public function getLanguage() {
		return $this->getParam( 'language' );
	}
}
