<?php

/**
 * This is the configuration-object.
 *
 * @author Bert Pattyn <bert@netlash.com>
 */
final class FrontendTwitterConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';

	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();
}
