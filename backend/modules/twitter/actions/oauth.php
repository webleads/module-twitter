<?php

/**
 * This is the action used to oAuth a Twitter user.
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendTwitterOauth extends BackendBaseAction
{
	/**
	 * The id of the widget.
	 *
	 * @var int
	 */
	private $id;

	/**
	 * The twitter user.
	 *
	 * @var mixed
	 */
	private $user;

	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if($this->id !== null && BackendTwitterModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get user
			$this->user = BackendTwitterModel::getUserByWidgetId($this->id);

			// already authenticated, all ok
			if($this->user['oauth_status'] == 'approved') $this->redirect(BackendModel::createURLForAction('index'));

			// get the oauthtokens if there is one (if you are redirected here from the twitter authentication)
			$oAuthToken = $this->getParameter('oauth_token', 'string', '');
			$oAuthVerifier = $this->getParameter('oauth_verifier', 'string', '');

			// first visit (otherwise twitter would have added these 2 parameters)
			try
			{
				if($oAuthToken == '' || $oAuthVerifier == '')
				{
					// request Request token with callback url
					BackendTwitterHelper::getTwitterInstance()->oAuthRequestToken(SITE_URL . BackendModel::createURLForAction('oauth', null, null, array('id' => $this->id)));

					// authorize
					BackendTwitterHelper::getTwitterInstance()->oAuthAuthorize();
				}

				// oauth tokens set (meaning we got redirected back from twitter)
				else
				{
					// exchanging the request token for an access token
					$response = BackendTwitterHelper::getTwitterInstance()->oAuthAccessToken($oAuthToken, $oAuthVerifier);

					// got an access token, yAy!
					if(isset($response['oauth_token']) && isset($response['oauth_token_secret']))
					{
						// create user array
						$item['oauth_status'] = 'approved';
						$item['oauth_token'] = $response['oauth_token'];
						$item['oauth_token_secret'] = $response['oauth_token_secret'];
						$item['twitter_id'] = $response['user_id'];
						$item['name'] = $response['screen_name'];

						// update the user
						BackendTwitterModel::updateUser($this->user['id'], $item);

						// clear the twitter cache, next cronjob run will fill it up again
						BackendTwitterHelper::clearStatusCache($this->id);

						// trigger event
						BackendModel::triggerEvent($this->getModule(), 'after_oauth', array('item' => $item));

						// successfully authenticated
						$this->redirect(BackendModel::createURLForAction('index') . '&report=authentication_success&var=' . urlencode($this->user['username']) . '&highlight=row-' . $this->id);
					}

					// access token not received
					else throw new BackendException('Access token not received');
				}
			}

			// something went wrong while authenticating the user
			catch(Exception $e)
			{
				$this->redirect(BackendModel::createURLForAction('index') . '&error=authentication_failed&var=' . urlencode($this->user['username']));
			}
		}

		// no item found, redirect to index, because somebody is fucking with our URL
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
