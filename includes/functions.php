<?php
/**
 * Created by Nabeel
 * Date: 2016-01-22
 * Time: 2:38 AM
 *
 * @package TeamYea\Gravity_Forms\Form_Entries_Field
 */

use TeamYea\Gravity_Forms\Form_Entries_Field\Plugin;

if ( !function_exists( 'gf_form_entries_field' ) ):
	/**
	 * Get plugin instance
	 *
	 * @return Plugin
	 */
	function gf_form_entries_field()
	{
		return Plugin::get_instance();
	}
endif;

if ( !function_exists( 'gfef_view' ) ):
	/**
	 * Load view
	 *
	 * @param string  $view_name
	 * @param array   $args
	 * @param boolean $return
	 *
	 * @return void
	 */
	function gfef_view( $view_name, $args = null, $return = false )
	{
		if ( $return )
		{
			// start buffer
			ob_start();
		}

		gf_form_entries_field()->load_view( $view_name, $args );

		if ( $return )
		{
			// get buffer flush
			return ob_get_clean();
		}
	}
endif;

if ( !function_exists( 'gfef_version' ) ):
	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	function gfef_version()
	{
		return gf_form_entries_field()->version;
	}
endif;