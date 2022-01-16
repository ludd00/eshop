<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Series
 * @package App\Model\Entities
 * @property int $seriesId
 * @property string $name
 */
class Series extends Entity implements \Nette\Security\Resource{

  /**
   * @inheritDoc
   */
  function getResourceId():string{
    return 'Series';
  }
}