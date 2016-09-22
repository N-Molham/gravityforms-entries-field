/**
 * Created by Nabeel on 2016-02-02.
 */
(function ( $, win, doc, undefined ) {
	$( function () {
		var $form_fields = $( '#gform_fields' ),
		    forms        = null,
		    forms_length = 0;

		// when form field changes
		$form_fields.on( 'change gfef-change', '.gfef_form_setting select', function ( e, reload_only ) {
			var $this = $( this );
			if ( null === forms ) {
				// generated forms
				forms        = $this.data( 'forms' );
				forms_length = forms.length;
			}

			reload_only = reload_only || false;
			if ( reload_only ) {
				$this.val( $this.attr( 'data-value' ) );
			}

			// vars
			var selected_form = null,
			    form_id       = parseInt( $this.val() );

			// fetch which form is selected
			for ( var i = 0; i < forms_length; i++ ) {
				if ( form_id === parseInt( forms[ i ].id ) ) {
					selected_form = forms[ i ];
					break;
				}
			}

			if ( null === selected_form ) {
				// skip if the form wan't not found
				return true;
			}

			if ( false === reload_only ) {
				// save value
				SetFieldProperty( 'selected_form', form_id );
			}

			// query linked fields dropdown
			var $fields_dropdown = $this.closest( 'ul' ).find( '.gfef_form_field_setting select' ).html( (function ( form ) {
				return function () {
					var options = [];
					for ( var i = 0; i < form.fields.length; i++ ) {
						options.push( '<option value="' + form.fields[ i ].id + '">' + form.fields[ i ].label + '</option>' );
					}
					return options.join( '' );
				};
			})( selected_form ) ).removeClass( 'disabled' );

			if ( reload_only ) {
				$fields_dropdown.val( $fields_dropdown.attr( 'data-value' ) );
			}
		} );

		// when form field changes
		$form_fields.on( 'change gfef-change', '.gfef_form_field_setting select', function ( e ) {
			// save value
			SetFieldProperty( 'selected_field', e.currentTarget.value );
		} );

		// when field is opened
		$( doc ).on( 'gform_load_field_settings', function ( e, field ) {
			if ( 'form_entries' === field.type ) {
				// trigger change event
				setTimeout( function () {
					var $field = $( '#field_' + field.id );
					$field.find( '.gfef_form_field_setting select' ).attr( 'data-value', field.selected_field );
					$field.find( '.gfef_form_setting select' ).attr( 'data-value', field.selected_form ).trigger( 'gfef-change', [ true ] );
				}, 10 );
			}
		} );
	} );
})( jQuery, window, document );