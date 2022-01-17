<?php

namespace App\AdminModule\Components\SeriesEditForm;

use App\Model\Entities\Series;
use App\Model\Facades\SeriesFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class SeriesEditForm
 * @package App\AdminModule\Components\SeriesEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class SeriesEditForm extends Form{

    use SmartObject;

    /** @var callable[] $onFinished */
    public $onFinished = [];
    /** @var callable[] $onFailed */
    public $onFailed = [];
    /** @var callable[] $onCancel */
    public $onCancel = [];
    /** @var SeriesFacade $seriesFacade */
    private $seriesFacade;

    /**
     * SeriesEditForm constructor.
     * @param Nette\ComponentModel\IContainer|null $parent
     * @param string|null $name
     * @param SeriesFacade $seriesFacade
     * @noinspection PhpOptionalBeforeRequiredParametersInspection
     */
    public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null,SeriesFacade $seriesFacade){
        parent::__construct($parent, $name);
        $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
        $this->seriesFacade=$seriesFacade;
        $this->createSubcomponents();
    }

    private function createSubcomponents(){
        $seriesId=$this->addHidden('seriesId');
        $this->addText('name','Název série')
            ->setRequired('Musíte zadat název série');

        $this->addSubmit('ok','uložit')
            ->onClick[]=function(SubmitButton $button){
            $values=$this->getValues('array');
            if (!empty($values['seriesId'])){
                try{
                    $series=$this->seriesFacade->getSeries($values['seriesId']);
                }catch (\Exception $e){
                    $this->onFailed('Požadovaná série nebyla nalezena.');
                    return;
                }
            }else{
                $series=new Series();
            }
            $series->assign($values,['name']);
            $this->seriesFacade->saveSeries($series);
            $this->setValues(['seriesId'=>$series->seriesId]);
            $this->onFinished('Série byla uložena.');
        };
        $this->addSubmit('storno','zrušit')
            ->setValidationScope([$seriesId])
            ->onClick[]=function(SubmitButton $button){
            $this->onCancel();
        };
    }

    /**
     * Metoda pro nastavení výchozích hodnot formuláře
     * @param Series|array|object $values
     * @param bool $erase = false
     * @return $this
     */
    public function setDefaults($values, bool $erase = false):self {
        if ($values instanceof Series){
            $values = [
                'seriesId'=>$values->seriesId,
                'name'=>$values->name
            ];
        }
        parent::setDefaults($values, $erase);
        return $this;
    }

}