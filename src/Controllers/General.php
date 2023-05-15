<?php

namespace NeuralSEO\Controllers;

use iTRON\wpConnections\Exceptions\MissingParameters;
use iTRON\wpConnections\Exceptions\RelationWrongData;
use iTRON\wpConnections\Query\Relation;
use NeuralSEO\Factory;
use NeuralSEO\Settings;

use const NeuralSEO\CPT_DESCRIPTION;
use const NeuralSEO\CPT_TITLE;
use const NeuralSEO\REQUEST_HOOK;
use const NeuralSEO\WPC_RELATION_D2P;
use const NeuralSEO\WPC_RELATION_T2P;

class General {

	public function init() {
		CPT::init();
		Settings::init();

		add_action( 'init', [ $this, 'registerRelations' ], 10 );

		/**
		 * The Chain A starts here.
		 * Listens for the requests from Frontend (actually from anywhere).
		 */
		add_action( 'nseo/request/triggered', [ RequestManager::class, 'processRequestTriggering' ], 10, 1 );

		/**
		 * The Chain B starts here.
		 * Process AS action sending request to Neural API.
		 */
		add_action( REQUEST_HOOK, [ RequestManager::class, 'processRequestAction' ], 10, 1 );

		/**
		 * The Chain C starts here.
		 * Receives data from Neural API.
		 */
		add_action( 'rest_api_init', [ WebhookManager::class, 'registerRestRoutes' ], 10 );
	}

	/**
	 * Prepares the connection client.
	 *
	 * @throws RelationWrongData
	 * @throws MissingParameters
	 */
	public function registerRelations() {
		$title2product = new Relation();
		$title2product
			->set( 'name', WPC_RELATION_T2P )
			->set( 'from', CPT_TITLE )
			->set( 'to', 'product' )
			->set( 'cardinality', '1-m' )
			->set( 'duplicatable', false );

		$description2product = new Relation();
		$description2product
			->set( 'name', WPC_RELATION_D2P )
			->set( 'from', CPT_DESCRIPTION )
			->set( 'to', 'product' )
			->set( 'cardinality', '1-m' )
			->set( 'duplicatable', false );

		Factory::getConnectionsClient()->registerRelation( $title2product );
		Factory::getConnectionsClient()->registerRelation( $description2product );
	}

	/**
	 * @return void
	 * @todo Add caps for other roles, e.g. `SEO Manager` by Yoast.
	 *
	 */
	public function processActivationHook() {
		$role = get_role( 'administrator' );
		$role->add_cap( Settings::MANAGE_CAPS, true );

		do_action( 'nseo/capabilities/set' );
	}
}
