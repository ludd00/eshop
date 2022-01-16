<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class ProductBrand
 * @package App\Model\Entities
 * @property int $productBrandId
 * @property int $productId
 * @property int $brandId
 */
class ProductBrand extends Entity implements \Nette\Security\Resource{

  /**
   * @inheritDoc
   */
  function getResourceId():string{
    return 'productBrand';
  }
}