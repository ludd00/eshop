<?php

namespace App\Model\Facades;

use App\Model\Entities\Brand;
use App\Model\Repositories\BrandRepository;

/**
 * Class BrandFacade - fasáda pro využívání vyrobcu z presenterů
 * @package App\Model\Facades
 */
class BrandFacade
{
  /** @var BrandRepository $brandRepository */
  private /*$brandRepository*/
    $brandRepository;

  public function __construct(BrandRepository $brandRepository)
  {
    $this->brandRepository = $brandRepository;
  }

  /**
   * Metoda pro načtení jedné kategorie
   * @param int $id
   * @return Brand
   * @throws \Exception
   */
  public function getBrand(int $id): Brand
  {
    return $this->brandRepository->find($id); //buď počítáme s možností vyhození výjimky, nebo ji ošetříme už tady a můžeme vracet např. null
  }

  /**
   * @param array|null $params
   * @param int|null $offset
   * @param int|null $limit
   * @return array
   */
  public function findBrands(array $params=null,int $offset=null,int $limit=null):array {
    return $this->brandRepository->findAllBy($params,$offset,$limit);
  }
}