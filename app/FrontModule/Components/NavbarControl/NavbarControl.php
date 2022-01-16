<?php

namespace App\FrontModule\Components\NavbarControl;

use App\Model\Facades\CategoriesFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Template;

/**
 * Class NavbarControl
 * @package App\FrontModule\Components\NavbarControl
 */
class NavbarControl extends Control{
  /** @var CategoriesFacade $categoriesFacade */
  private $categoriesFacade;

  /**
   * Akce renderující šablonu s odkazem pro zobrazení harmonogramu na desktopu
   * @param array $params = []
   */
  public function render(){
    $template=$this->prepareTemplate('default');
    $template->categories=$this->categoriesFacade->findCategories();
    $template->render();
  }

  /**
   * NavbarControl constructor.
   * @param CategoriesFacade $categoriesFacade
   */
  public function __construct(CategoriesFacade $categoriesFacade){
    $this->categoriesFacade=$categoriesFacade;
  }

  /**
   * Metoda vytvářející šablonu komponenty
   * @param string $templateName=''
   * @return Template
   */
  private function prepareTemplate(string $templateName=''):Template{
    $template=$this->template;
    if (!empty($templateName)){
      $template->setFile(__DIR__.'/templates/'.$templateName.'.latte');
    }
    return $template;
  }

}