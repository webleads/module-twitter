<?php

namespace Frontend\Modules\Twitter\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Model as FrontendModel;

/**
 * In this file we store all generic functions that we will be using in the twitter module.
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class Model
{
    /**
     * Get the recent tweets.
     *
     * @param int $widgetId The widget id.
     * @return array
     */
    public static function getRecentTweets($widgetId)
    {
        // init
        $widgetId = (int)$widgetId;
        $tweets = array();

        // get the widget
        $widget = self::getWidget($widgetId);

        // widget exists
        if (!empty($widget)) {
            // get tweets
            $tweets = (array)FrontendModel::getContainer()->get('database')->getRecords(
                'SELECT tt.text, tt.source, tt.truncated, UNIX_TIMESTAMP(tt.created_on) AS created_on
				 FROM twitter_tweets AS tt
				 WHERE tt.widget_id = ?
				 ORDER BY tt.created_on DESC
				 LIMIT ?',
                array($widget['id'], (int)$widget['number_of_items'])
            );

            // loop tweets
            foreach ($tweets as &$tweet) {
                $tweet['truncated'] = ($tweet['truncated'] == 'Y');
            }
        }

        // return tweets
        return $tweets;
    }

    /**
     * Get the widget.
     *
     * @param int $id The widget id.
     * @return array
     */
    public static function getWidget($id)
    {
        return (array)FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT tw.id, tw.user_id, tw.tag, tw.number_of_items
			 FROM twitter_widgets AS tw
			 WHERE tw.id = ?',
            array((int)$id)
        );
    }
}
