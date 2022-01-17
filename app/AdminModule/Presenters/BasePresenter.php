<?php

namespace App\AdminModule\Presenters;

use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;

/**
 * Class BasePresenter
 * @package App\AdminModule\Presenters
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {

  /**
   * @throws ForbiddenRequestException
   * @throws AbortException
   */
  protected function startup(){
    parent::startup();
    $presenterName = $this->request->presenterName;
    $action = !empty($this->request->parameters['action'])?$this->request->parameters['action']:'';

    if (!$this->user->isAllowed($presenterName,$action)){
      if ($this->user->isLoggedIn()){
        //throw new ForbiddenRequestException();
          $this->flashMessage('Tento obsah je buď pro nepřihlášeného uživatele, nebo k němu nemáte přístup. ','warning');
          $this->redirect(':Front:Homepage:', ['backlink' => $this->storeRequest()]);
      }else{
        $this->flashMessage('Pro zobrazení požadovaného obsahu je nutné se přihlásit!','warning');
        //uložíme původní požadavek - předáme ho do persistentní proměnné v UserPresenteru
        $this->redirect(':Front:User:login', ['backlink' => $this->storeRequest()]);
      }
    }
  }

}