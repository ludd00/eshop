<?php

namespace App\Model\Entities;

use LeanMapper\Entity;

/**
 * Class Product
 * @package App\Model\Entities
 * @property int $productId
 * @property string $title
 * @property string $url
 * @property string $description
 * @property float $price
 * @property string $photoExtension = ''
 * @property bool $available = true
 * @property Category|null $category m:hasOne
 * @property int|null $brandId
 * @property int|null $seriesId
 * 
 */
class Product extends Entity implements \Nette\Security\Resource{

    /**
     * @inheritDoc
     */
    function getResourceId():string{
        return 'Product';
    }
}