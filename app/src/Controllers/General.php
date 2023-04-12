<?php

namespace NeuralSEO\Controllers;

use iTRON\wpConnections\Client;
use iTRON\wpConnections\Exceptions\ConnectionWrongData;
use iTRON\wpConnections\Exceptions\MissingParameters;
use iTRON\wpConnections\Exceptions\RelationNotFound;
use iTRON\wpConnections\Query\Connection;
use iTRON\wpPostAble\Exceptions\wppaSavePostException;
use NeuralSEO\Models\Description;
use NeuralSEO\Models\Title;
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
			$this->getConnectionsClient()->getRelation( WPC_RELATION_T2P )->createConnection( $titleConnectionQuery );

			$description = new Description();
			$description->getPost()->post_content = $item['description'];
			$description->publish();

			$descrConnectionQuery = new Connection();
			$descrConnectionQuery->set( 'from', $description->getPost()->ID );
			$descrConnectionQuery->set( 'to', $post_id );
			$this->getConnectionsClient()->getRelation( WPC_RELATION_D2P )->createConnection( $descrConnectionQuery );
		}
	}
}
