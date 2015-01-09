<?php

namespace Backend\Modules\Twitter\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Twitter\Engine\Model as BackendTwitterModel;


/**
 * This is the edit-action, it will display a form to edit an existing widget
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // get parameters
        $this->id = $this->getParameter('id', 'int');

        // does the item exists
        if ($this->id !== null && BackendTwitterModel::exists($this->id)) {
            // call parent, this will probably add some general CSS/JS or other required files
            parent::execute();

            // get the data
            $this->getData();

            // load the form
            $this->loadForm();

            // validate the form
            $this->validateForm();

            // parse the datagrid
            $this->parse();

            // display the page
            $this->display();
        } // no item found, redirect to index, because somebody is fucking with our URL
        else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }

    /**
     * Get the data.
     */
    private function getData()
    {
        $this->record = BackendTwitterModel::get($this->id);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('edit');

        // create elements
        $this->frm->addText('username', $this->record['username']);
        $this->frm->addText('tag', $this->record['tag']);
        $this->frm->addDropdown('number_of_items', array_combine(range(1, 30), range(1, 30)),
            $this->record['number_of_items']);

    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        // call parent
        parent::parse();

        // assign fields
        $this->tpl->assign('item', $this->record);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        // is the form submitted?
        if ($this->frm->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // shorten fields
            $txtUsername = $this->frm->getField('username');
            $txtTag = $this->frm->getField('tag');
            $ddmNumberOfItems = $this->frm->getField('number_of_items');

            // username is required
            $txtUsername->isFilled(BL::getError('UsernameIsRequired'));

            // doublecheck for valid number
            if ($ddmNumberOfItems->isFilled(BL::getError('NumberOfItemsIsRequired'))) {
                if (!\SpoonFilter::isMaximum(200, $ddmNumberOfItems->getValue())) {
                    $ddmNumberOfItems->addError(BL::getError('MaximumTweetsExceeded'));
                }
                if (!\SpoonFilter::isMinimum(1, $ddmNumberOfItems->getValue())) {
                    $ddmNumberOfItems->addError(BL::getError('MinimumTweetsExceeded'));
                }
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // init
                $mustAuthenticate = false;

                // get user
                $userId = BackendTwitterModel::getUserIdByUsername($txtUsername->getValue());

                // does not exist yet so add
                if ($userId === 0) {
                    // create
                    $userId = BackendTwitterModel::insertUser(array('username' => $txtUsername->getValue()));

                    // set oauth flag
                    $mustAuthenticate = true;
                }

                // item data
                $item['id'] = $this->id;
                $item['extra_id'] = $this->record['extra_id'];
                $item['user_id'] = $userId;
                $item['tag'] = ($txtTag->isFilled()) ? str_ireplace('#', '', $txtTag->getValue()) : null;
                $item['number_of_items'] = $ddmNumberOfItems->getValue();

                // update the widget
                $userId = BackendTwitterModel::update($item);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $item));

                // we need to authenticate, redirect to oAuth
                if ($mustAuthenticate) {
                    $this->redirect(BackendModel::createURLForAction('Oauth') . '&id=' . $item['id']);
                } // no need for authentication,  go to index
                else {
                    $this->redirect(BackendModel::createURLForAction('Index') . '&report=edited&highlight=row-' . $item['id']);
                }
            }
        }
    }
}
