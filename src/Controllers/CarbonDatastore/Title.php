<?php

namespace NeuralSEO\Controllers\CarbonDatastore;

use const NeuralSEO\WPC_RELATION_T2P;

class Title extends CarbonDatastore {
	public string $type = 'title';
	public string $field = 'seo_title';
	public string $relation = WPC_RELATION_T2P;
}
