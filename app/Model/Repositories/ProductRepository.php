<?php

namespace App\Model\Repositories;

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
      //pokud je zadané požadované ID tagu, najdeme ho v navázané tabulce
      $query->where('category_id IN (SELECT category_id FROM product WHERE category_id=%i)',$categoryId);
    }

    return $this->createEntities($query->fetchAll($offset, $limit));
  }

}