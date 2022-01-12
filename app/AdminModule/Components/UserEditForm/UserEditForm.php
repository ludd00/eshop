<?php

namespace App\AdminModule\Components\UserEditForm;

use App\Model\Entities\User;
use App\Model\Facades\UsersFacade;
use App\Model\Repositories\RoleRepository;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class UserEditForm
 * @package App\AdminModule\Components\UserEditForm
 *
 * @method onFinished(string $message = '')
 * @method onFailed(string $message = '')
 * @method onCancel()
 */
class UserEditForm extends Form{

  use SmartObject;

  /** @var callable[] $onFinished */
  public $onFinished = [];
  /** @var callable[] $onFailed */
  public $onFailed = [];
  /** @var callable[] $onCancel */
  public $onCancel = [];
  /** @var UsersFacade $usersFacade */
  private $usersFacade;
  /** @var RoleRepository $roleRepository */
  private $roleRepository;

  /**
   * UserEditForm constructor.
   * @param Nette\ComponentModel\IContainer|null $parent
   * @param string|null $name
   * @param UsersFacade $usersFacade
   * @noinspection PhpOptionalBeforeRequiredParametersInspection
   */
  public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, UsersFacade $usersFacade, RoleRepository $roleRepository){
    parent::__construct($parent, $name);
    $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
    $this->usersFacade=$usersFacade;
    $this->roleRepository=$roleRepository;
    $this->createSubcomponents();
  }

  private function createSubcomponents(){
    $userId=$this->addHidden('userId');
    $this->addText('name','Jméno')
    ->setRequired('Musíte zadat jméno');
    $this->addText('email','E-mail')
    ->setRequired('Musíte zadat e-mail');


    #region role
    $roles=$this->roleRepository->findAllBy();
    $rolesArr=[];
    foreach ($roles as $role){
      $rolesArr[$role->roleId]=$role->roleId;
    }
    $this->addSelect('roleId','Role',$rolesArr)
      ->setPrompt('--vyberte roli--')
      ->setRequired(false);
    #endregion role




    $this->addSubmit('ok','uložit')
      ->onClick[]=function(SubmitButton $button){
      $values=$this->getValues('array');
      if (!empty($values['userId'])){
        try{
          $userById=$this->usersFacade->getUser($values['userId']);
        }catch (\Exception $e){
          $this->onFailed('Požadovaný uživatel nebyl nalezen.');
          return;
        }
      }else{
        $userById=new User();
      }
      $userById->assign($values,['name','email']);
      $userById->role = $this->roleRepository->getRole($values['roleId']);
      $this->usersFacade->saveUser($userById);
      $this->setValues(['userId'=>$userById->userId]);

      $this->onFinished('Uživatel byl uložen.');
    };
    $this->addSubmit('storno','zrušit')
      ->setValidationScope([$userId])
      ->onClick[]=function(SubmitButton $button){
      $this->onCancel();
    };
  }

  /**
   * Metoda pro nastavení výchozích hodnot formuláře
   * @param User|array|object $values
   * @param bool $erase = false
   * @return $this
   */
  public function setDefaults($values, bool $erase = false):self {
    if ($values instanceof User){
      $values = [
        'userId'=>$values->userId,
        'roleId'=>$values->role?$values->role->roleId:null,
        'name'=>$values->name,
        'email'=>$values->email
      ];
    }
    parent::setDefaults($values, $erase);
    return $this;
  }

}