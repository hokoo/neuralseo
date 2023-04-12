<?php

namespace NeuralSEO\Models;

use iTRON\wpPostAble\Exceptions\wppaCreatePostException;
use iTRON\wpPostAble\Exceptions\wppaLoadPostException;
use const NeuralSEO\CPT_TITLE;

class Title extends Entity{

	/**
	 * @throws wppaLoadPostException
	 * @throws wppaCreatePostException
	 */
	public function __construct( int $description_id = 0 ) {
		$this->_post_type = CPT_TITLE;
		parent::__construct( $description_id );
	}
}
