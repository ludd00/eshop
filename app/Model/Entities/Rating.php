<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Rating
 * @package App\Model\Entities
 * @property int $ratingId
 * @property int $productId
 * @property float|null $avgStars
 */
class Rating extends Entity implements \Nette\Security\Resource{

  /**
   * @inheritDoc
   */
  function getResourceId():string{
    return 'rating';
  }
}