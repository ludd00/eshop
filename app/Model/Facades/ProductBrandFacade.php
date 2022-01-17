<?php

namespace App\Model\Facades;

use App\Model\Entities\Product;
use App\Model\Entities\ProductBrand;
use App\Model\Repositories\BrandRepository;
use App\Model\Repositories\ProductBrandRepository;

/**
 * Class ProductBrandFacade - fasáda pro využívání hodnoceni z presenterů
 * @package App\Model\Facades
 */
class ProductBrandFacade
{
  /** @var ProductBrandRepository $productBrandRepository */
  private /*$productBrandRepository*/
    $productBrandRepository;
  /** @var BrandRepository $brandRepository */
  private /*$brandRepository*/
    $brandRepository;

  /**
   * Metoda pro získání jednoho produktu
   * @param int $id
   * @return ProductBrand
   * @throws \Exception
   */
  public function getProductBrand(int $productId):ProductBrand {
    return $this->productBrandRepository->findBy(['product_id'=>$productId]);
  }

  public function __construct(ProductBrandRepository $productBrandRepository, BrandRepository $brandRepository)
  {
    $this->productBrandRepository = $productBrandRepository;
    $this->brandRepository = $brandRepository;
  }

  /**
   * Metoda pro uložení vyrobce produktu
   */
  public function saveProductBrand(ProductBrand &$productBrand){
    return (bool)$this->productBrandRepository->persist($productBrand);
  }

    /**
     * Metoda pro uložení vyrobce produktu
     */
    public function saveNewProductBrand(ProductBrand &$productBrand, Product $product, int $brandId){
        $productBrand->productId= $product->productId;
        $productBrand->brandId = $brandId;
        return (bool)$this->productBrandRepository->persist($productBrand);
    }

  /**
   * Metoda pro smazání kategorie
   * @param ProductBrand $productBrand
   * @return bool
   */
  public function deleteProductBrand(ProductBrand $productBrand):bool {
    try{
      return (bool)$this->productBrandRepository->delete($productBrand);
    }catch (\Exception $e){
      return false;
    }
  }



}