<?php

namespace App\Model\Repositories;

use App\Model\Entities\Product;

/**
 * Class ProductRepository
 * @package App\Model\Repositories
 */
class ProductRepository extends BaseRepository{

  /**
   * @param int|null $categoryId
   * @param int|null $brandId
   * @param int|null $offset
   * @param int|null $limit
   * @return Product[]
   * @throws \LeanMapper\Exception\InvalidStateException
   */
  public function findAllByCategoryAndBrand(?int $categoryId = null,?int $brandId = null, ?int $offset = null, ?int $limit = null):array{
    $query = $this->connection->select('*')->from($this->getTable());

    #region filtrovani podle kategorie
    if ($categoryId){
      $query->where('category_id=%i', $categoryId);
    }
    #endregion

    #region filtrovani podle vyrobce
    if ($brandId){
      $query->where('product_id IN (SELECT product_id FROM product_brand WHERE brand_id=%i)', $brandId);
    }
    #endregion

    //vytvoření entit
    return $this->createEntities($query->fetchAll($offset, $limit));
  }

  /**
   * @param int|null $seriesId
   * @param int|null $offset
   * @param int|null $limit
   * @return Product[]
   * @throws \LeanMapper\Exception\InvalidStateException
   */
  public function findSeries(?int $productId = null, ?int $offset = null, ?int $limit = null):array{
    $query = $this->connection->select('*')->from($this->getTable());


    #region filtrovani podle serie
    if ($productId){
      $query->where('product_id IN (SELECT product_id FROM product_series WHERE series_id IN (SELECT series_id FROM product_series WHERE product_id=%i))', $productId)
            ->and('product_id NOT IN (SELECT product_id FROM product_series WHERE product_id=%i)', $productId);
    }
    #endregion

    //vytvoření entit
    return $this->createEntities($query->fetchAll($offset, $limit));
  }


}