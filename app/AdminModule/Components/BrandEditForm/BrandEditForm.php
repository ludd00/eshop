<?php

namespace App\AdminModule\Components\BrandEditForm;

use App\Model\Entities\Brand;
use App\Model\Facades\BrandsFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class BrandEditForm
 * @package App\AdminModule\Components\BrandEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class BrandEditForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onFailed */
  public $onFailed = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];
  /** @var BrandsFacade $brandsFacade */
  private $brandsFacade;

  /**
   * BrandEditForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param BrandsFacade $brandsFacade
   * @noinspection PhpOptionalBeforeRequiredParametersInspection
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, BrandsFacade $brandsFacade){
    parent::__construct($parent, $name);
    $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
    $this->brandsFacade=$brandsFacade;
    $this->createSubcomponents();
  }

  private function createSubcomponents(){
    $brandId=$this->addHidden('brandId');
    $this->addText('name','Název výrobce')
      ->setRequired('Musíte zadat název výrobce');

    $this->addSubmit('ok','uložit')
      ->onClick[]=function(SubmitButton $button){
      $values=$this->getValues('array');
      if (!empty($values['brandId'])){
        try{
          $brand=$this->brandsFacade->getBrand($values['brandId']);
        }catch (\Exception $e){
          $this->onFailed('Požadovaný výrobce nebyl nalezen.');
          return;
        }
      }else{
        $brand=new Brand();
      }
      $brand->assign($values,['name']);
      $this->brandsFacade->saveBrand($brand);
      $this->setValues(['brandId'=>$brand->brandId]);
      $this->onFinished('Výrobce byl uložen.');
    };
    $this->addSubmit('storno','zrušit')
      ->setValidationScope([$brandId])
      ->onClick[]=function(SubmitButton $button){
      $this->onCancel();
    };
  }

  /**
   * Metoda pro nastavení výchozích hodnot formuláře
   * @param Brand|array|object $values
   * @param bool $erase = false
   * @return $this
   */
  public function setDefaults($values, bool $erase = false):self {
    if ($values instanceof Brand){
      $values = [
        'brandId'=>$values->brandId,
        'name'=>$values->name
      ];
    }
    parent::setDefaults($values, $erase);
    return $this;
  }

}