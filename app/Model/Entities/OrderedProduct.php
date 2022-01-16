<?php


namespace App\Model\Entities;


use LeanMapper\Entity;

/**
 * Class OrderedProduct
 * @package App\Model\Entities
 * @property int $orderedProductId
 * @property ShopOrder $shopOrder m:hasOne
 * @property Product $product m:hasOne
 * @property int $unitPrice
 * @property int $count
 */
class OrderedProduct extends Entity{

}