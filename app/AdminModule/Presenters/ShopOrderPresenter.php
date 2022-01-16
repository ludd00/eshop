<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\ProductEditForm\ProductEditForm;
use App\AdminModule\Components\ProductEditForm\ProductEditFormFactory;
use App\Model\Entities\Product;
use App\Model\Entities\ShopOrder;
use App\Model\Facades\ProductsFacade;
use App\Model\Facades\ShopOrdersFacade;
use Cassandra\Date;
use Nette\Utils\DateTime;

/**
 * Class ShopOrderPresenter
 * @package App\AdminModule\Presenters
 */
class ShopOrderPresenter extends BasePresenter
{
    /** @var ShopOrdersFacade $shopOrdersFacade */
    private $shopOrdersFacade;
    /** @var ProductEditFormFactory $productEditFormFactory */
    private $productEditFormFactory;

    /**
     * Akce pro vykreslení seznamu objednávek
     */
    public function renderDefault():void {
        $this->template->shopOrders=$this->shopOrdersFacade->findShopOrders();
    }

    /**
     * Akce pro vykreslení seznamu objednávek
     * @throws \Exception
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
     * Akce pro nastavení objednávky jako odeslané
     * @param int $id
     */
    public function handleSent(int $id){
        try{
            $shopOrder = $this->shopOrdersFacade->getShopOrder($id);
        }catch (\Exception $e) {
            $this->flashMessage('Objednávka nebyla nalezena', 'error');
            $this–>$this->redirect('default');
        }

        if(!empty($shopOrder)){
            if(empty($shopOrder->dateSent)){
                $shopOrder->dateSent = new \Dibi\DateTime();
                $shopOrder->status = "sent";
            }else {
                $shopOrder->dateSent = null;
                $shopOrder->dateDelivered = null;
                if($shopOrder->paid){
                    $shopOrder->status = "paid";
                }else{
                    $shopOrder->status = "confirmed";
                }
            }
            $this->shopOrdersFacade->saveShopOrder($shopOrder);
        }else{
            $this->flashMessage('Objednávka nebyla nalezena', 'error');
        }
        $this–>$this->redirect('default');
    }

    /**
     * Akce pro nastavení objednávky jako doručené
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleDelivered(int $id){
        try{
            $shopOrder = $this->shopOrdersFacade->getShopOrder($id);
        }catch (\Exception $e) {
            $this->flashMessage('Objednávka nebyla nalezena', 'error');
            $this–>$this->redirect('default');
        }

        if(!empty($shopOrder)){
            if(empty($shopOrder->dateDelivered)){
                $shopOrder->dateDelivered = new \Dibi\DateTime();
                $shopOrder->status = "delivered";
            }else {
                $shopOrder->dateDelivered = null;
                $shopOrder->status = "sent";
            }
            $this->shopOrdersFacade->saveShopOrder($shopOrder);
        }else{
            $this->flashMessage('Objednávka nebyla nalezena', 'error');
        }
        $this–>$this->redirect('default');
    }

    #region injections
    public function injectShopOrdersFacade(ShopOrdersFacade $shopOrdersFacade)
    {
        $this->shopOrdersFacade = $shopOrdersFacade;
    }
    #endregion injections

}
