<?php

namespace App\FrontModule\Components\ShopOrderCreateForm;

/**
 * Interface ShopOrderCreateFormFactory
 * @package App\FrontModule\Components\ShopOrderCreateForm
 */
interface ShopOrderCreateFormFactory{

  public function create():ShopOrderCreateForm;

}