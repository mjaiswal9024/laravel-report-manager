<?php

namespace Drivezy\LaravelReportManager\Observers;

use Drivezy\LaravelUtility\Observers\BaseObserver;

/**
 * Class ModelParamObserver
 * @package Drivezy\LaravelReportManager\Observers
 */
class ModelParameterObserver extends BaseObserver {
    /**
     * @var array
     */
    protected $rules = [
        'model_id'      => 'required',
        'parameter'     => 'required',
        'default_value' => 'required',
    ];
}