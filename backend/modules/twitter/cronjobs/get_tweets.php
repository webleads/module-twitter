<?php

/**
 * This is the cronjob to get the tweets and cache them in the database
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendTwitterCronjobGetTweets extends BackendBaseCronjob
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
