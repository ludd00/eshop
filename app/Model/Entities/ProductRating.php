<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class ProductRating
 * @package App\Model\Entities
 * @property int $productRatingId
 * @property int $userId
 * @property int $productId
 * @property float $stars
 */
class ProductRating extends Entity implements \Nette\Security\Resource{

  /**
   * @inheritDoc
   */
  function getResourceId():string{
    return 'productRating';
  }
}