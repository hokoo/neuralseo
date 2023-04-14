<?php

namespace NeuralSEO\Models;

class RequestData {
	public string $title;
	public string $description;
	public string $language;
	public int $postID;
	public array $attributes;

	public function __construct( int $postID = 0 ) {
		// @todo Item data
		$this->postID = $postID;
		$this->title = 'Foo';
		$this->description = 'Bar';
		$this->language = 'en';
		$this->attributes = [
			'Color' => ['white','black'],
			'Sizes' => ['S','M','XXL'],
			'Length'=> [10,11,12,15],
			// etc.
		];
	}

	public function toArray(): array {
		extract( get_object_vars( $this ) );
		return compact( 'postID', 'title', 'description', 'language', 'attributes' );
	}

	public static function fromArray( array $data ): self {
		$instance = new self();
		$instance->postID       = $data['postID'];
		$instance->title        = $data['title'];
		$instance->description  = $data['description'];
		$instance->language     = $data['language'];
		$instance->attributes   = $data['attributes'];

		return $instance;
	}
}
