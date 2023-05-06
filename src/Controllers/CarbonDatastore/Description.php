<?php

namespace NeuralSEO\Controllers\CarbonDatastore;

use const NeuralSEO\WPC_RELATION_D2P;

class Description extends CarbonDatastore {
	public string $type = 'description';
	public string $field = 'seo_description';
	public string $relation = WPC_RELATION_D2P;
}
