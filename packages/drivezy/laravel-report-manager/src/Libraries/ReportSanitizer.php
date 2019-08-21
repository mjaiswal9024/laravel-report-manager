<?php

namespace Drivezy\LaravelReportManager\Libraries;

use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\SystemScript;
use Drivezy\LaravelReportManager\Models\ModelParameters;
use Illuminate\Support\Facades\Auth;

/**
 * Class ReportSanitizer
 * @package Drivezy\LaravelReportManager\Libraries
 */
class ReportSanitizer {

    /**
     * @var null
     */
    public $request = null;

    /**
     * ReportSanitizer constructor.
     * @param $request
     */
    public function __construct ($request) {
        $this->request = $request;

        $this->initiateSanitizer();
    }

    /**
     * initialize sanitizing process
     */
    private function initiateSanitizer () {
        $this->scriptSanitizer();
    }

    /**
     * add script to request object
     * @return null
     */
    private function scriptSanitizer () {
        $scriptDetails = SystemScript::where('source_type', md5(DataModel::class))
            ->where('source_id', $this->request->model_id)
            ->first();

        if ( !$scriptDetails || is_null($scriptDetails->script) )
            return $this->request->script = null;

        $this->request->script = $scriptDetails->script;

        $this->scriptParameterSanitizer();
    }

    /**
     * set parameter values for the script as per the request method
     */
    private function scriptParameterSanitizer () {
        $this->request->user_id = Auth::id();
        $this->request->request_method = $_SERVER['REQUEST_METHOD'];

        if ( $this->request->request_method === 'GET' )
            $this->setDefaultParameters();

        $this->setOtherRequiredParameters();
    }

    /**
     * set default parameter for script in request object
     */
    private function setDefaultParameters () {
        $defaultParamDetails = ModelParameters::where('model_id', $this->request->model_id)->get();

        foreach ( $defaultParamDetails as $params )
            $this->request->{$params->parameter} = ReportUtilities::convertParameterDefaultValues($params->default_value);
    }

    /**
     * set other required parameter for the script
     */
    private function setOtherRequiredParameters () {
        if ( !isset($this->request->limit) )
            $this->request->limit = 20;

        if ( !isset($this->request->page) )
            $this->request->page = 1;

        if ( !isset($this->request->order) )
            $this->request->order = 1;

        if ( !isset($this->request->filter_query) )
            $this->request->filter_query = '1=1';

        if ( !isset($request->grouping_columns) )
            $this->request->grouping_columns = null;

        if ( !isset($request->aggrigate_columns) )
            $this->request->aggrigate_columns = null;

        if ( !isset($this->request->export) )
            $this->request->aggrigate_columns = false;
    }
}