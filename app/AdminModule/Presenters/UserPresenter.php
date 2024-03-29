<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\PasswordResetForm\PasswordResetForm;
use App\AdminModule\Components\PasswordResetForm\PasswordResetFormFactory;
use App\AdminModule\Components\UserEditForm\UserEditForm;
use App\AdminModule\Components\UserEditForm\UserEditFormFactory;
use App\Model\Facades\UsersFacade;
use App\Model\Repositories\RoleRepository;


/**
 * Class UserPresenter
 * @package App\AdminModule\Presenters
 */
class UserPresenter extends BasePresenter
{
    /** @var UsersFacade $usersFacade */
    private $usersFacade;
    /** @var UserEditFormFactory $userEditFormFactory */
    private $userEditFormFactory;
    /** @var PasswordResetFormFactory $passwordResetFormFactory */
    private $passwordResetFormFactory;
    /** @var RoleRepository $roleRepository */
    private $roleRepository;

    /**
     * Akce pro vykreslení seznamu uživatelů
     */
    public function renderDefault(): void
    {
        $this->template->users = $this->usersFacade->findUsers(['order' => 'email']);
    }

    /**
     * Akce pro zablokování uživatele
     * @throws \Nette\Application\AbortException
     */
    public function handleBlockById(int $id): void
    {
        try {
            $user = $this->usersFacade->getUser($id);
        } catch (\Exception $e) {
            $this->flashMessage('Požadovaný uživatel nebyl nalezen.', 'error');
            $this->redirect('default');
        }

        if (!empty($user)) {
            if ($user->blocked) {

                try {
                    $role = $this->roleRepository->getRole('authenticated');
                } catch (\Exception $e) {
                    $this->flashMessage('Role nebyla nalezena.', 'error');
                    $this->redirect('default');
                }

                $user->role = $role;
                $user->blocked = false;

                $this->flashMessage("Uživatel '" . $user->name . "' byl odblokován. ");
            }
            else {

                try {
                    $role = $this->roleRepository->getRole('blocked');
                } catch (\Exception $e) {
                    $this->flashMessage('Role nebyla nalezena.', 'error');
                    $this->redirect('default');
                }

                $user->role = $role;
                $user->blocked = true;

                $this->flashMessage("Uživatel '" . $user->name . "' byl zablokován. ");
            }

            $this->usersFacade->saveUser($user);
            $this->redirect('default');

        } else {
            $this->flashMessage("Uživatel nebyl nalezen. ");
            $this->redirect('default');
        }
    }

    /**
     * Akce pro reset hesla administrátorem
     */
    public function renderPasswordReset(int $id): void
    {
        try {
            $user = $this->usersFacade->getUser($id);
        } catch (\Exception $e) {
            $this->flashMessage('Požadovaný uživatel nebyl nalezen.', 'error');
            $this->redirect('default');
        }
        $form = $this->getComponent('passwordResetForm');
        //$form->setUserReset($user);
        $form->setDefaults($user);
        $this->template->userReset = $user;
    }


    /**
     * Akce pro úpravu jednoho uživatele
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function renderEdit(int $id): void
    {
        try {
            $user = $this->usersFacade->getUser($id);
        } catch (\Exception $e) {
            $this->flashMessage('Požadovaný uživatel nebyl nalezen.', 'error');
            $this->redirect('default');
        }

        $form = $this->getComponent('userEditForm');
        $form->setDefaults($user);
        $this->template->userEdit = $user;
    }

    /**
     * Formulář na editaci uživatelů
     * @return UserEditForm
     */
    public function createComponentUserEditForm(): UserEditForm
    {
        $form = $this->userEditFormFactory->create();
        $form->onCancel[] = function () {
            $this->redirect('default');
        };
        $form->onFinished[] = function ($message = null) {
            if (!empty($message)) {
                $this->flashMessage($message);
            }
            $this->redirect('default');
        };
        $form->onFailed[] = function ($message = null) {
            if (!empty($message)) {
                $this->flashMessage($message, 'error');
            }
            $this->redirect('default');
        };
        return $form;
    }

    /**
     * Formulář na reset hesla uživatele
     * @return UserEditForm
     */
    public function createComponentPasswordResetForm(): PasswordResetForm
    {
        $form = $this->passwordResetFormFactory->create();
        $form->onCancel[] = function () {
            $this->redirect('default');
        };
        $form->onFinished[] = function ($message = null) {
            if (!empty($message)) {
                $this->flashMessage($message);
            }
            $this->redirect('default');
        };
        $form->onFailed[] = function ($message = null) {
            if (!empty($message)) {
                $this->flashMessage($message, 'error');
            }
            $this->redirect('default');
        };
        return $form;
    }

    #region injections
    public function injectUsersFacade(UsersFacade $usersFacade)
    {
        $this->usersFacade = $usersFacade;
    }

    public function injectUserEditFormFactory(UserEditFormFactory $userEditFormFactory)
    {
        $this->userEditFormFactory = $userEditFormFactory;
    }

    public function injectPasswordResetFormFactory(PasswordResetFormFactory $passwordResetFormFactory)
    {
        $this->passwordResetFormFactory = $passwordResetFormFactory;
    }

    public function injectRoleRepository(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    #endregion injections

}
