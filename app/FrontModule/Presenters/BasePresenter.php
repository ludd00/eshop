<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\CartControl\CartControl;
use App\FrontModule\Components\CartControl\CartControlFactory;
use App\FrontModule\Components\UserLoginControl\UserLoginControl;
use App\FrontModule\Components\UserLoginControl\UserLoginControlFactory;
use App\FrontModule\Components\UserRegisterControl\UserRegisterControl;
use App\FrontModule\Components\UserRegisterControl\UserRegisterControlFactory;
use App\FrontModule\Components\NavbarControl\NavbarControl;
use App\FrontModule\Components\NavbarControl\NavbarControlFactory;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;

/**
 * Class BasePresenter
 * @package App\FrontModule\Presenters
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
  /** @var UserLoginControlFactory $userLoginControlFactory */
  private $userLoginControlFactory;
  /** @var UserRegisterControlFactory $userRegisterControlFactory */
  private $userRegisterControlFactory;
  /** @var CartControlFactory $cartControlFactory*/
  private $cartControlFactory;
  /** @var NavbarControlFactory $navbarControlFactory*/
  private $navbarControlFactory;

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
        throw new ForbiddenRequestException();
      }else{
        $this->flashMessage('Pro zobrazení požadovaného obsahu se musíte přihlásit!','warning');
        //uložíme původní požadavek - předáme ho do persistentní proměnné v UserPresenteru
        $this->redirect('User:login', ['backlink' => $this->storeRequest()]);
      }
    }
  }

  /**
   * Komponenta pro navbar
   * @return NavbarControl
   */
  public function createComponentNavbar():NavbarControl {
    return $this->navbarControlFactory->create();
  }

  /**
   * Komponenta pro zobrazení údajů o aktuálním uživateli (přihlášeném či nepřihlášeném)
   * @return UserLoginControl
   */
  public function createComponentUserLogin():UserLoginControl {
    return $this->userLoginControlFactory->create();
  }

    /**
     * Komponenta pro asi registraci, zkouším všechno, chci to udělat taky jako signál, ale nejde mi to :(
     * @return UserRegisterControl
     */
    public function createComponentUserRegister():UserRegisterControl {
        return $this->userRegisterControlFactory->create();
    }

  /**
   * Komponenta košíku
   * @return CartControl
   */
  public function createComponentCart():CartControl {
    return $this->cartControlFactory->create();
  }

  #region injections

    /**
     * @param UserLoginControlFactory $userLoginControlFactory
     */
    public function injectUserLoginControlFactory(UserLoginControlFactory $userLoginControlFactory):void {
    $this->userLoginControlFactory=$userLoginControlFactory;
  }

    public function injectUserRegisterControlFactory(UserRegisterControlFactory $userRegisterControlFactory):void {
        $this->userRegisterControlFactory=$userRegisterControlFactory;
    }

  public function injectCartControlFactory(CartControlFactory $cartControlFactory):void {
    $this->cartControlFactory=$cartControlFactory;
  }
  public function injectNavbarControlFactory(NavbarControlFactory $navbarControlFactory):void {
    $this->navbarControlFactory=$navbarControlFactory;
  }

  #endregion injections
}