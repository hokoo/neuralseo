<?php
/*
* Plugin Name: NeuralSEO
* Description: Integrate your site with Neural SEO API
* Author:      Hokku
* Version:     0.1
* License:     GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: neuralseo
* Domain Path: /languages
*/

namespace NeuralSEO;

use NeuralSEO\Controllers\CPT;
use NeuralSEO\Controllers\DataManager;
use NeuralSEO\Controllers\General;
use NeuralSEO\Controllers\Sender;
use NeuralSEO\Controllers\Webhook;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/neuralApi/autoload.php';
require_once __DIR__ . '/includes/functions.php';

const SLUG = 'neural_seo';
const REQUEST_HOOK = 'nseo/request';
const POST_STATUS_META = 'nseo_post_status';
const CPT_TITLE = 'nseo_title';
const CPT_DESCRIPTION = 'nseo_description';
const WPC_RELATION_T2P = 'title2product';
const WPC_RELATION_D2P = 'description2product';

$controller = new General(
	new DataManager(),
	new CPT(),
	new Webhook(),
	new Sender()
);
$controller->init();
