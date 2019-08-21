<?php

namespace Drivezy\LaravelReportManager\Models;

use Drivezy\LaravelReportManager\Observers\ParameterDefaultObserver;
use Drivezy\LaravelUtility\Models\BaseModel;

/**
 * Class ParameterDefaults
 * @package Drivezy\LaravelReportManager\Models
 */
class ParameterDefaults extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'dz_parameter_defaults';

    /**
     *Override the boot functionality to add up the observer
     */
    public static function boot () {
        parent::boot();
        self::observe(new ParameterDefaultObserver());
    }
}