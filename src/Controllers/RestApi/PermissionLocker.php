<?php

namespace NeuralSEO\Controllers\RestApi;

use WP_REST_Posts_Controller;

/**
 * Class PermissionLocker
 *
 * This class is used to lock the access permissions of the plugin's custom post types.
 *
 * @package NeuralSEO\Controllers\RestApi
 *
 * @since 1.0.0
 */
class PermissionLocker extends WP_REST_Posts_Controller {

	/**
	 * Posts can not be visible without having capabilities.
	 *
	 * @param $post
	 *
	 * @return bool
	 */
	public function check_read_permission( $post ): bool {
		$post_type = get_post_type_object( $post->post_type );

		return 'publish' === $post->post_status && current_user_can( $post_type->cap->read_post, $post->ID );
	}

	/**
	 * @param $request
	 *
	 * @return bool
	 */
	public function get_items_permissions_check( $request ): bool {
		$post_type = get_post_type_object( $this->post_type );

		return current_user_can( $post_type->cap->read_post );
	}
}
