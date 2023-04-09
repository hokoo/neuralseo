<?php
function neuralApiSdkLoadClass( $className ) {
	if ( strncmp( 'NeuralApi', $className, 9 ) !== 0 ) {
		return;
	}
	$length = 9;

	$path = dirname( __FILE__ );
	$path .= str_replace('\\', '/', substr($className, $length)) . '.php';
	if ( file_exists( $path ) ) {
		require $path;
	}
}

spl_autoload_register( 'neuralApiSdkLoadClass' );
