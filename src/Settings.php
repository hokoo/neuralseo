<?php

namespace NeuralSEO;

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use NeuralSEO\Controllers\CarbonDatastore\CarbonDatastore;
use NeuralSEO\Controllers\CarbonDatastore\Description;
use NeuralSEO\Controllers\CarbonDatastore\Title;
use NeuralSEO\Controllers\StatusManager;

class Settings {
	const MANAGE_CAPS = 'nseo_manage_options';

	public static function init() {
		add_action( 'carbon_fields_register_fields', [ self::class, 'createOptions' ] );
		add_action( 'carbon_fields_register_fields', [ self::class, 'createProductFields' ] );
		add_action( 'after_setup_theme', [ self::class, 'loadCarbon' ] );
		add_action( 'admin_print_footer_scripts', [ self::class, 'printScript' ], 99999999 );
		add_filter( 'carbon_fields_should_delete_field_value_on_save',
			[ self::class, 'preventDeleteOnSaving' ],
			50,
			2 );
	}

	/**
	 * @return void
	 * @todo Set necessary options.
	 *
	 */
	function createOptions() {
		$options_page = Container::make( 'theme_options', 'Neural SEO' )
		                         ->set_icon( 'dashicons-editor-textcolor' )
		                         ->where( 'current_user_capability', '=', self::MANAGE_CAPS )
		                         ->add_fields( [
			                         Field::make( 'html', 'nseo_common_info' )
			                              ->set_html( [ self::class, 'getAccountInfo' ] )
		                         ] );
		Container::make( 'theme_options', 'Connection', __( 'Connection' ) )
		         ->set_page_parent( $options_page )
		         ->add_fields( [
			         Field::make( 'text', 'nseo_login', 'Account Login' )
			              ->set_width( 50 ),
			         Field::make( 'text', 'nseo_api_key', 'API Key' )
			              ->set_width( 50 )
			              ->set_attribute( 'type', 'password' ),
		         ] )
			// Only administrator has access to API Key.
			     ->where( 'current_user_capability', '=', 'manage_options' );
	}

	static function createProductFields() {
		Container::make( 'post_meta', 'Neural SEO Data' )
		         ->where( 'post_type', '=', 'product' )
		         ->add_fields( [
			         Field::make( 'html', 'nseo_data_panel' )
			              ->set_html( [ self::class, 'getProductPanel' ] )
		         ] )
		         ->add_fields( [
			         Field::make( 'complex', 'nseo_data_title' )
			              ->set_width( 50 )
			              ->set_classes( 'nseo-data-set nseo-data-title' )
			              ->set_datastore( new Title() )
			              ->add_fields( 'title', [
				              Field::make( 'textarea', 'seo_title', __( 'SEO Title' ) ),
				              Field::make( 'hidden', 'id' ),
				              Field::make( 'hidden', 'connection_id' ),
				              Field::make( 'hidden', 'original_text' ),
				              Field::make( 'hidden', 'original_selected' ),
				              Field::make( 'checkbox', 'selected', __( 'Approve this title' ) )
				                   ->set_classes( 'nseo-selected-item' )
				                   ->set_option_value( CarbonDatastore::CHECK_TRUE )
				                   ->set_width( 50 )
				                   ->set_help_text( __( 'Select this field as approved.' ) ),
				              Field::make( 'checkbox', 'delete', __( 'Delete this title' ) )
				                   ->set_option_value( CarbonDatastore::CHECK_TRUE )
				                   ->set_width( 50 )
				                   ->set_help_text( __( 'This field will be deleted after `Save` button is clicked.' ) ),
			              ] )
			              ->set_header_template( 'Title ID : <%- id %>' ),
			         Field::make( 'complex', 'nseo_data_description' )
			              ->set_width( 50 )
			              ->set_classes( 'nseo-data-set nseo-data-description' )
			              ->set_datastore( new Description() )
			              ->add_fields( 'description', [
				              Field::make( 'textarea', 'seo_description', __( 'SEO Description' ) ),
				              Field::make( 'hidden', 'id' ),
				              Field::make( 'hidden', 'connection_id' ),
				              Field::make( 'hidden', 'original_text' ),
				              Field::make( 'hidden', 'original_selected' ),
				              Field::make( 'checkbox', 'selected', __( 'Approve this description' ) )
				                   ->set_classes( 'nseo-selected-item' )
				                   ->set_option_value( CarbonDatastore::CHECK_TRUE )
				                   ->set_width( 50 )
				                   ->set_help_text( __( 'Select this field as approved.' ) ),
				              Field::make( 'checkbox', 'delete', __( 'Delete this description' ) )
				                   ->set_option_value( CarbonDatastore::CHECK_TRUE )
				                   ->set_width( 50 )
				                   ->set_help_text( __( 'This field will be deleted after `Save` button is clicked.' ) ),
			              ] )
			              ->set_header_template( 'Description ID : <%- id %>' ),
		         ] );
	}

