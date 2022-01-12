<?php

namespace App\Model\Repositories;

/**
 * Class RoleRepository
 * @package App\Model\Repositories
 */
class RoleRepository extends BaseRepository{

  public function getRole($id) {
    $row = $this->connection->select('*')
      ->from($this->getTable())
      ->where($this->mapper->getPrimaryKey($this->getTable()) . '= %s', $id)
      ->fetch();

    if (!$row) {
      throw new \Exception('Entity was not found.');
    }
    return $this->createEntity($row);
  }

}