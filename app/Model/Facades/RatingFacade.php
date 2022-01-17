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
     * Metoda pro načtení jednoho hodnoceni
     * @param int $id
     * @return Rating|null
     */
    public function getRating(?int $id):?Rating
    {
        try {
            $rating = $this->ratingRepository->findBy(['product_id'=>$id]);
        }catch (\Exception $e){
            $rating = null;
        }
        return $rating;
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
    $rating = $this->getRating($productId);
    if ($rating){
        $produtcsRatings = $this->productRatingRepository->findProductRatings($productId);
        foreach ($produtcsRatings as $produtcsRating) {
            $avg += $produtcsRating->stars;
            $count += 1;
        }
        $rating->avgStars = $avg/$count;
        return (bool)$this->ratingRepository->persist($rating);
    }else{
        $rating = new Rating();
        $produtcsRatings = $this->productRatingRepository->findAllBy(['product_id'=>$productId]);
        foreach ($produtcsRatings as $produtcsRating) {
            $avg += $produtcsRating->stars;
            $count += 1;
        }
        $rating->productId = $productId;
        $rating->avgStars = $avg/$count;
        return (bool)$this->ratingRepository->persist($rating);
    }
  }




}