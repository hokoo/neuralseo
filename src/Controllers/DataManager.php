<?php

namespace NeuralSEO\Controllers;

use iTRON\wpConnections\Exceptions\ConnectionWrongData;
use iTRON\wpConnections\Exceptions\MissingParameters;
use iTRON\wpConnections\Exceptions\RelationNotFound;
use iTRON\wpConnections\Query\Connection;
use iTRON\wpPostAble\Exceptions\wppaSavePostException;
use NeuralSEO\Factory;
use NeuralSEO\Models\Description;
use NeuralSEO\Models\Title;
use const NeuralSEO\WPC_RELATION_D2P;
use const NeuralSEO\WPC_RELATION_T2P;

class DataManager {
	/**
	 * Processes data from Neural API.
	 *
	 * Received data format.
	 * @type int        $postID    Post ID.
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
	public function processResults( int $postID, string $language, array $data ){
		foreach ( $data as $item ) {
			$title = new Title();
			$title->getPost()->post_content = $item['title'];
			$title->setLanguage( $language );
			$title->publish();

			$titleConnectionQuery = new Connection();
			$titleConnectionQuery->set( 'from', $title->getPost()->ID );
			$titleConnectionQuery->set( 'to', $postID );
			$titleConnectionQuery->set( 'order', 10 );
			Factory::getConnectionsClient()->getRelation( WPC_RELATION_T2P )->createConnection( $titleConnectionQuery );

			$description = new Description();
			$description->getPost()->post_content = $item['description'];
			$description->setLanguage( $language );
			$description->publish();

			$descrConnectionQuery = new Connection();
			$descrConnectionQuery->set( 'from', $description->getPost()->ID );
			$descrConnectionQuery->set( 'to', $postID );
			$descrConnectionQuery->set( 'order', 10 );
			Factory::getConnectionsClient()->getRelation( WPC_RELATION_D2P )->createConnection( $descrConnectionQuery );

			StatusManager::dataReceived( $postID );
		}
	}
}
