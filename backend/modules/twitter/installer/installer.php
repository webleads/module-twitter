<?php

/**
 * Installer for the twitter module.
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class TwitterInstaller extends ModuleInstaller
{
	/**
	 * Install the module.
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'twitter' as a module
		$this->addModule('twitter', 'The twitter module.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'twitter');

		// action rights
		$this->setActionRights(1, 'twitter', 'add');
		$this->setActionRights(1, 'twitter', 'delete');
		$this->setActionRights(1, 'twitter', 'edit');
		$this->setActionRights(1, 'twitter', 'index');
		$this->setActionRights(1, 'twitter', 'oauth');
		$this->setActionRights(1, 'twitter', 'settings');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$this->setNavigation($navigationModulesId, 'Twitter', 'twitter/index', array('twitter/add', 'twitter/edit'));

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Twitter', 'twitter/settings');
	}
}
