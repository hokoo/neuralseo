<?php

namespace NeuralSEO\Controllers;

use iTRON\wpConnections\Client;
use iTRON\wpConnections\Exceptions\ConnectionWrongData;
use iTRON\wpConnections\Exceptions\MissingParameters;
use iTRON\wpConnections\Exceptions\RelationNotFound;
use iTRON\wpConnections\Exceptions\RelationWrongData;
use iTRON\wpConnections\Query\Connection;
use iTRON\wpConnections\Query\Relation;
use iTRON\wpPostAble\Exceptions\wppaSavePostException;
use NeuralSEO\Models\Description;
use NeuralSEO\Models\Title;
use const NeuralSEO\CPT_DESCRIPTION;
use const NeuralSEO\CPT_TITLE;
use const NeuralSEO\SLUG;
use const NeuralSEO\WPC_RELATION_D2P;
use const NeuralSEO\WPC_RELATION_T2P;

class General {
	private Client $connectionsClient;

	public function getConnectionsClient(): Client {
		if ( empty( $this->connectionsClient ) ) {
			$this->connectionsClient = new Client( SLUG );
		}

		return $this->connectionsClient;
	}

	/**
	 * @throws MissingParameters
	 * @throws RelationWrongData
	 */
	public function init() {
		$this->registerRelations();
	}

	/**
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

		$this->getConnectionsClient()->registerRelation( $title2product );
		$this->getConnectionsClient()->registerRelation( $description2product );
	}

	/**
	 * Processes data from Neural API.
	 *
	 * Received data format.
	 * @type int        $post_id    Post ID.
	 * @type string     $language   Language of generated data.
	 * @type array      $data {
	 *      @type   string  $title
	 *      @type   string  $description
	 * }
	 *
	 * @return void
	 *
	 * @throws RelationNotFound
	 * @throws ConnectionWrongData
	 * @throws MissingParameters
	 * @throws wppaSavePostException
	 */
	public function processResults( int $post_id, string $language, array $data ){
		foreach ( $data as $item ) {
			$title = new Title();
			$title->getPost()->post_content = $item['title'];
			$title->publish();

			$titleConnectionQuery = new Connection();
			$titleConnectionQuery->set( 'from', $title->getPost()->ID );
			$titleConnectionQuery->set( 'to', $post_id );
			$titleConnectionQuery->set( 'order', 10 );
			$this->getConnectionsClient()->getRelation( WPC_RELATION_T2P )->createConnection( $titleConnectionQuery );

			$description = new Description();
			$description->getPost()->post_content = $item['description'];
			$description->publish();

			$descrConnectionQuery = new Connection();
			$descrConnectionQuery->set( 'from', $description->getPost()->ID );
			$descrConnectionQuery->set( 'to', $post_id );
			$descrConnectionQuery->set( 'order', 10 );
			$this->getConnectionsClient()->getRelation( WPC_RELATION_D2P )->createConnection( $descrConnectionQuery );
		}
	}
}
