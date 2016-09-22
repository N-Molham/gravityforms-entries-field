<?php namespace TeamYea\Gravity_Forms\Form_Entries_Field;

use GFAPI;

/**
 * Backend logic
 *
 * @package TeamYea\Gravity_Forms\Form_Entries_Field
 */
class Backend extends Component
{
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init()
	{
		parent::init();

		// GravityForm general section settings
		add_action( 'gform_field_standard_settings', [ &$this, 'field_settings_options' ], 10, 2 );

		// GravityForm settings fields tooltips
		add_filter( 'gform_tooltips', [ &$this, 'field_tooltips' ] );

		// WP admin enqueues
		add_action( 'admin_enqueue_scripts', [ &$this, 'load_assets' ] );
	}

	/**
	 * Load assets
	 *
	 * @return void
	 */
	public function load_assets()
	{
		if ( 'gf_edit_forms' !== filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) )
		{
			// skip unwanted page request
			return;
		}

		// loading path
		$load_path = GFEF_URI . 'assets/' . ( Helpers::is_script_debugging() ? 'src' : 'dist' ) . '/';

		// load main JS file
		wp_enqueue_script( 'gfef-form', $load_path . 'js/admin.js', [ 'jquery' ], gfef_version(), true );
	}

	/**
	 * Settings fields' tooltips
	 *
	 * @param array $tooltips
	 *
	 * @return array
	 */
	public function field_tooltips( $tooltips )
	{
		// extra tips
		$tooltips['gfef_form']       = __( '<h6>Form</h6>Which form to load entries?', GFEF_DOMAIN );
		$tooltips['gfef_form_field'] = __( '<h6>Form Field</h6>Which form field use in the dropdown menu options?', GFEF_DOMAIN );

		return $tooltips;
	}

	/**
	 * Render field settings options
	 *
	 * @param int $placement
	 * @param int $form_id
	 *
	 * @return void
	 */
	public function field_settings_options( $placement, $form_id )
	{
		if ( 20 !== $placement )
		{
			// skip unwanted location
			return;
		}

		$forms = array_map( function ( $form )
		{
			// get only wanted information
			return [
				'id'     => $form['id'],
				'title'  => $form['title'],
				'fields' => array_map( function ( $field )
				{
					// get only needed form input information
					return [
						'id'    => $field->id,
						'label' => $field->label,
					];
				}, array_values( array_filter( $form['fields'], function ( $field )
				{
					// filter out section items/fields
					return 'section' !== $field['type'];
				} ) ) ),
			];
		}, GFAPI::get_forms() ); // query registered forms

		gfef_view( 'settings/form_field', compact( 'forms' ) );
	}

	/**
	 * Get form entries list
	 *
	 * @param int $form_id
	 *
	 * @return array
	 */
	public function get_form_entries( $form_id )
	{
		return GFAPI::get_entries( $form_id );
	}
}
