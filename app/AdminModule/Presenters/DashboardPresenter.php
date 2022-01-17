<?php

namespace App\AdminModule\Presenters;

use App\Model\Entities\User;
use App\Model\Facades\ShopOrdersFacade;
use App\Model\Facades\UsersFacade;

class DashboardPresenter extends BasePresenter{

    /** @var UsersFacade $usersFacade */
    private $usersFacade;
    /** @var ShopOrdersFacade $shopOrdersFacade */
    private $shopOrdersFacade;

    public function renderDefault(){
        try{
            $myUser=$this->usersFacade->getUser($this->getUser()->id);
        }catch (\Exception $e){
            $this->flashMessage('Uživatel nebyl nalezen. Asi neexistujete.', 'error');
            $this->redirect('default');
        }

        try{
            $paidShopOrders=$this->shopOrdersFacade->findPaidShopOrders();
        }catch (\Exception $e){
            //$this->flashMessage('Nebyly žádné nalezeny.', 'error');
            //$paidShopOrders = null;
        }

        try{
            $sentShopOrders=$this->shopOrdersFacade->findSentShopOrders();
        }catch (\Exception $e){
            //$this->flashMessage('Nebyly žádné nalezeny.', 'error');
            $sentShopOrders = null;
        }

        $this->template->paidShopOrders = $paidShopOrders;
        $this->template->sentShopOrders = $sentShopOrders;
        $this->template->currentUser = $myUser;

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

    public function injectUsersFacade(UsersFacade $usersFacade){
        $this->usersFacade = $usersFacade;
    }

    public function injectShopOrdersFacade(ShopOrdersFacade $shopOrdersFacade){
        $this->shopOrdersFacade = $shopOrdersFacade;
    }

}
