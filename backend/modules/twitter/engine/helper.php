<?php

/**
 * In this file we store all generic functions that we will be using in the twitter module.
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendTwitterHelper
{
	/**
	 * Twitter object.
	 *
	 * @var	Twitter
	 */
	private static $twitter;

	/**
	 * Clear all the tweets for all widgets in our cache (or only the given ones).
	 *
	 * @param  mixed[optional] $widgetIds Widget ids to clear. If omitted, clear all.
	 * @return array
	 */
	public static function clearStatusCache($widgetIds = null)
	{
		// recast
		$widgetIds = (array) $widgetIds;

		// loop widgets
		foreach($widgetIds as $id)
		{
			// clear the statuses cache
			BackendModel::getContainer()->get('database')->delete('twitter_tweets', 'widget_id = ?', $id);
		}
	}

	/**
	 * Get the twitter object. If it does not exists yet it will be created.
	 *
	 * @return Twitter
	 */
	public static function getTwitterInstance()
	{
		// first time
		if(!isset(self::$twitter))
		{
			// lazy load the twitter external class
			require_once PATH_WWW . '/library/external/twitter.php';

			// get the consumer key and secret
			$consumerKey = BackendModel::getModuleSetting('twitter', 'consumer_key');
			$consumerSecret = BackendModel::getModuleSetting('twitter', 'consumer_secret');

			// create Twitter object
			self::$twitter = new Twitter($consumerKey, $consumerSecret);
		}

		// cough instance up
		return self::$twitter;
	}

	/**
	 * Update all the twitter widgets in our cache (or only the given ones).
	 *
	 * @param  mixed[optional] $widgetIds Which widgets to update. If omitted, update all.
	 */
	public static function updateStatusCache($widgetIds = null)
	{
		// get the widgets
		$widgets = BackendTwitterModel::getWidgets($widgetIds);

		// loop widgets
		foreach($widgets as $widget)
		{
			// I need my tokens or I will go banana
			if($widget['oauth_token'] !== null && $widget['oauth_token_secret'] !== null)
			{
				// set token en token_secret
				BackendTwitterHelper::getTwitterInstance()->setOAuthToken($widget['oauth_token']);
				BackendTwitterHelper::getTwitterInstance()->setOAuthTokenSecret($widget['oauth_token_secret']);

				// number of tweets to fetch
				$numberOfItems = ($widget['tag'] !== '') ? 200 : $widget['number_of_items'] + 10;

				// try and fetch statuses
				try
				{
					// get the tweets
					$statuses = BackendTwitterHelper::getTwitterInstance()->statusesUserTimeline($widget['twitter_id'], null, null, null, $numberOfItems, null, null, true);
				}

				// gracefully handle exceptions
				catch(Exception $e)
				{
					// debug information
					if(SPOON_DEBUG) Spoon::dump($e);

					// problem to get this statuses, go to next widget
					continue;
				}

				// loop tweets
				foreach($statuses as $status)
				{
					// filter by tag
					if($widget['tag'] !== null && !stripos($status['text'], '#' . $widget['tag'])) continue;

					// update in our cache
					self::updateTweet($widget['widget_id'], $status);
				}

				// cleanup cache so we only have the requested amount of tweets left
				$cleanupDate = (string) BackendModel::getContainer()->get('database')->getVar(
					'SELECT created_on
					 FROM twitter_tweets
					 WHERE widget_id = ?
					 ORDER BY created_on DESC
					 LIMIT ?, 1',
					array($widget['widget_id'], (int) $widget['number_of_items'])
				);

				// remove tweets to are to old
				if($cleanupDate !== '')
				{
					BackendModel::getContainer()->get('database')->delete(
						'twitter_tweets',
						'widget_id = ? AND created_on <= ?',
						array($widget['widget_id'], $cleanupDate)
					);
				}

				// update 'last synced on'
				BackendModel::getContainer()->get('database')->update('twitter_widgets', array('last_synced_on' => BackendModel::getUTCDate()), 'id = ?', $widget['widget_id']);
			}
		}
	}

	/**
	 * Update a tweet in our cache.
	 *
	 * @param int $widgetId The widget id.
	 * @param array $status The data received from twitter.
	 * @return array
	 */
	private static function updateTweet($widgetId, array $status)
	{
		// tweet data
		$record['tweet_id'] = (string) $status['id'];
		$record['widget_id'] = (int) $widgetId;
		$record['text'] = (string) $status['text'];
		$record['source'] = str_replace("&quot;", '"', SpoonFilter::htmlentitiesDecode($status['source']));
		$record['truncated'] = isset($status['truncated']) ? 'Y' : 'N';
		$record['in_reply_to_status_id'] = isset($status['in_reply_to_status_id']) ? $status['in_reply_to_status_id'] : null;
		$record['in_reply_to_user_id'] = isset($status['in_reply_to_user_id']) ? $status['in_reply_to_user_id'] : null;
		$record['in_reply_to_screen_name'] = isset($status['in_reply_to_screen_name']) ? $status['in_reply_to_screen_name'] : null;
		$record['created_on'] = BackendModel::getUTCDate(null, strtotime($status['created_at']));

		// insert or update
		BackendModel::getContainer()->get('database')->execute(
			'INSERT INTO twitter_tweets
			 (
			 	tweet_id,
			 	widget_id,
			 	text,
			 	source,
			 	truncated,
			 	in_reply_to_status_id,
			 	in_reply_to_user_id,
			 	in_reply_to_screen_name,
			 	created_on
		 	 )
			 VALUES
			 (
			 	:tweetId,
			 	:widgetId,
			 	:text,
			 	:source,
			 	:truncated,
			 	:inReplyToStatusId,
			 	:inReplyToUserId,
			 	:inReplyToScreenName,
			 	:createdOn
		 	 )
			 ON DUPLICATE KEY UPDATE
			 	text = :text,
			 	source = :source,
			 	truncated = :truncated,
			 	in_reply_to_status_id = :inReplyToStatusId,
			 	in_reply_to_user_id = :inReplyToUserId,
			 	in_reply_to_screen_name = :inReplyToScreenName,
			 	created_on = :createdOn',
			array(
				'tweetId' => $record['tweet_id'],
				'widgetId' => $record['widget_id'],
				'text' => $record['text'],
				'source' => $record['source'],
				'truncated' => $record['truncated'],
				'inReplyToStatusId' => $record['in_reply_to_status_id'],
				'inReplyToUserId' => $record['in_reply_to_user_id'],
				'inReplyToScreenName' => $record['in_reply_to_screen_name'],
				'createdOn' => $record['created_on'],
			)
		);

		// return tweet id
		return $record['tweet_id'];
	}

	/**
	 * Update the users information in our cache.
	 *
	 * @param mixed[optional] $userIds An array of user ids. If omitted update all.
	 * @return array
	 */
	public static function updateUsersCache($userIds = null)
	{
		// recast
		$userIds = (array) $userIds;

		// no users ids set, so fetch all
		if(empty($userIds)) $userIds = BackendTwitterModel::getUserIds();

		// init
		$users = array();

		// users found so go like a ninja
		if(!empty($userIds))
		{
			// try and gently ask twitter for some user info
			try
			{
				// get user info
				$users = (array) BackendTwitterHelper::getTwitterInstance()->usersLookup($userIds);
			}

			// problem while getting the users information
			catch(TwitterException $e)
			{
				// debug information
				if(SPOON_DEBUG) Spoon::dump($e);

				// nothing found, so sad :(
				return array();
			}

			// loop users
			foreach($users as $user)
			{
				// user data
				$record['name'] = (string) $user['name'];
				$record['location'] = (string) $user['location'];
				$record['description'] = (string) $user['description'];
				$record['profile_image_url'] = (string) $user['profile_image_url'];
				$record['url'] = (string) $user['url'];

				// update user information
				BackendModel::getContainer()->get('database')->update(
					'twitter_users',
					$record,
					'twitter_id = ?',
					array($user['id'])
				);
			}
		}

		// return twitter data
		return $users;
	}
}
