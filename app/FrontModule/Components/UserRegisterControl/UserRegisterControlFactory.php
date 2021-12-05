<?php

namespace App\FrontModule\Components\UserRegisterControl;

/**
 * Interface UserLoginControlFactory
 * @package App\FrontModule\Components\UserLoginControl
 */
interface UserRegisterControlFactory{

  public function create():UserRegisterControl;

}