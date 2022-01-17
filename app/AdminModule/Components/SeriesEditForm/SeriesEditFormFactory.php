<?php

namespace App\AdminModule\Components\SeriesEditForm;

/**
 * Interface SeriesEditFormFactory
 * @package App\AdminModule\Components\SeriesEditForm
 */
interface SeriesEditFormFactory{

    public function create():SeriesEditForm;

}