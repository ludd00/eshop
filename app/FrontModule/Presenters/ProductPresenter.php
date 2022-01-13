<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\ProductCartForm\ProductCartFormFactory;
use App\Model\Entities\Product;
use App\Model\Facades\ProductsFacade;
use Nette\Application\BadRequestException;
use Nette\Utils\Image;

/**
 * Class ProductPresenter
 * @package App\FrontModule\Presenters
 * @property string $category
 */
class ProductPresenter extends BasePresenter{
  /** @var ProductsFacade $productsFacade */
  private $productsFacade;
  /** @var ProductCartFormFactory $productCartFormFactory */
  private $productCartFormFactory;

  /** @persistent */
  public $category;

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
  public function renderList():void {
    //TODO tady by mělo přibýt filtrování podle kategorie, stránkování atp.
    $this->template->products = $this->productsFacade->findProducts(['order'=>'title']);
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


  #region injections
  public function injectProductsFacade(ProductsFacade $productsFacade):void {
    $this->productsFacade=$productsFacade;
  }

  public function injectProductCartFormFactory(ProductCartFormFactory $productCartFormFactory):void {
    $this->productCartFormFactory=$productCartFormFactory;
  }
  #endregion injections
}