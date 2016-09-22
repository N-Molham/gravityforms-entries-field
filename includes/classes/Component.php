<?php namespace TeamYea\Gravity_Forms\Form_Entries_Field;

/**
 * Base Component
 *
 * @package TeamYea\Gravity_Forms\Form_Entries_Field
 */
class Component extends Singular
{
	/**
	 * Plugin Main Component
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init()
	{
		// vars
		$this->plugin = Plugin::get_instance();
	}
}
