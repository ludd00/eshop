<?php

namespace App\Model\Facades;

use App\Model\Entities\Brand;
use App\Model\Repositories\BrandRepository;

/**
 * Class BrandsFacade - fasáda pro využívání vyrobcu z presenterů
 * @package App\Model\Facades
 */
class BrandsFacade
{
  /** @var BrandRepository $brandRepository */
  private /*$brandRepository*/
    $brandRepository;

  public function __construct(BrandRepository $brandRepository)
  {
    $this->brandRepository = $brandRepository;
  }

  /**
   * Metoda pro načtení jednoho vyrobce
   * @param int $id
   * @return Brand|null
   */
  public function getBrand(?int $id):?Brand
  {
    try {
      $brand = $this->brandRepository->find($id);
    } catch (\Exception $e) {
      $brand = null;
    }
    return $brand;
  }

  /**
   * @param array|null $params
   * @param int|null $offset
   * @param int|null $limit
   * @return Brand[]
   */
  public function findBrands(array $params=null,int $offset=null,int $limit=null):array {
    return $this->brandRepository->findAllBy($params,$offset,$limit);
  }

  /**
   * Metoda pro uložení vyrobce
   * @param Brand &$brand
   * @return bool - true, pokud byly v DB provedeny nějaké změny
   */
  public function saveBrand(Brand &$brand):bool {
    return (bool)$this->brandRepository->persist($brand);
  }

  /**
   * Metoda pro smazání výrobce
   * @param Brand $brand
   * @return bool
   */
  public function deleteBrand(Brand $brand):bool {
    try{
      return (bool)$this->brandRepository->delete($brand);
    }catch (\Exception $e){
      return false;
    }
  }
}