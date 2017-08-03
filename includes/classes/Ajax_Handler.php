<?php namespace TeamYea\Gravity_Forms\Form_Entries_Field;

/**
 * AJAX handler
 *
 * @package TeamYea\Gravity_Forms\Form_Entries_Field
 */
class Ajax_Handler extends Component
{
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init()
	{
		parent::init();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		{
			$action = filter_var( isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '', FILTER_SANITIZE_STRING );
			if ( method_exists( $this, $action ) )
			{
				// hook into action if it's method exists
				add_action( 'wp_ajax_' . $action, [ &$this, $action ] );
				add_action( 'wp_ajax_nopriv_' . $action, [ &$this, $action ] );
			}
		}
	}

	/**
	 * Search form entries
	 *
	 * @return void
	 */
	public function search_form_entries()
	{
		// nonce check
		check_admin_referer( 'gfef_search_form_entries', 'nonce' );

		$search_term  = sanitize_text_field( isset( $_REQUEST['search'] ) ? $_REQUEST['search'] : '' );
		$search_page  = absint( $_REQUEST['page'] ? $_REQUEST['page'] : 1 );
		$search_field = sanitize_key( isset( $_REQUEST['field'] ) ? $_REQUEST['field'] : '' );
		if ( empty( $search_term ) || empty( $search_field ) )
		{
			// empty or invalid params
			$this->error( __( 'Missing search parameters!', GFEF_DOMAIN ) );
		}

		$search_field = array_map( 'absint', explode( '_', $search_field ) );
		if ( 3 !== count( $search_field ) )
		{
			// empty or invalid field
			$this->error( __( 'Missing search field!', GFEF_DOMAIN ) );
		}

		$form = \GFAPI::get_form( $search_field[1] );
		if ( ! is_array( $form ) || empty( $form['fields'] ) )
		{
			// empty or invalid field
			$this->error( __( 'Missing search field!', GFEF_DOMAIN ) );
		}

		$target_field = null;
		/* @var $field \GF_Field */
		foreach ( $form['fields'] as $field )
		{
			if ( $search_field[2] === $field->id && 'form_entries' === $field->get_input_type() )
			{
				$target_field = $field;
				break;
			}
		}

		if ( null === $target_field )
		{
			// empty or invalid field
			$this->error( __( 'Missing search field!', GFEF_DOMAIN ) );
		}

		// search form id
		$search_form_id = $target_field->selected_form;
		$selected_field = \GFFormsModel::get_field( \GFAPI::get_form( $search_form_id ), $target_field->selected_field );
		$page_size      = 10;
		$offset         = $search_page > 1 ? $page_size * $search_page : 0;

		// search all field for matchs
		$search_results = \GFAPI::get_entries( $search_form_id, [
			'field_filters' => [
				[ 'key' => '0', 'operator' => 'contains', 'value' => $search_term ],
			],
		], null, [ 'offset' => $offset, 'page_size' => $page_size ] );

		// parse data before responding
		$this->success( array_map( function ( $entry ) use ( $selected_field ) {
			return [
				'text' => $selected_field->get_value_export( $entry ),
				'id'   => absint( $entry['id'] ),
			];
		}, $search_results ) );
	}

	/**
	 * AJAX Debug response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function debug( $data )
	{
		// return dump
		$this->error( $data );
	}

	/**
	 * AJAX Debug response ( dump )
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $args
	 *
	 * @return void
	 */
	public function dump( $args )
	{
		// return dump
		$this->error( print_r( func_num_args() === 1 ? $args : func_get_args(), true ) );
	}

	/**
	 * AJAX Error response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function error( $data )
	{
		wp_send_json_error( $data );
	}

	/**
	 * AJAX success response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function success( $data )
	{
		wp_send_json_success( $data );
	}

	/**
	 * AJAX JSON Response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $response
	 *
	 * @return void
	 */
	public function response( $response )
	{
		// send response
		wp_send_json( $response );
	}
}
