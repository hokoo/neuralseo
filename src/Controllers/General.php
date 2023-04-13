<?php

namespace NeuralSEO\Controllers;

use iTRON\wpConnections\Exceptions\MissingParameters;
use iTRON\wpConnections\Exceptions\RelationWrongData;
use iTRON\wpConnections\Query\Relation;
use NeuralSEO\Factory;
use NeuralSEO\Models\RequestData;
use const NeuralSEO\CPT_DESCRIPTION;
use const NeuralSEO\CPT_TITLE;
use const NeuralSEO\REQUEST_HOOK;
use const NeuralSEO\SLUG;
use const NeuralSEO\WPC_RELATION_D2P;
use const NeuralSEO\WPC_RELATION_T2P;

class General {
	public DataManager $dataManager;
	public CPT $CPT;
	public Webhook $webhook;

	public function __construct(
		DataManager $dataManager,
		CPT $CPT,
		Webhook $webhook
	) {
		$this->dataManager = $dataManager;
		$this->CPT = $CPT;
		$this->webhook = $webhook;
	}

	public function init() {
		$this->CPT::init();
		$this->webhook->init( $this );

		add_action( 'init', [ $this, 'registerRelations' ], 10 );
		$this->registerRequestListener();
	}

	/**
	 * Listens for requests from FE.
	 *
	 * @return void
	 */
	public function registerRequestListener() {

		/**
		 * The Chain A starts here.
		 */
		add_action( 'nseo/request/triggered', [ $this, 'processRequestTriggering' ], 10, 1 );
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

	public function processRequestTriggering( $postID ) {
		$data = new RequestData( $postID );
		$actionID = as_enqueue_async_action( REQUEST_HOOK, $data->toArray(), SLUG );
		StatusManager::actionScheduled( $actionID, $postID );
	}
}
