<?php

namespace App\Model\Repositories;


use App\Model\Entities\ProductBrand;

/**
 * Class ProductBrandRepository - repozitář pro hodnoceni produktu
 * @package App\Model\Repositories
 */
class ProductBrandRepository extends BaseRepository{

  /**
   * @param int|null $productId
   * @param int|null $offset
   * @param int|null $limit
   * @return ProductBrand[]
   * @throws \LeanMapper\Exception\InvalidStateException
   */
  public function findProductBrand(?int $productId = null, ?int $offset = null, ?int $limit = null):array{
    $query = $this->connection->select('*')->from($this->getTable());


    if ($productId){
      $query->where('product_id IN (SELECT product_id FROM product_brand WHERE product_id=%i)', $productId);
    }

    //vytvoření entit
    return $this->createEntities($query->fetchAll($offset, $limit));
  }
}