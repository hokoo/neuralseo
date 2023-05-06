<?php

namespace NeuralSEO\Controllers;

use NeuralSEO\Controllers\RestApi\PermissionLocker;
use NeuralSEO\Settings;
use const NeuralSEO\CPT_DESCRIPTION;
use const NeuralSEO\CPT_TITLE;

class CPT {
	public static function init() {
		add_action( 'init', [self::class, 'register'], 20 );
	}

	public static function register() {
		$caps = [
			'edit_post'              => Settings::MANAGE_CAPS,
			'read_post'              => Settings::MANAGE_CAPS,
			'delete_post'            => Settings::MANAGE_CAPS,
			'edit_posts'             => Settings::MANAGE_CAPS,
			'edit_others_posts'      => Settings::MANAGE_CAPS,
			'publish_posts'          => Settings::MANAGE_CAPS,
			'read_private_posts'     => Settings::MANAGE_CAPS,
			'read'                   => Settings::MANAGE_CAPS,
			'delete_posts'           => Settings::MANAGE_CAPS,
			'delete_private_posts'   => Settings::MANAGE_CAPS,
			'delete_published_posts' => Settings::MANAGE_CAPS,
			'delete_others_posts'    => Settings::MANAGE_CAPS,
			'edit_private_posts'     => Settings::MANAGE_CAPS,
			'edit_published_posts'   => Settings::MANAGE_CAPS,
			'create_posts'           => Settings::MANAGE_CAPS,
		];

		register_post_type( CPT_TITLE, [
			'public'                => true,
			'show_in_menu'          => true,
			'publicly_queryable'    => false,
			'show_in_rest'          => true,
			'rest_controller_class' => PermissionLocker::class,
			'capabilities'          => $caps
		] );

		register_post_type( CPT_DESCRIPTION, [
			'public'                => true,
			'show_in_menu'          => true,
			'publicly_queryable'    => false,
			'show_in_rest'          => true,
			'rest_controller_class' => PermissionLocker::class,
			'capabilities'          => $caps
		] );
	}
}
