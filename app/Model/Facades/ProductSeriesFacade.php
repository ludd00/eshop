<?php

namespace App\Model\Facades;

use App\Model\Entities\Product;
use App\Model\Entities\ProductSeries;
use App\Model\Repositories\SeriesRepository;
use App\Model\Repositories\ProductSeriesRepository;

/**
 * Class ProductSeriesFacade - fasáda pro využívání hodnoceni z presenterů
 * @package App\Model\Facades
 */
class ProductSeriesFacade
{
  /** @var ProductSeriesRepository $productSeriesRepository */
  private /*$productSeriesRepository*/
    $productSeriesRepository;
  /** @var SeriesRepository $seriesRepository */
  private /*$seriesRepository*/
    $seriesRepository;

  /**
   * Metoda pro získání jednoho produktu
   * @param int $id
   * @return ProductSeries
   * @throws \Exception
   */
  public function getProductSeries(int $productId):ProductSeries {
    return $this->productSeriesRepository->findBy(['product_id'=>$productId]);
  }

  public function __construct(ProductSeriesRepository $productSeriesRepository,SeriesRepository $seriesRepository)
  {
    $this->productSeriesRepository = $productSeriesRepository;
    $this->seriesRepository = $seriesRepository;
  }

  /**
   * Metoda pro uložení vyrobce produktu
   */
  public function saveProductSeries(ProductSeries &$productSeries){
    return (bool)$this->productSeriesRepository->persist($productSeries);
  }

    /**
     * Metoda pro uložení vyrobce produktu
     */
    public function saveNewProductSeries(ProductSeries &$productSeries, Product $product, int $seriesId){
        $productSeries->productId= $product->productId;
        $productSeries->seriesId = $seriesId;
        return (bool)$this->productSeriesRepository->persist($productSeries);
    }

  /**
   * Metoda pro smazání kategorie
   * @param ProductSeries $productSeries
   * @return bool
   */
  public function deleteProductSeries(ProductSeries $productSeries):bool {
    try{
      return (bool)$this->productSeriesRepository->delete($productSeries);
    }catch (\Exception $e){
      return false;
    }
  }



}