<?php

namespace Backend\Modules\Twitter;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Config as BackendBaseConfig;
use Backend\Core\Engine\Model as BackendModel;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This is the configuration-object for the twitter module
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
final class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var    string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions
     *
     * @var    array
     */
    protected $disabledActions = array();

    /**
     * Check if all required settings have been set
     *
     * @param string|KernelInterface $kernel
     * @param string $module The module.
     * @throws \Exception
     * @throws \SpoonException
     */
    public function __construct(KernelInterface $kernel, $module)
    {
        // parent construct
        parent::__construct($kernel, $module);

        // init
        $error = false;

        $action = $this->getContainer()->has('url') ? $this->getContainer()->get('url')->getAction() : null;

        // missing consumer key
        if (BackendModel::getModuleSetting('Twitter', 'consumer_key') === null) {
            $error = true;
        }

        // missing consumer secret
        if (BackendModel::getModuleSetting('Twitter', 'consumer_secret') === null) {
            $error = true;
        }

        // missing settings, so redirect to the index-page to show a message (except on the index- and settings-page)
        if ($error && $action != 'Settings') {
            \SpoonHTTP::redirect(BackendModel::createURLForAction('Settings'));
        }
    }
}
