<?php

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendTwitterAdd extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the form
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addText('username');
		$this->frm->addText('tag');
		$this->frm->addDropdown('number_of_items', array_combine(range(1, 30), range(1, 30)), 5);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// shorten fields
			$txtUsername = $this->frm->getField('username');
			$txtTag = $this->frm->getField('tag');
			$ddmNumberOfItems = $this->frm->getField('number_of_items');

			// username is required
			$txtUsername->isFilled(BL::getError('UsernameIsRequired'));

			// doublecheck for valid number
			if($ddmNumberOfItems->isFilled(BL::getError('NumberOfItemsIsRequired')))
			{
				if(!SpoonFilter::isMaximum(200, $ddmNumberOfItems->getValue())) $ddmNumberOfItems->addError(BL::getError('MaximumTweetsExceeded'));
				if(!SpoonFilter::isMinimum(1, $ddmNumberOfItems->getValue())) $ddmNumberOfItems->addError(BL::getError('MinimumTweetsExceeded'));
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// init
				$mustAuthenticate = false;

				// get user
				$userId = BackendTwitterModel::getUserIdByUsername($txtUsername->getValue());

				// does not exist yet so add
				if($userId === 0)
				{
					// create user
					$userId = BackendTwitterModel::insertUser(array('username' => $txtUsername->getValue()));

					// set oauth flag
					$mustAuthenticate = true;
				}

				// item data
				$item['user_id'] = $userId;
				$item['tag'] = ($txtTag->isFilled()) ? str_ireplace('#', '', $txtTag->getValue()) : null;
				$item['number_of_items'] = $ddmNumberOfItems->getValue();

				// insert the item
				$widgetId = BackendTwitterModel::insert($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));

				// we need to authenticate, redirect to oAuth
				if($mustAuthenticate) $this->redirect(BackendModel::createURLForAction('oauth') . '&id=' . $widgetId);

				// no need for authentication,  go to index
				else $this->redirect(BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $widgetId);
			}
		}
	}
}
