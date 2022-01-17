<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\BrandEditForm\BrandEditForm;
use App\AdminModule\Components\BrandEditForm\BrandEditFormFactory;
use App\Model\Facades\BrandsFacade;

/**
 * Class BrandPresenter
 * @package App\AdminModule\Presenters
 */
class BrandPresenter extends BasePresenter{
  /** @var BrandsFacade $brandsFacade */
  private $brandsFacade;
  /** @var BrandEditFormFactory $brandEditFormFactory */
  private $brandEditFormFactory;

  /**
   * Akce pro vykreslení seznamu kategorií
   */
  public function renderDefault():void {
    $this->template->brands=$this->brandsFacade->findBrands(['order'=>'name']);
  }

  /**
   * Akce pro úpravu jedné kategorie
   * @param int $id
   * @throws \Nette\Application\AbortException
   */
  public function renderEdit(int $id):void {
    try{
      $brand=$this->brandsFacade->getBrand($id);
    }catch (\Exception $e){
      $this->flashMessage('Požadovaný výrobce nebyl nalezen.', 'error');
      $this->redirect('default');
    }
    $form=$this->getComponent('brandEditForm');
    $form->setDefaults($brand);
    $this->template->brand=$brand;
  }

  /**
   * Akce pro smazání kategorie
   * @param int $id
   * @throws \Nette\Application\AbortException
   */
  public function actionDelete(int $id):void {
    try{
      $brand=$this->brandsFacade->getBrand($id);
    }catch (\Exception $e){
      $this->flashMessage('Požadovaný výrobce nebyl nalezen.', 'error');
      $this->redirect('default');
    }

    if (!$this->user->isAllowed($brand,'delete')){
      $this->flashMessage('Tohoto výrobce není možné smazat.', 'error');
      $this->redirect('default');
    }

    if ($this->brandsFacade->deleteBrand($brand)){
      $this->flashMessage('Výrobce byl smazán.', 'info');
    }else{
      $this->flashMessage('Tohoto výrobce není možné smazat.', 'error');
    }

    $this->redirect('default');
  }

  /**
   * Formulář na editaci kategorií
   * @return BrandEditForm
   */
  public function createComponentBrandEditForm():BrandEditForm {
    $form = $this->brandEditFormFactory->create();
    $form->onCancel[]=function(){
      $this->redirect('default');
    };
    $form->onFinished[]=function($message=null){
      if (!empty($message)){
        $this->flashMessage($message);
      }
      $this->redirect('default');
    };
    $form->onFailed[]=function($message=null){
      if (!empty($message)){
        $this->flashMessage($message,'error');
      }
      $this->redirect('default');
    };
    return $form;
  }

  #region injections
  public function injectBrandsFacade(BrandsFacade $brandsFacade){
    $this->brandsFacade=$brandsFacade;
  }
  public function injectBrandEditFormFactory(BrandEditFormFactory $brandEditFormFactory){
    $this->brandEditFormFactory=$brandEditFormFactory;
  }
  #endregion injections

}
