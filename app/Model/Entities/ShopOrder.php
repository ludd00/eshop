<?php

namespace App\Model\Entities;

use Dibi\DateTime;
use LeanMapper\Entity;

/**
 * Class ShopOrder
 * @package App\Model\Entities
 * @property int $shopOrderId
 * @property User $user m:hasOne
 * @property string $address
 * @property string|null $billingAddress
 * @property string $status
 * @property bool $paid
 * @property DateTime|null $dateSent
 * @property DateTime|null $dateDelivered
 * @property DateTime $lastChanged
 * @property int|null $price
 * @property string|null $customerNote
 * @property string|null $adminNote
 * @property OrderedProduct[] $orderedProducts m:belongsToMany
 */
class ShopOrder extends Entity{

  /*public function updateCartItems(){
    $this->row->cleanReferencingRowsCache('cart_item');//smažeme cache, aby se položky v košíku znovu načetly z DB bez nutnosti načtení celého košíku
  }

  public function getTotalCount():int {
    $result = 0;
    if (!empty($this->items)){
      foreach ($this->items as $item){
        $result+=$item->count;
      }
    }
    return $result;
  }

  public function getTotalPrice():float {
    $result=0;
    if (!empty($this->items)){
      foreach ($this->items as $item){
        $result+=$item->product->price*$item->count;
      }
    }
    return $result;
  }*/

}