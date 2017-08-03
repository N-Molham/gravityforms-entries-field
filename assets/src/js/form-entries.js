/**
 * Created by Nabeel on 2017-08-03.
 */
(function ( $, win, doc, undefined ) {
	$( doc ).on( 'gform_post_render', function ( e, form_id ) {
		$( '#gform_' + form_id ).find( '.gfield_select-autocomplete' ).each( function ( index, element ) {
			$( element ).select2( {
				minimumInputLength: 3,
				ajax              : {
					url           : gfef_params.ajax_url,
					dataType      : 'json',
					delay         : 250,
					data          : function ( params ) {
						gfef_params.ajax_params.search = params.term;
						gfef_params.ajax_params.page   = params.page;
						gfef_params.ajax_params.field  = element.id;

						return gfef_params.ajax_params;
					},
					processResults: function ( response, params ) {
						params.page = params.page || 1;

						if ( false === response.success ) {
							return {
								results   : [],
								pagination: {
									more: params.page
								}
							};
						}

						return {
							results   : response.data,
							pagination: {
								more: (params.page * 10) < response.data.length
							}
						};
					},
					cache         : true
				}
			} );
		} );
	} );
})( jQuery, window, document );