<?php

namespace NeuralSEO\Controllers;

use const NeuralSEO\CPT_DESCRIPTION;
use const NeuralSEO\CPT_TITLE;

class CPT {
	public static function init() {
		add_action( 'init', [self::class, 'register'], 20 );
	}

	public static function register() {

		register_post_type( CPT_TITLE, [
			'public'             => false,
			'show_in_menu'       => false,
			'publicly_queryable' => false,
			'show_in_rest'       => false,
		] );

		register_post_type( CPT_DESCRIPTION, [
			'public'             => false,
			'show_in_menu'       => false,
			'publicly_queryable' => false,
			'show_in_rest'       => false,
		] );
	}
}
