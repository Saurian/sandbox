<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    protected function startup()
    {
        parent::startup();

        $user = $this->getUser();

        if (!$user->isAllowed($this->name, $this->action)) {
            //$this->flashMessage($message, 'warning');
            $this->getUser()->logout();
            $this->redirect('User:login', array('backlink' => $this->storeRequest()));
        }
    }


    /**
     * simple ajax redirect
     *
     * @param string $uri
     * @throws Nette\Application\AbortException
     */
    public function ajaxRedirect(string $uri = 'this')
    {
        if ($this->isAjax())
            $this->redrawControl();

        else
            $this->redirect($uri);

    }

}
