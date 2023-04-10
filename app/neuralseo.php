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

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/neuralApi/autoload.php';
$client = new \NeuralApi\DevClient();
