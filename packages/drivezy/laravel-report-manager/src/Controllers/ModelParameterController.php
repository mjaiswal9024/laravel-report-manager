<?php

namespace Drivezy\LaravelReportManager\Controllers;

use Drivezy\LaravelRecordManager\Controllers\RecordController;
use Drivezy\LaravelReportManager\Libraries\ReportUtilities;
use Drivezy\LaravelReportManager\Models\ModelParameters;

/**
 * Class ModelParamController
 * @package Drivezy\LaravelReportManager\Controllers
 */
class ModelParameterController extends RecordController {
    /**
     * @var string
     */
    public $model = ModelParameters::class;

    public function getModelParameters ($id) {
        return fixed_response(ReportUtilities::populateScriptParameters($id));
    }
}

