<?php

namespace App\Model\Facades;

use App\Model\Entities\Brand;
use App\Model\Entities\Series;
use App\Model\Repositories\SeriesRepository;

/**
 * Class SeriesFacade - fasáda pro využívání vyrobcu z presenterů
 * @package App\Model\Facades
 */
class SeriesFacade
{
  /** @var SeriesRepository $seriesRepository */
  private /*$seriesRepository*/
    $seriesRepository;

  public function __construct(SeriesRepository $seriesRepository)
  {
    $this->seriesRepository = $seriesRepository;
  }

  /**
   * Metoda pro načtení jedné serie
   * @param int $id
   * @return Series|null
   */
  public function getSeries(?int $id):?Series
  {
    try {
      $series = $this->seriesRepository->find($id);
    }catch (\Exception $e){
      $series = null;
    }
    return $series;
  }

  /**
   * @param array|null $params
   * @param int|null $offset
   * @param int|null $limit
   * @return Series[]
   */
  public function findSeries(array $params=null,int $offset=null,int $limit=null):array {
    return $this->seriesRepository->findAllBy($params,$offset,$limit);
  }
}