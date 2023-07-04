<?php

namespace NeuralSEO\Controllers;

use iTRON\wpConnections\Exceptions\ConnectionWrongData;
use iTRON\wpConnections\Exceptions\MissingParameters;
use iTRON\wpConnections\Exceptions\RelationNotFound;
use iTRON\wpConnections\Query\Connection;
use iTRON\wpPostAble\Exceptions\wppaSavePostException;
use NeuralSEO\Controllers\CarbonDatastore\CarbonDatastore;
use NeuralSEO\Factory;
use NeuralSEO\Models\Description;
use NeuralSEO\Models\Title;
use Ramsey\Collection\AbstractCollection;

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
	public static function processResults( int $postID, string $language, array $data ){
		foreach ( $data as $item ) {
			$title = new Title();
			$title->getPost()->post_content = $item['title'];
			$title->setLanguage( $language );
			$title->publish();

			$tConnectionQuery = new Connection();
			$tConnectionQuery->set( 'from', $title->getPost()->ID );
			$tConnectionQuery->set( 'to', $postID );
			$tConnectionQuery->set( 'order', CarbonDatastore::BASIC );
			Factory::getConnectionsClient()->getRelation( WPC_RELATION_T2P )->createConnection( $tConnectionQuery );

			$description = new Description();
			$description->getPost()->post_content = $item['description'];
			$description->setLanguage( $language );
			$description->publish();

			$dConnectionQuery = new Connection();
			$dConnectionQuery->set( 'from', $description->getPost()->ID );
			$dConnectionQuery->set( 'to', $postID );
			$dConnectionQuery->set( 'order', CarbonDatastore::BASIC );
			Factory::getConnectionsClient()->getRelation( WPC_RELATION_D2P )->createConnection( $dConnectionQuery );
		}
	}

    /**
     * Returns title for post.
     *
     * @param int $postID
     *
     * @return string
     *
     * @throws RelationNotFound
     */
    public static function getTitle( int $postID ): string
    {
        $query = new Connection();
        $query->set( 'to', $postID );

        // @todo: add policy "there is no selected post" applying
        $titles = Factory::getConnectionsClient()
                        ->getRelation( WPC_RELATION_T2P )
                        ->findConnections( $query )
                        ->sort( 'order', AbstractCollection::SORT_ASC );

        $title = '';
        if ( $titles->count() ) {
            $title = get_post_field( 'post_content', $titles->first()->from );
        }

        return apply_filters( 'nseo/data/getTitle', $title, $postID );
    }

    /**
     * Returns description for post.
     *
     * @param int $postID
     *
     * @return string
     *
     * @throws RelationNotFound
     */
    public static function getDescription( int $postID ): string {
        $query = new Connection();
        $query->set( 'to', $postID );

        // @todo: add policy "there is no selected post" applying
        $descriptions = Factory::getConnectionsClient()
                               ->getRelation( WPC_RELATION_D2P )
                               ->findConnections( $query )
                               ->sort( 'order', AbstractCollection::SORT_ASC );

        $description = '';
        if ( $descriptions->count() ) {
            $description = get_post_field( 'post_content', $descriptions->first()->from );
        }

        return apply_filters( 'nseo/data/getDescription', $description, $postID );
    }
}
