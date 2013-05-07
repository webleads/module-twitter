{*
	variables that are available:
	- {$widgetTwitterRecentTweets}: contains an array with the recent tweets. Each element contains data about the tweet.
*}

{option:widgetTwitterRecentTweets}
	<div class="widget widgetTwitterRecentTweets">
		<h3>{$lblRecentTweets|ucfirst}</h3>
		<ul>
			{iteration:widgetTwitterRecentTweets}
			<li>
				{$widgetTwitterRecentTweets.text}
				<small>{$widgetTwitterRecentTweets.created_on|timeago}</small>
			</li>
			{/iteration:widgetTwitterRecentTweets}
		</ul>
	</div>
{/option:widgetTwitterRecentTweets}