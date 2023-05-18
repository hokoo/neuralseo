<?php
$test_root = getenv( 'WP_TESTS_DIR' ) ? : dirname( __FILE__ ) . '/../wordpress-develop/tests/phpunit';
define( "WP_TESTS_CONFIG_FILE_PATH", $test_root . '/../../wp-tests-config.php' );
require $test_root . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../neuralseo.php';
}
echo $test_root;
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $test_root . '/includes/bootstrap.php';
