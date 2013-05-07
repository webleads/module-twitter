<?php

/**
 * This is the index-action (default), it will display the overview of twitter widgets
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendTwitterIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load data grid
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Loads the data grid with all the widgets.
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendTwitterModel::QRY_DATA_GRID_BROWSE);

		// sorting columns
		$this->datagrid->setSortingColumns(array('oauth_status', 'username', 'tag', 'number_of_items', 'last_synced_on'), 'oauth_status');
		$this->datagrid->setSortParameter('desc');

		// set colum URLs
		$this->datagrid->setColumnURL('username', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// icons
		$this->datagrid->setTooltip('oauth_status', 'OauthStatusTooltip');

		// parse a nice connection status message
		$this->datagrid->setColumnFunction(array('BackendTwitterIndex', 'setConnectionStatus'), array('[oauth_status]', '[id]'), 'oauth_status', true);

		// parse date
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[last_synced_on]'), 'last_synced_on', true);

		// add edit column
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::getLabel('Edit'));
	}

	/**
	 * Parse all datagrids
	 */
	protected function parse()
	{
		// parse the datagrid for all blogposts
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}

	/**
	 * Parse the twitter oAuth status to a human message.
	 *
	 * @param string $status Posibilities: approved, not_approved.
	 * @param string $widgetId The id of the widget.
	 * @return string
	 */
	public static function setConnectionStatus($status, $widgetId)
	{
		// approved
		if($status == 'approved') return BL::getLabel('Approved');

		// not yet approved
		else return BL::getLabel('NotApproved') . ' (<a href="' . BackendModel::createURLForAction('oauth', null, null, array('id' => $widgetId)) . '">' . BL::getLabel('ToApprove') . '</a>)';
	}
}
