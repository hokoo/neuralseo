<?php

namespace NeuralSEO\Controllers;

class Render {
    static function addFilters() {
        // Filters the titles and meta descriptions of the woocommerce products.
        add_filter( 'wpseo_title', [ __CLASS__, 'filterTitle' ], 10, 1 );
        add_filter( 'wpseo_metadesc', [ __CLASS__, 'filterMetaDescription' ], 10, 1 );

        // @todo Add compatibility with other SEO plugins.
    }

    static function filterTitle( $title ) {
        if ( ! get_the_ID() ) {
            return $title;
        }
        return DataManager::getTitle( get_the_ID() );
    }

    static function filterMetaDescription( $description ) {
        if ( ! get_the_ID() ) {
            return $description;
        }
        return DataManager::getDescription( get_the_ID() );
    }
}
