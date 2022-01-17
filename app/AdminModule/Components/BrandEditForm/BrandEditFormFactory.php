<?php

namespace App\AdminModule\Components\BrandEditForm;

/**
 * Interface BrandEditFormFactory
 * @package App\AdminModule\Components\BrandEditForm
 */
interface BrandEditFormFactory{

  public function create():BrandEditForm;

}