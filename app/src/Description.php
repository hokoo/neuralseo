<?php

namespace NeuralSEO;

use iTRON\wpPostAble\Exceptions\wppaCreatePostException;
use iTRON\wpPostAble\Exceptions\wppaLoadPostException;
use iTRON\wpPostAble\wpPostAble;
use iTRON\wpPostAble\wpPostAbleTrait;
use NeuralSEO\Models\Entity;

class Description extends Entity implements wpPostAble {
	use wpPostAbleTrait;

	/**
	 * @throws wppaLoadPostException
	 * @throws wppaCreatePostException
	 */
	public function __construct( int $description_id = 0 ) {
		$this->post_type = CPT_DESCRIPTION;
		parent::__construct( $description_id );
	}
}
