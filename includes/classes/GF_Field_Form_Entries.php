<?php namespace TeamYea\Gravity_Forms\Form_Entries_Field;

use GF_Entry_List_Table;
use GF_Field_Select;
use GFAPI;
use GFFormsModel;

/**
 * Form Entries field class
 *
 * @package TeamYea\Gravity_Forms\Form_Entries_Field
 */
class GF_Field_Form_Entries extends GF_Field_Select
{
	/**
	 * Field type key name
	 *
	 * @var string
	 */
	public $type = 'form_entries';

	public function get_form_editor_field_title()
	{
		return esc_attr__( 'Form Entries', GFEF_DOMAIN );
	}

	public function get_form_editor_button()
	{
		return [
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
		];
	}

	public function get_form_editor_field_settings()
	{
		return array_merge( array_filter( parent::get_form_editor_field_settings(), function ( $setting_class )
		{
			return !in_array( $setting_class, [ 'choices_setting', 'prepopulate_field_setting' ] );
		} ), [ 'gfef_form_setting', 'gfef_form_field_setting' ] );
	}

	/**
	 * Get from entries for select dropdown menu
	 *
	 * @param string $value
	 *
	 * @return array
	 */
	public function get_form_entries_choices( $value = '' )
	{
		$form_entries = [];
		if ( !isset( $this->selected_form ) || !isset( $this->selected_field ) )
		{
			// no form or field selected!
			return $form_entries;
		}

		// fetch form entries
		$form_entries = gf_form_entries_field()->backend->get_form_entries( absint( $this->selected_form ) );

		if ( sizeof( $form_entries ) > 0 )
		{
			$selected_field = GFFormsModel::get_field( GFAPI::get_form( $this->selected_form ), $this->selected_field );
			$form_entries   = array_map( function ( $entry ) use ( $selected_field, $value )
			{
				return [
					'text'       => $selected_field->get_value_export( $entry ),
					'value'      => $entry['id'],
					'isSelected' => absint( $entry['id'] ) === absint( $value ),
					'price'      => '',
				];
			}, $form_entries );
		}

		return $form_entries;
	}

	public function get_choices( $value )
	{
		$this->choices = $this->get_form_entries_choices( $value );

		return parent::get_choices( $value );
	}

	public function get_field_input( $form, $value = '', $entry = null )
	{
		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$id       = $this->id;
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		$logic_event        = $this->get_conditional_logic_event( 'change' );
		$size               = $this->size;
		$class_suffix       = $is_entry_detail ? '_admin' : '';
		$class              = $size . $class_suffix;
		$css_class          = trim( esc_attr( $class ) . ' gfield_select' );
		$tabindex           = $this->get_tabindex();
		$disabled_text      = $is_form_editor ? 'disabled="disabled"' : '';
		$required_attribute = $this->isRequired ? 'aria-required="true"' : '';
		$invalid_attribute  = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

		return sprintf( "<div class='ginput_container ginput_container_select'><select name='input_%d' id='%s' $logic_event class='%s' $tabindex %s %s %s>%s</select></div>", $id, $field_id, $css_class, $disabled_text, $required_attribute, $invalid_attribute, $this->get_choices( $value ) );
	}

	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form )
	{
		return $this->get_value_entry_output( $value, true );
	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' )
	{
		return $this->get_value_entry_output( $value, true );
	}

	/**
	 * Get entry value output
	 *
	 * @param mixed $value
	 * @param bool  $with_link
	 *
	 * @return string
	 */
	public function get_value_entry_output( $value, $with_link = false )
	{
		$selected_form  = GFAPI::get_form( $this->selected_form );
		$selected_field = GFFormsModel::get_field( $selected_form, $this->selected_field );
		$selected_entry = GFAPI::get_entry( $value );
		$value_output   = $selected_field->get_value_export( $selected_entry );

		if ( $with_link )
		{
			$table = new GF_Entry_List_Table( [
				'form' => $selected_form,
			] );

			// output in link
			return sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $table->get_detail_url( $selected_entry ) ), $value_output );
		}

		return $value_output;
	}

	public function sanitize_entry_value( $value, $form_id )
	{
		return absint( $value );
	}

	public function validate( $value, $form )
	{
		$selected_entry = absint( $value );
		if ( 0 === $selected_entry )
		{
			// invalid selection
			$this->failed_validation  = true;
			$this->validation_message = __( 'Invalid Entry Selection!', GFEF_DOMAIN );
		}

		$allowed_entries = array_map( function ( $entry )
		{
			return absint( $entry['id'] );
		}, gf_form_entries_field()->backend->get_form_entries( absint( $this->selected_form ) ) );

		if ( !in_array( $selected_entry, $allowed_entries ) )
		{
			// invalid selection
			$this->failed_validation  = true;
			$this->validation_message = __( 'Selected entry not found!', GFEF_DOMAIN );
		}
	}

	public function allow_html()
	{
		return false;
	}
}