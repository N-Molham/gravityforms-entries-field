<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 22-Sep-16
 * Time: 3:13 PM
 */
?>
<li class="gfef_form_setting field_setting">
	<label for="gfef-form" class="section_label">
		<?php esc_html_e( 'Form', 'gravityforms' ); ?>
		<?php gform_tooltip( 'gfef_form' ) ?>
	</label>
	<select id="gfef-form" data-forms="<?php echo esc_attr( json_encode( $forms ) ); ?>"><?php
		foreach ( $forms as $form )
		{
			echo '<option value="', $form['id'], '">', $form['title'], '</option>';
		}
		?></select>
</li>

<li class="gfef_form_field_setting field_setting">
	<label for="gfef-form-field" class="section_label">
		<?php esc_html_e( 'Form Field', 'gravityforms' ); ?>
		<?php gform_tooltip( 'gfef_form_field' ) ?>
	</label>
	<select id="gfef-form-field" class="disabled"></select>
</li>
