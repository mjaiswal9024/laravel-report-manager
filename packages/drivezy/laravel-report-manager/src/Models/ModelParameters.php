<?php

namespace Drivezy\LaravelReportManager\Models;

use Drivezy\LaravelReportManager\Observers\ModelParameterObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class ModelParam
 * @package Drivezy\LaravelReportManager\Models
 */
class ModelParameters extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_model_parameter_details';

    /**
     *Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ModelParameterObserver());
    }
}
