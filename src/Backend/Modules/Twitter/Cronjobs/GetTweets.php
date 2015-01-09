<?php

namespace Backend\Modules\Twitter\Cronjobs;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Cronjob as BackendBaseCronjob;
use Backend\Modules\Twitter\Engine\Helper as BackendTwitterHelper;


/**
 * This is the cronjob to get the tweets and cache them in the database
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class GetTweets extends BackendBaseCronjob
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // set busy file
        $this->setBusyFile();

        // update twitter statuses cache
        BackendTwitterHelper::updateStatusCache();

        // update twitter users cache
        BackendTwitterHelper::updateUsersCache();

        // remove busy file
        $this->clearBusyFile();
    }
}
