<?php

namespace App\AdminModule\Components\PasswordResetForm;

use App\AdminModule\Components\UserEditForm\UserEditForm;
use App\Model\Entities\User;
use App\Model\Facades\UsersFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\SmartObject;
use Nextras\FormsRendering\Renderers\Bs4FormRenderer;
use Nextras\FormsRendering\Renderers\FormLayout;

/**
 * Class PasswordResetForm
 * @package App\AdminModule\Components\PasswordResetForm
 *
 * @method onFinished($message='')
 * @method onCancel()
 */
class PasswordResetForm extends Form{

    use SmartObject;

    /** @var callable[] $onFinished */
    public $onFinished = [];
    /** @var callable[] $onCancel */
    public $onCancel = [];
    /** @var callable[] $onFailed */
    public $onFailed = [];

    /** @var UsersFacade $usersFacade */
    private $usersFacade;
    /** @var Nette\Application\LinkGenerator $linkGenerator */
    private $linkGenerator;
    /** @var string $mailFromEmail */
    private $mailFromEmail = '';
    /** @var string $mailFromName */
    private $mailFromName = '';
    /** @var User $userReset
        User pro resetování hesla
     */
    private $userReset;

    /**
     * ForgottenPasswordForm constructor.
     * @param Nette\ComponentModel\IContainer|null $parent
     * @param string|null $name
     * @param UsersFacade $usersFacade
     * @param Nette\Application\LinkGenerator $linkGenerator
     */
    public function __construct(Nette\ComponentModel\IContainer $parent = null, string $name = null, UsersFacade $usersFacade, Nette\Application\LinkGenerator $linkGenerator){
        parent::__construct($parent, $name);
        $this->setRenderer(new Bs4FormRenderer(FormLayout::VERTICAL));
        $this->usersFacade=$usersFacade;
        $this->createSubcomponents();
        $this->linkGenerator=$linkGenerator;
    }

    /**
     * Metoda volaná z configu, která se používá pro nastavení údajů o odesílateli e-mailů
     * @param string $email
     * @param string $name
     */
    public function setMailFrom(string $email, string $name):void {
        $this->mailFromEmail=$email;
        $this->mailFromName=$name;
    }

    private function createSubcomponents(){

        $this->addHidden('user_id');
        $this->addHidden('email');

        $this->addSubmit('ok','poslat e-mail pro obnovu hesla pro uživatele ' . $this->userReset->name)
            ->onClick[]=function(SubmitButton $button){

            $userEmail = $this->values->email;

            //najdeme daného uživatele
            try{
                $user = $this->usersFacade->getUserByEmail($userEmail);
            }catch (\Exception $e){
                //uživatel nebyl nalezen - zvažte, zda o tom informovat uživatele, či nikoliv
                $this->onFinished('Pokud uživatelský účet s daným e-mailem existuje, poslali jsme vám odkaz na změnu hesla.');
                return;
            }
            //vygenerování odkaz na změnu hesla
            $forgottenPassword = $this->usersFacade->saveNewForgottenPasswordCode($user);
            $mailLink = $this->linkGenerator->link('Front:User:renewPassword', ['user'=>$user->userId, 'code'=>$forgottenPassword->code]);

            #region příprava textu mailu
            $mail = new Nette\Mail\Message();
            $mail->setFrom($this->mailFromEmail, $this->mailFromName);
            $mail->addTo($user->email, $user->name);
            $mail->subject = 'Obnova zapomenutého hesla';
            $mail->htmlBody = 'Obdrželi jsme vaši žádost na obnovu zapomenutého hesla. Pokud si přejete heslo změnit, <a href="'.$mailLink.'">klikněte zde</a>.';
            #endregion endregion příprava textu mailu

            //odeslání mailu pomocí PHP funkce mail
            $mailer = new Nette\Mail\SendmailMailer;
            $mailer->send($mail);

            $this->onFinished('E-mail byl odeslán.');
        };
        $this->addSubmit('storno','zrušit')
            ->setValidationScope([])
            ->onClick[]=function(SubmitButton $button){
            $this->onCancel('cancel');
        };
    }

    public function setUserReset(User $userReset){
            $this->userReset = $userReset;
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
                'email'=>$values->email
                //'roleId'=>$values->role?$values->role->roleId:null,
                //'name'=>$values->name,
            ];
        }
        parent::setDefaults($values, $erase);
        return $this;
    }
}