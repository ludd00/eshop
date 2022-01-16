<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class ProductRating
 * @package App\Model\Entities
 * @property int $productRatingId
 * @property User $user m:hasOne
 * @property Product $product m:hasOne
 * @property int $stars
 */
class ProductRating extends Entity implements \Nette\Security\Resource{

  /**
   * @inheritDoc
   */
  function getResourceId():string{
    return 'productRating';
  }
}