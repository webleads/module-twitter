<?php

namespace Backend\Modules\Twitter\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the twitter module.
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class Installer extends ModuleInstaller
{
    /**
     * Install the module.
     */
    public function install()
    {
        // load install.sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add 'twitter' as a module
        $this->addModule('Twitter', 'The twitter module.');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // module rights
        $this->setModuleRights(1, 'Twitter');

        // action rights
        $this->setActionRights(1, 'Twitter', 'Add');
        $this->setActionRights(1, 'Twitter', 'Delete');
        $this->setActionRights(1, 'Twitter', 'Edit');
        $this->setActionRights(1, 'Twitter', 'Index');
        $this->setActionRights(1, 'Twitter', 'OAuth');
        $this->setActionRights(1, 'Twitter', 'Settings');

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $this->setNavigation($navigationModulesId, 'Twitter', 'twitter/index', array('twitter/add', 'twitter/edit'));

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Twitter', 'twitter/settings');
    }
}
