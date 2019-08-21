<?php

namespace Drivezy\LaravelReportManager\Controllers;

use Drivezy\LaravelReportManager\Libraries\ReportManager;
use Drivezy\LaravelReportManager\Libraries\ReportUtilities;
use Illuminate\Http\Request;

/**
 * Class ReportController
 * @package Drivezy\LaravelReportManager\Controllers
 */
class ReportController {

    /**
     * @param Request $request
     * @param $id integer
     * @return mixed
     */
    public function getReportData (Request $request, $id) {
        $request->request->set('model_id', $id);

        return fixed_response(( new ReportManager($request->all()) )->response);
    }

    /**
     * @param $id integer
     * @return mixed
     */
    public function getModelColumns ($id) {
        return fixed_response(ReportUtilities::populateScriptColumns($id));
    }
}