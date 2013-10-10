<?php

/**
 * In this file we store all generic functions that we will be using in the twitter module
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendTwitterModel
{
	/**
	 * Query for the widget overview data grid.
	 *
	 * @var	string
	 */
	const QRY_DATA_GRID_BROWSE =
		'SELECT tw.id, tu.username, tw.tag, tw.number_of_items, tu.oauth_status, UNIX_TIMESTAMP(tw.last_synced_on) AS last_synced_on
		 FROM twitter_widgets AS tw
		 INNER JOIN twitter_users AS tu ON tw.user_id = tu.id';

	/**
	 * Delete a widget.
	 *
	 * @param int $id  The id of the record to delete.
	 */
	public static function delete($id)
	{
		// recast
		$id = (int) $id;

		// get db
		$db = BackendModel::getContainer()->get('database');

		// get widget record
		$item = self::get($id);

		// build extra
		$extra = array('id' => $item['extra_id'],
						'module' => 'twitter',
						'type' => 'widget',
						'action' => 'recent_tweets');

		// delete extra
		$db->delete('modules_extras', 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// delete widget
		$db->delete('twitter_widgets', 'id = ?', $id);

		// delete tweets of widget
		$db->delete('twitter_tweets', 'widget_id = ?', $item['id']);

		// check if widhgets exist
		$widgetsExists = (bool) $db->getVar(
			'SELECT COUNT(id)
			 FROM twitter_widgets
			 WHERE user_id = ?',
			array($item['user_id'])
		);

		// if there are no widgets left for this username, delete the username
		if($widgetsExists)
		{
			$db->delete('twitter_users', 'id = ?', $item['user_id']);
		}
	}

	/**
	 * Does a widget with this id exist.
	 *
	 * @param int $id The id of the widget.
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(*)
			 FROM twitter_widgets
			 WHERE id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get a user based on the id.
	 *
	 * @param int $id User id.
	 * @return array
	 */
	public static function getUser($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT *
			 FROM twitter_users
			 WHERE id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get a user based on the id of a widget.
	 *
	 * @param int $id  The id of the widget.
	 * @return array
	 */
	public static function getUserByWidgetId($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT u.*
			 FROM twitter_users u
			 INNER JOIN twitter_widgets w ON u.id = w.user_id
			 WHERE w.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get a user id based on the username.
	 *
	 * @param string $username Twitter username.
	 * @return int
	 */
	public static function getUserIdByUsername($username)
	{
		return (int) BackendModel::getContainer()->get('database')->getVar(
			'SELECT id
			 FROM twitter_users
			 WHERE username = ?',
			array((string) $username)
		);
	}

	/**
	 * Get all the user ids.
	 *
	 * @return array
	 */
	public static function getUserIds()
	{
		return (array) BackendModel::getContainer()->get('database')->getColumn(
			'SELECT twitter_id
			 FROM twitter_users'
		);
	}

	/**
	 * Get a widget based on the id.
	 *
	 * @param int $id Id of the widget.
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT w.*, u.username
			 FROM twitter_widgets w
			 INNER JOIN twitter_users u ON w.user_id = u.id
			 WHERE w.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get the widgets. If the widgetIds are given, only return this widgets.
	 *
	 * @param mixed[optional] $widgetIds Which widgets to fetch. If omitted fetch all.
	 * @return array
	 */
	public static function getWidgets($widgetIds = null)
	{
		// recast
		$widgetIds = (array) $widgetIds;

		// init query
		$query =
			'SELECT tw.id AS widget_id, tw.tag, tw.number_of_items,
			 	tu.twitter_id, tu.oauth_token, tu.oauth_token_secret, tu.username
			 FROM twitter_widgets AS tw
			 INNER JOIN twitter_users AS tu ON tu.id = tw.user_id';

		// add optional widget id's
		if(!empty($widgetIds))
		{
			// loop and cast to integers
			foreach($widgetIds as &$id) $id = (int) $id;

			// create an array with an equal amount of questionmarks as ids provided
			$idPlaceHolders = array_fill(0, count($widgetIds), '?');

			$query .= ' WHERE tw.id IN (' . implode(',', $idPlaceHolders) . ')';
		}

		// get widgets
		return (array) BackendModel::getContainer()->get('database')->getRecords($query, $widgetIds);
	}

	/**
	 * Insert a new twitter user.
	 *
	 * @param array $item User data.
	 * @return int
	 */
	public static function insertUser(array $item)
	{
		return BackendModel::getContainer()->get('database')->insert('twitter_users', $item);
	}

	/**
	 * Insert a new twitter widget.
	 *
	 * @param array $item Widget data.
	 * @return int
	 */
	public static function insert(array $item, $cache=true)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// get user
		$user = self::getUser($item['user_id']);

		// create widget label
		$extraLabel = ucfirst(BL::getLabel('Twitter')) . ': ' . $user['username'];
		if($item['tag'] !== null) $extraLabel .=  ' - #' . $item['tag'];
		$extraLabel .= ' (' . $item['number_of_items'] . ')';

		// build extra
		$extra = array(
			'module' => 'twitter',
			'type' => 'widget',
			'label' => 'Twitter',
			'action' => 'recent_tweets',
			'data' => null,
			'hidden' => 'N',
			'sequence' => $db->getVar(
				'SELECT MAX(i.sequence) + 1
				 FROM modules_extras AS i
				 WHERE i.module = ?',
				array('twitter')
			)
		);

		if(is_null($extra['sequence'])) $extra['sequence'] = $db->getVar(
			'SELECT CEILING(MAX(i.sequence) / 1000) * 1000
			 FROM modules_extras AS i'
		);

		// insert extra
		$item['extra_id'] = $db->insert('modules_extras', $extra);
		$extra['id'] = $item['extra_id'];

		// insert and return the new revision id
		$item['id'] = $db->insert('twitter_widgets', $item);

		// update extra (item id is now known)
		$extra['data'] = serialize(array(
			'id' => $item['id'],
			'extra_label' => $extraLabel,
			'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id'])
		);
		$db->update(
			'modules_extras',
			$extra,
			'id = ? AND module = ? AND type = ? AND action = ?',
			array($extra['id'], $extra['module'], $extra['type'], $extra['action'])
		);

		if($cache)
		{
			// update twitter statuses cache
			BackendTwitterHelper::updateStatusCache();

			// update twitter users cache
			BackendTwitterHelper::updateUsersCache();
		}

		// return widget id
		return $item['id'];
	}

	/**
	 * Update a user.
	 *
	 * @param int $id Id of the user to update.
	 * @param  array $user User data to update.
	 */
	public static function updateUser($id, array $user)
	{
		BackendModel::getContainer()->get('database')->update('twitter_users', $user, 'id = ?', (int) $id);
	}

	/**
	 * Update a twitter widget.
	 *
	 * @param array $item The data to update.
	 * @return int
	 */
	public static function update(array $item)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// get user
		$user = self::getUser($item['user_id']);

		// create widget label
		$extraLabel = ucfirst(BL::getLabel('Twitter')) . ': ' . $user['username'];
		if($item['tag'] !== null) $extraLabel .=  ' - #' . $item['tag'];
		$extraLabel .= ' (' . $item['number_of_items'] . ')';

		// build extra
		$extra = array(
			'id' => $item['extra_id'],
			'module' => 'twitter',
			'type' => 'widget',
			'label' => 'Twitter',
			'action' => 'recent_tweets',
			'data' => serialize(array(
				'id' => $item['id'],
				'extra_label' => $extraLabel,
				'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id'])
				),
			'hidden' => 'N');

		// update extra
		$db->update('modules_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// update widget
		$db->update('twitter_widgets', $item, 'id = ?', array($item['id']));

		// widget will need to rebuild data as settings may have changed!
		$db->delete('twitter_tweets', 'widget_id = ?', array($item['id']));

		// update twitter statuses cache
		BackendTwitterHelper::updateStatusCache();

		// update twitter users cache
		BackendTwitterHelper::updateUsersCache();

		// return widget id
		return $item['id'];
	}
}
