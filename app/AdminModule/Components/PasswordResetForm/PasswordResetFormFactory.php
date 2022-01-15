<?php

namespace App\AdminModule\Components\PasswordResetForm;

/**
 * Interface PasswordResetFormFactory
 * @package App\AdminModule\Components\PasswordResetForm
 */
interface PasswordResetFormFactory{

    public function create():PasswordResetForm;

}