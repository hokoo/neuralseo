<?php

namespace NeuralSEO\Controllers\CarbonDatastore;

use Carbon_Fields\Datastore\Datastore;
use Carbon_Fields\Field\Field;
use iTRON\wpConnections\Exceptions\ConnectionWrongData;
use iTRON\wpConnections\Exceptions\RelationNotFound;
use iTRON\wpConnections\Query\Connection;
use NeuralSEO\Factory;
use Ramsey\Collection\AbstractCollection;
use Ramsey\Collection\CollectionInterface;

abstract class CarbonDatastore extends Datastore {
	public const APPROVED = 0;
	public const BASIC = 10;
	public const CHECK_TRUE = 'yes';
	public string $type = '';
	public string $field = '';
	public string $relation = '';

	public function init() {
	}

	/**
	 * @throws RelationNotFound
	 */
	public function load( Field $field ): array {
		$postsCollection = $this->loadConnectedPosts( $field );

		$result = [];
		foreach ( $postsCollection as $item ) {
			$content  = get_post_field( 'post_content', $item->from );
			$approved = $item->order === self::APPROVED ? self::CHECK_TRUE : null;
			/** @var \iTRON\wpConnections\Connection $item */
			$result[] = [
				'_type'             => $this->type,
				$this->field        => $content,
				'original_text'     => $content,
				'id'                => $item->from,
				'connection_id'     => $item->id,
				'selected'          => $approved,
				'original_selected' => $approved,
			];
		}

		return $result;
	}

	/**
	 * Receives nasty set of fields from admin frontend.
	 * When some subfield get deleted, it just does not come to here,
	 * so that method has to calculate what had been deleted on the frontend before the `Save` button got clicked.
	 *
	 * But since it most likely will cause undesired removing of connected posts,
	 * native carbon fields removing is disabled.
	 *
	 * Subfields have a special field `delete` as a checkbox, and it is checked when the subfield is to be deleted.
	 * So that method can just check if it is checked and delete the post.
	 *
	 * @param Field $field
	 *
	 * @return void
	 * @throws RelationNotFound
	 */
	public function save( Field $field ) {
		// Catches top-level (root) fields only.
		if ( ! empty( $field->get_hierarchy() ) ) {
			return;
		}

		foreach ( $field->get_formatted_value() as $item ) {
			// Process deleting.
			if ( ! empty( $item['delete'] ) ) {
				$this->deleteConnectedPost( $item['id'] );
				continue;
			}

			// Process order (approve) changing.
			if ( $item['original_selected'] !== $item['selected'] ) {
				$query = new Connection();
				$query
					->set( 'id', $item['connection_id'] )
					->set( 'order', $item['selected'] ? self::APPROVED : self::BASIC );

				try {
					Factory::getConnectionsClient()
					       ->getRelation( $this->relation )
					       ->updateConnection( $query );
				} catch ( ConnectionWrongData $e ) {
					error_log( $e->getMessage() );
				}
			}

			// Process text updating.
			if ( $item['original_text'] !== trim( $item[ $this->field ] ) ) {
				$this->saveConnectedPost( $item );
			}
		}
	}

	/**
	 * This method is called each time before save() method got invoked.
	 * It is a part of fields value update logic by Carbon Fields.
	 * Since that does not fit, it is just not used.
	 * Moreover, this behavior is prevented by filter `carbon_fields_should_delete_field_value_on_save`.
	 *
	 * And finally, by default this method is not called when a field get actually deleted, lol.
	 *
	 * @param Field $field
	 *
	 * @return void
	 */
	public function delete( Field $field ) {
	}

	/**
	 * Loads connected posts from database.
	 *
	 * @param Field $field
	 *
	 * @return CollectionInterface
	 * @throws RelationNotFound
	 */
	private function loadConnectedPosts( Field $field ): CollectionInterface {
		$query = new Connection();
		$query->set( 'to', $this->get_object_id() );

		return Factory::getConnectionsClient()
		              ->getRelation( $this->relation )
		              ->findConnections( $query )
		              ->sort( 'order', AbstractCollection::SORT_ASC );
	}

	private function saveConnectedPost( array $data ) {
		wp_update_post( [
			'ID'           => $data['id'],
			'post_content' => $data[ $this->field ]
		] );
	}

	private function deleteConnectedPost( int $id ) {
		wp_delete_post( $id, true );
	}
}