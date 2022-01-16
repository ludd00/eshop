<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Brand
 * @package App\Model\Entities
 * @property int $brandId
 * @property string $name
 */
class Brand extends Entity implements \Nette\Security\Resource{

  /**
   * @inheritDoc
   */
  function getResourceId():string{
    return 'Brand';
  }
}