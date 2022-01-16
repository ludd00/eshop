<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\ProductCartForm\ProductCartFormFactory;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;
use App\Model\Facades\BrandFacade;
use Nette\Application\BadRequestException;
use Nette\Utils\Image;
use App\FrontModule\Components\CartControl\CartControl;
use App\FrontModule\Components\ProductCartForm\ProductCartForm;
use Nette\Application\UI\Multiplier;

/**
 * Class ProductPresenter
 * @package App\FrontModule\Presenters
 * @property string $category
 */
class ProductPresenter extends BasePresenter{
  /** @var ProductsFacade $productsFacade */
  private $productsFacade;
  /** @var CategoriesFacade $categoriesFacade */
  private $categoriesFacade;
  /** @var ProductCartFormFactory $productCartFormFactory */
  private $productCartFormFactory;
  /** @var BrandFacade $brandFacade */
  private $brandFacade;

  /** @persistent */
  public $category = null;
  /** @persistent */
  public $brand = null;

  /**
   * Akce pro zobrazení jednoho produktu
   * @param string $url
   * @throws BadRequestException
   */
  public function renderShow(string $url):void {
    try{
      $product = $this->productsFacade->getProductByUrl($url);
    }catch (\Exception $e){
      throw new BadRequestException('Produkt nebyl nalezen.');
    }

    $this->template->product = $product;
  }

  /**
   * Akce pro vykreslení přehledu produktů
   */
  public function renderList(){

    #region category
    $activeCategory = null;
    if ($this->category){
      try {
        $activeCategory=$this->categoriesFacade->getCategory($this->category);
      }catch (\Exception $e){
        $this->redirect('list', ['category'=>null]);
      }
    }
    $this->template->activeCategory=$activeCategory;
    #endregion

    #region brand
    $activeBrand = null;
    if ($this->brand){
      try {
        $activeBrand=$this->brandFacade->getBrand($this->brand);
      }catch (\Exception $e){
        $this->redirect('list', ['brand'=>null]);
      }
    }
    $this->template->activeBrand=$activeBrand;
    $this->template->brands = $this->brandFacade->findBrands();
    #endregion

    //nacteni produktu
    $this->template->products = $this->productsFacade->findProductsBy($activeCategory, $activeBrand);
  }

  public function actionPhoto($id) {
    $product = $this->productsFacade->getProduct($id);
    $path = __DIR__.'/../../../www/img/products/'.$product->productId.'.'.$product->photoExtension;
    if (file_exists($path)){
      $image = Image::fromFile($path);
      $image->send(Image::PNG);
    } else{
      $image = Image::fromFile(__DIR__.'/../../../www/img/products/img.png');
      $image->send(Image::PNG);
    }
  }

  protected function createComponentProductCartForm():Multiplier {
    return new Multiplier(function($productId){
      $form = $this->productCartFormFactory->create();
      $form->setDefaults(['productId'=>$productId]);
      $form->onSubmit[]=function(ProductCartForm $form){
        try{
          $product = $this->productsFacade->getProduct($form->values->productId);
          //kontrola zakoupitelnosti
        }catch (\Exception $e){
          $this->flashMessage('Produkt nejde přidat do košíku','error');
          $this->redirect('this');
        }
        //přidání do košíku
        /** @var CartControl $cart */
        $cart = $this->getComponent('cart');
        $cart->addToCart($product, (int)$form->values->count);
        $this->redirect('this');
      };

      return $form;
    });
  }


  #region injections
  public function injectProductsFacade(ProductsFacade $productsFacade):void {
    $this->productsFacade=$productsFacade;
  }

  public function injectCategoriesFacade(CategoriesFacade $categoriesFacade):void {
    $this->categoriesFacade=$categoriesFacade;
  }

  public function injectProductCartFormFactory(ProductCartFormFactory $productCartFormFactory):void {
    $this->productCartFormFactory=$productCartFormFactory;
  }

  public function injectBrandFacade(BrandFacade $brandFacade):void {
    $this->brandFacade=$brandFacade;
  }

  #endregion injections
}