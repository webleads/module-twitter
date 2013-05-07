<?php

/**
 * This is a widget with recent tweets.
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class FrontendTwitterWidgetRecentTweets extends FrontendBaseWidget
{
	/**
	 * The tweets
	 */
	private $tweets;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// load the data
		$this->loadData();

		// load template
		$this->loadTemplate();

		// parse
		$this->parse();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->tweets = FrontendTwitterModel::getRecentTweets((int) $this->data['id']);
	}

	/**
	 * Parse
	 */
	private function parse()
	{
		// clean up tweets
		foreach($this->tweets as &$tweet)
		{
			// make links clickable
			$tweet['text'] = preg_replace('/((http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:\/~\+#]*[\w\-\@?^=%&amp;\/~\+#])?)/i', '<a href="$1" rel="nofollow">$1</a>', $tweet['text']);

			// make twitter usernames clickable
			$tweet['text'] = preg_replace('|@([!\w]*)|i', '<a href="http://twitter.com/$1" rel="nofollow">@$1</a>', $tweet['text']);

			// make twitter tags clickable
			$tweet['text'] = preg_replace('|#([!\w]*)|i', '<a href="http://twitter.com/#search?q=%23$1" rel="nofollow">#$1</a>', $tweet['text']);
		}

		// assign tweets
		$this->tpl->assign('widgetTwitterRecentTweets', $this->tweets);
	}
}
