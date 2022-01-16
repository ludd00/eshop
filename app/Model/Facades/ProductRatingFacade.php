<?php

namespace App\Model\Facades;

use App\Model\Entities\Product;
use App\Model\Entities\ProductRating;
use App\Model\Entities\Rating;
use App\Model\Repositories\ProductRatingRepository;
use App\Model\Repositories\RatingRepository;

/**
 * Class ProductRatingFacade - fasáda pro využívání hodnoceni z presenterů
 * @package App\Model\Facades
 */
class ProductRatingFacade
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
   * Metoda pro uložení hodnocení produktu
   */
  public function saveProductRating(ProductRating &$productRating){
    return (bool)$this->productRatingRepository->persist($productRating);
  }



}