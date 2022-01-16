<?php

namespace App\Model\Facades;

use App\Model\Entities\Product;
use App\Model\Entities\ProductRating;
use App\Model\Entities\Rating;
use App\Model\Repositories\ProductRatingRepository;
use App\Model\Repositories\RatingRepository;

/**
 * Class RatingFacade - fasáda pro využívání hodnoceni z presenterů
 * @package App\Model\Facades
 */
class RatingFacade
{
  /** @var ProductRatingRepository $productRatingRepository */
  private /*$productRatingRepository*/
    $productRatingRepository;
  /** @var RatingRepository $ratingRepository */
  private /*$ratingRepository*/
    $ratingRepository;

  public function __construct(ProductRatingRepository $productRatingRepository, RatingRepository $ratingRepository)
  {
    $this->productRatingRepository = $productRatingRepository;
    $this->ratingRepository = $ratingRepository;
  }


  /**
   * @param int $productId
   * @return Rating
   * @throws \Exception
   */
  public function findRating(int $productId):Rating{
    return $this->ratingRepository->getRating($productId);
  }

  /**
   * @param array|null $params
   * @param int|null $offset
   * @param int|null $limit
   * @return array
   */
  public function findRatings(array $params=null,int $offset=null,int $limit=null):array {
    return $this->ratingRepository->findAllBy($params,$offset,$limit);
  }

  /**
   * Metoda pro uložení hodnocení produktu
   */
  public function saveAvgRating(int $productId){
    $avg = null;
    $count = null;
    $ratingById = $this->findRating($productId);
    $ratings = $this->productRatingRepository->findProductRatings($productId);
    foreach ($ratings as $rating) {
      $avg += $rating->stars;
      $count += 1;
    }
    $ratingById->avgStars = $avg/$count;
    return (bool)$this->ratingRepository->persist($ratingById);
  }



}