<?php

namespace Backend\Modules\Twitter\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Twitter\Engine\Model as BackendTwitterModel;

/**
 * This is the delete-action, it will delete an item.
 *
 * @author Bert Pattyn <bert@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class Delete extends BackendBaseActionDelete
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

            // get widget
            $this->record = BackendTwitterModel::get($this->id);

            // delete item
            BackendTwitterModel::delete($this->id);

            // trigger event
            BackendModel::triggerEvent($this->getModule(), 'after_delete', array('item' => $this->record));

            // user was deleted, so redirect
            $this->redirect(BackendModel::createURLForAction('index') . '&report=deleted');
        } // no item found, redirect to index, because somebody is fucking with our URL
        else {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }
    }
}
