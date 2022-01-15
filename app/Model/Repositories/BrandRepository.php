<?php

namespace App\Model\Repositories;

use App\Model\Entities\Brand;

/**
 * Class BrandRepository - repozitář pro výrobce
 * @package App\Model\Repositories
 */
class BrandRepository extends BaseRepository{

  public function getBrand($id) {
    $row = $this->connection->select('*')
      ->from($this->getTable())
      ->where($this->mapper->getPrimaryKey($this->getTable()) . '= %i', $id)
      ->fetch();

    if (!$row) {
      throw new \Exception('Entity was not found.');
    }
    return $this->createEntity($row);
  }
}