<?php

namespace App\FrontModule\Components\NavbarControl;

/**
 * Interface NavbarControlFactory
 * @package App\FrontModule\Components\NavbarControl
 */
interface NavbarControlFactory{

  public function create():NavbarControl;

}