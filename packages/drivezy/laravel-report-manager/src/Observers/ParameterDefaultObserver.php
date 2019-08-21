<?php

namespace Drivezy\LaravelReportManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class ParameterDefaultObserver
 * @package Drivezy\LaravelReportManager\Observers
 */
class ParameterDefaultObserver extends BaseObserver {

    /**
     * @var array
     */
    protected $rules = [
        'name'                 => 'required',
        'backend_cast_phrase'  => 'required',
        'frontend_cast_phrase' => 'required',
    ];
}