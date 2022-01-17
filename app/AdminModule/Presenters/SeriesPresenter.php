<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\SeriesEditForm\SeriesEditForm;
use App\AdminModule\Components\SeriesEditForm\SeriesEditFormFactory;
use App\Model\Facades\SeriesFacade;

/**
 * Class SeriesPresenter
 * @package App\AdminModule\Presenters
 */
class SeriesPresenter extends BasePresenter{
    /** @var SeriesFacade $seriesFacade */
    private $seriesFacade;
    /** @var SeriesEditFormFactory $seriesEditFormFactory */
    private $seriesEditFormFactory;

    /**
     * Akce pro vykreslení seznamu kategorií
     */
    public function renderDefault():void {
        $this->template->series=$this->seriesFacade->findSeries(['order'=>'name']);
    }

    /**
     * Akce pro úpravu jedné kategorie
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function renderEdit(int $id):void {
        try{
            $oneSeries=$this->seriesFacade->getSeries($id);
        }catch (\Exception $e){
            $this->flashMessage('Požadovaná série nebyla nalezena.', 'error');
            $this->redirect('default');
        }
        $form=$this->getComponent('seriesEditForm');
        $form->setDefaults($oneSeries);
        $this->template->oneSeries=$oneSeries;
    }

    /**
     * Akce pro smazání kategorie
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(int $id):void {
        try{
            $oneSeries=$this->seriesFacade->getSeries($id);
        }catch (\Exception $e){
            $this->flashMessage('Požadovaná série nebyla nalezena.', 'error');
            $this->redirect('default');
        }

        if (!$this->user->isAllowed($oneSeries,'delete')){
            $this->flashMessage('Tuto sérii není možné smazat.', 'error');
            $this->redirect('default');
        }

        if ($this->seriesFacade->deleteSeries($oneSeries)){
            $this->flashMessage('Série byla smazána.', 'info');
        }else{
            $this->flashMessage('Tuto sérii není možné smazat.', 'error');
        }

        $this->redirect('default');
    }

    /**
     * Formulář na editaci kategorií
     * @return SeriesEditForm
     */
    public function createComponentSeriesEditForm():SeriesEditForm {
        $form = $this->seriesEditFormFactory->create();
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
    public function injectSeriesFacade(SeriesFacade $seriessFacade){
        $this->seriesFacade=$seriessFacade;
    }
    public function injectSeriesEditFormFactory(SeriesEditFormFactory $seriesEditFormFactory){
        $this->seriesEditFormFactory=$seriesEditFormFactory;
    }
    #endregion injections

}
