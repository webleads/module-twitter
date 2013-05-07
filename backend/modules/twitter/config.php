<?php

/**
 * This is the configuration-object for the twitter module
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
final class BackendTwitterConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';

	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();

	/**
	 * Check if all required settings have been set
	 *
	 * @param string $module The module.
	 */
	public function __construct($module)
	{
		// parent construct
		parent::__construct($module);

		// init
		$error = false;
		$action = Spoon::exists('url') ? Spoon::get('url')->getAction() : null;

		// missing consumer key
		if(BackendModel::getModuleSetting('twitter', 'consumer_key') === null) $error = true;

		// missing consumer secret
		if(BackendModel::getModuleSetting('twitter', 'consumer_secret') === null) $error = true;

		// missing settings, so redirect to the index-page to show a message (except on the index- and settings-page)
		if($error && $action != 'settings') SpoonHTTP::redirect(BackendModel::createURLForAction('settings'));
	}
}
