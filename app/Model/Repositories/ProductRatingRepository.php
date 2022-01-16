<?php

namespace App\Model\Repositories;


use App\Model\Entities\ProductRating;

/**
 * Class ProductRatingRepository - repozitář pro hodnoceni produktu
 * @package App\Model\Repositories
 */
class ProductRatingRepository extends BaseRepository{

  /**
   * @param int|null $productId
   * @param int|null $offset
   * @param int|null $limit
   * @return ProductRating[]
   * @throws \LeanMapper\Exception\InvalidStateException
   */
  public function findProductRatings(?int $productId = null, ?int $offset = null, ?int $limit = null):array{
    $query = $this->connection->select('*')->from($this->getTable());


    if ($productId){
      $query->where('product_id IN (SELECT product_id FROM product_rating WHERE product_id=%i)', $productId);
    }

    //vytvoření entit
    return $this->createEntities($query->fetchAll($offset, $limit));
  }
}