<?php

namespace App\Model\Repositories;

use App\Model\Entities\Rating;

/**
 * Class RatingRepository - repozitář pro hodnoceni
 * @package App\Model\Repositories
 */
class RatingRepository extends BaseRepository
{

  /**
   * @param $id
   * @return mixed
   * @throws \LeanMapper\Exception\InvalidStateException
   */
  public function getRating($id) {
    $row = $this->connection->select('*')
      ->from($this->getTable())
      ->where('product_id IN (SELECT product_id FROM rating WHERE product_id=%i)', $id)
      ->fetch();

    if (!$row) {
      throw new \Exception('Entity was not found.');
    }
    return $this->createEntity($row);
  }


}


