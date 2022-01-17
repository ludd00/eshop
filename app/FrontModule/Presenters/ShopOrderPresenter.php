<?php

namespace App\FrontModule\Presenters;

use App\FrontModule\Components\ProductCartForm\ProductCartFormFactory;
use App\FrontModule\Components\ShopOrderCreateForm\ShopOrderCreateForm;
use App\FrontModule\Components\ShopOrderCreateForm\ShopOrderCreateFormFactory;
use App\Model\Facades\CategoriesFacade;
use App\Model\Facades\ProductsFacade;
use App\Model\Facades\BrandsFacade;
use App\Model\Facades\ShopOrdersFacade;
use Nette;
use Nette\Application\BadRequestException;
use Nette\Utils\Image;
use App\FrontModule\Components\CartControl\CartControl;
use App\FrontModule\Components\ProductCartForm\ProductCartForm;
use Nette\Application\UI\Multiplier;

/**
 * Class ShopOrderPresenter
 * @package App\FrontModule\Presenters
 * @property string $category
 */
class ShopOrderPresenter extends BasePresenter{
  /** @var ProductsFacade $productsFacade */
  private $productsFacade;
  /** @var ShopOrdersFacade $shopOrdersFacade */
  private $shopOrdersFacade;
  /** @var CategoriesFacade $categoriesFacade */
  private $categoriesFacade;
  /** @var ProductCartFormFactory $productCartFormFactory */
  private $productCartFormFactory;
    /** @var ShopOrderCreateFormFactory $shopOrderCreateFormFactory */
    private $shopOrderCreateFormFactory;
  /** @var BrandsFacade $brandFacade */
  private $brandFacade;
  /** @var Security\User $user */
  private $user;

  /** @persistent */
  public $category = null;
  /** @persistent */
  public $brand = null;

    /**
     * Akce pro vykreslení seznamu objednávek pro přihlášeného uživatele
     * @throws \Exception
     */
    public function renderDefault():void {
        $user = $this->getUser();
        $this->template->shopOrders=$this->shopOrdersFacade->findShopOrdersByUser($user->id);
    }

  /**
   * Akce pro zobrazení jedné objednávky
   * @param string $id
   * @throws BadRequestException
   */
    public function renderDetail(int $id):void {
        try{
            $shopOrder=$this->shopOrdersFacade->getShopOrder($id);
        }catch (\Exception $e){
            $this->flashMessage('Požadovaná objednávka nebyla nalezena.', 'error');
            $this->redirect('default');
        }

        $this->template->shopOrder=$shopOrder;
    }

    /**
     * Akce pro zrušení objednávky
     * @param int $id
     */
    public function handleCancel(int $id){
        try{
            $shopOrder = $this->shopOrdersFacade->getShopOrder($id);
        }catch (\Exception $e) {
            $this->flashMessage('Objednávka nebyla nalezena', 'error');
            $this–>$this->redirect('default');
        }

        if(!empty($shopOrder)){
            $shopOrder->status = 'canceled';
            $this->shopOrdersFacade->saveShopOrder($shopOrder);
            $this->flashMessage('Objednávka byla zrušena.');
        }else{
            $this->flashMessage('Objednávka nebyla nalezena', 'error');
        }
        $this–>$this->redirect('default');
    }

    /**
     * Akce pro zaplacení
     * @param int $id
     */
    public function handlePay(int $id){
        try{
            $shopOrder = $this->shopOrdersFacade->getShopOrder($id);
        }catch (\Exception $e) {
            $this->flashMessage('Objednávka nebyla nalezena', 'error');
            $this–>$this->redirect('default');
        }

        if(!empty($shopOrder)){
            $shopOrder->paid = true;
            $shopOrder->status = 'paid';
            $this->shopOrdersFacade->saveShopOrder($shopOrder);
            $this->flashMessage('Objednávka byla úspěšně zaplacena ;)');
        }else{
            $this->flashMessage('Objednávka nebyla nalezena', 'error');
        }
        $this–>$this->redirect('default');
    }

    public function createComponentShopOrderCreateForm():ShopOrderCreateForm{
        $form=$this->shopOrderCreateFormFactory->create();
        $form->setCurrentUser($this->getUser());

        $form->onFinished[]=function($message=''){
            if (!empty($message)){
                $this->flashMessage($message);
            }
            $this->redirect('shopOrder:default');
        };
        $form->onCancel[]=function()use($form){
            $this->redirect('shopOrder:add');
        };

        return $form;
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

  public function injectShopOrderCreateFormFactory(ShopOrderCreateFormFactory $shopOrderCreateFormFactory):void {
        $this->shopOrderCreateFormFactory = $shopOrderCreateFormFactory;
  }

  public function injectBrandFacade(BrandsFacade $brandFacade):void {
    $this->brandFacade=$brandFacade;
  }

  public function injectShopOrdersFacade(ShopOrdersFacade $shopOrdersFacade):void {
    $this->shopOrdersFacade=$shopOrdersFacade;
  }

  #endregion injections
}