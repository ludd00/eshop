<?php

namespace App\Model\Repositories;

use App\Model\Entities\Product;

/**
 * Class ProductRepository
 * @package App\Model\Repositories
 */
class ProductRepository extends BaseRepository{

  /**
   * Metoda pro vyhledání produktů podle kategorie
   * @param int $categoryId = null
   * @param int $offset = null
   * @param int $limit = null
   * @return array
   */
  public function findByCategory($categoryId=null,int $offset=null,int $limit=null):array {
    $query = $this->connection->select('*')->from($this->getTable());

    if ($categoryId){
      $query->where('category_id=%i', $categoryId);
    }

    return $this->createEntities($query->fetchAll($offset, $limit));
  }

  /**
   * @param int|null $brandId
   * @param int|null $offset
   * @param int|null $limit
   * @return Product[]
   */
  public function findAllByBrand(?int $brandId = null, ?int $offset = null, ?int $limit = null):array{
    $query = $this->connection->select('*')->from($this->getTable());

    #region filtrovani podle vyrobce
    if ($brandId){
      $query->where('product_id IN (SELECT product_id FROM product_brand WHERE brand_id=%i)', $brandId);
    }
    #endregion

    //vytvoření entit
    return $this->createEntities($query->fetchAll($offset, $limit));
  }

}