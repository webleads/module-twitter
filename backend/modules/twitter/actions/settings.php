<?php

/**
 * This is the settings-action, it will display a form to set general twitter settings.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendTwitterSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load form
		$this->loadForm();

		// validates the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Loads the settings form
	 */
	private function loadForm()
	{
		// init settings form
		$this->frm = new BackendForm('settings');

		// add text fields
		$this->frm->addText('consumer_key', BackendModel::getModuleSetting($this->URL->getModule(), 'consumer_key'));
		$this->frm->addText('consumer_secret', BackendModel::getModuleSetting($this->URL->getModule(), 'consumer_secret'));
	}

	/**
	 * Parse
	 */
	protected function parse()
	{
		// parent parse functionality
		parent::parse();

		// get module settings
		$consumerKey = BackendModel::getModuleSetting($this->URL->getModule(), 'consumer_key');
		$consumerSecret = BackendModel::getModuleSetting($this->URL->getModule(), 'consumer_secret');

		// assign error messages
		if($consumerKey == '') $this->tpl->assign('noConsumerKey', true);
		if($consumerSecret == '') $this->tpl->assign('noConsumerSecret', true);
		if($consumerKey == '' || $consumerSecret == '') $this->tpl->assign('noConsumerKeys', true);
	}

	/**
	 * Validates the settings form
	 */
	private function validateForm()
	{
		// form is submitted
		if($this->frm->isSubmitted())
		{
			// shorten fields
			$txtConsumerKey = $this->frm->getField('consumer_key');
			$txtConsumerSecret = $this->frm->getField('consumer_secret');

			// validation
			$txtConsumerKey->isFilled(BL::err('FieldIsRequired'));
			$txtConsumerSecret->isFilled(BL::err('FieldIsRequired'));

			// form is validated
			if($this->frm->isCorrect())
			{
				// set our settings
				BackendModel::setModuleSetting($this->URL->getModule(), 'consumer_key', $txtConsumerKey->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'consumer_secret', $txtConsumerSecret->getValue());

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