	function loadCarbon() {
		Carbon_Fields::boot();
	}

	static function getAccountLogin(): string {
		return carbon_get_theme_option( 'nseo_login' );
	}

	static function getAccountKey(): string {
		return carbon_get_theme_option( 'nseo_api_key' );
	}

	/**
	 * @return string
	 * @todo Account info
	 *
	 */
	static function getAccountInfo(): string {
		$login = self::getAccountLogin();

		return "Account Info: Login [$login]";
	}

	public static function preventDeleteOnSaving( $delete, Field\Field $field ) {
		if ( in_array( $field->get_base_name(), [ 'nseo_data_title', 'nseo_data_description' ] ) ) {
			return false;
		}

		return $delete;
	}

	public function getProductPanel() {
		$status = StatusManager::getPostStatus( get_the_ID() );
		$html   = "<div class='nseo-status status'>Status: <span>{$status->status}</span></div>";

		$last = $status->lastRequest ? date_i18n( 'd.m.Y H:i:s', $status->lastRequest ) : 'Never';
		$html .= "<div class='nseo-status last-request'>Last request: <span>$last</span></div>";

		// Provide a button to make a request.
		if ( ! StatusManager::isActive( get_the_ID() ) ) {
			$html .= "<div class='nseo-status request-button'><button class='button button-primary' id='nseo_request_button'>Request</button></div>";
		}

		return $html;
	}

	static function printScript() {
		?>
		<script>
            // Unselect all other "Set this" checkboxes when selecting one.
            processCheckboxes = () => {
                const sets = document.querySelectorAll('.nseo-data-set');
                sets.forEach(singleSet => {
                    let checkboxes = singleSet.querySelectorAll('.nseo-selected-item input');

                    // Add event listener to each checkbox
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', () => {
                            // If this checkbox is checked, check off the other checkboxes
                            if (checkbox.checked) {
                                checkboxes.forEach(otherCheckbox => {
                                    if (otherCheckbox !== checkbox) {
                                        otherCheckbox.checked = false;
                                    }
                                });
                            }
                        });
                    });
                });
            }
            (function () {
                const {addAction, didAction} = window.cf.hooks;
                (didAction('carbon-fields.init') && processCheckboxes()) ||
                addAction('carbon-fields.init', 'carbon-fields/metaboxes', processCheckboxes);
            })();

            // Add event listener to the Request button. Send REST API request.
            (function () {
                const button = document.getElementById('nseo_request_button');
                if (button) {
                    button.addEventListener('click', function (event) {
                        event.preventDefault();

                        const data = new FormData();
                        data.append('action', 'nseo_request');
                        data.append('post_id', '<?php echo get_the_ID(); ?>');

                        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                            method: 'POST',
                            body: data
                        })
                            .then((response) => response.json())
                            .then(response => {
                                alert( response.data.message );

                                if (response.success) {
									button.style.display = 'none';
								}
                            })
                            .catch((error) => {
                                console.log(error);
                                alert( "An error has occurred." );
                            });
                    });
                }
            })();
		</script>

		<style>
            /* Hide hidden fields, kek. */
            .nseo-data-set .cf-hidden {
                display: none;
            }

            /* Hide Add button. */
            .nseo-data-set .cf-complex__inserter {
                display: none;
            }

            /* Hide Duplicate and Remove buttons. */
            .nseo-data-set .cf-complex__group-action:nth-child(1),
            .nseo-data-set .cf-complex__group-action:nth-child(2) {
                display: none;
            }
		</style>
		<?php
	}
}
