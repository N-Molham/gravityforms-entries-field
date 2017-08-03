<?php namespace TeamYea\Gravity_Forms\Form_Entries_Field;

/**
 * Frontend logic
 *
 * @package TeamYea\Gravity_Forms\Form_Entries_Field
 */
class Frontend extends Component
{
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init()
	{
		parent::init();

		// Before form get rendered
		add_filter( 'gform_pre_render', [ &$this, 'load_assets' ] );
	}

	/**
	 * Load JS & CSS assets
	 *
	 * @param array $form
	 *
	 * @return array
	 */
	public function load_assets( $form )
	{
		// Select2 assets
		wp_enqueue_style( 'select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', null, '4.0.3' );
		wp_register_script( 'select2-script', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', [ 'jquery' ], '4.0.3', true );
		wp_enqueue_script( 'gfef-form-entries', Helpers::enqueue_path() . 'js/form-entries.js', [
			'jquery',
			'select2-script',
		], Helpers::assets_version(), true );

		wp_localize_script( 'gfef-form-entries', 'gfef_params', [
			'ajax_url'    => admin_url( 'admin-ajax.php' ),
			'ajax_params' => [
				'action' => 'search_form_entries',
				'nonce'  => wp_create_nonce( 'gfef_search_form_entries' ),
			],
		] );

		return $form;
	}
}
