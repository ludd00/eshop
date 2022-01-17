<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class ProductSeries
 * @package App\Model\Entities
 * @property int $productSeriesId
 * @property int $productId
 * @property int $seriesId
 */
class ProductSeries extends Entity implements \Nette\Security\Resource{

  /**
   * @inheritDoc
   */
  function getResourceId():string{
    return 'seriesBrand';
  }
}