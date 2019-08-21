<?php

namespace Drivezy\LaravelReportManager\Libraries;

/**
 * Class ReportValidation
 * @package Drivezy\LaravelReportManager\Libraries
 */
class ReportPostValidation {

    /**
     * @var null
     */
    public $request = null;

    /**
     * @var array|null
     */
    public $response = null;

    /**
     * ReportPostValidation constructor.
     * @param $request
     */
    public function __construct ($request) {
        $this->request = $request;
        $this->response = success_message('Post validation completed');

        $this->initializePostValidation();
    }

    /**
     * initialize post validation process
     */
    private function initializePostValidation () {
        $this->scriptValidation();
    }

    /**
     * validate script
     *
     * @return array
     */
    private function scriptValidation () {
        if ( !$this->request->script )
            return $this->response = failure_message('Reporting script not found');

        $this->parametersValidation();
    }

    /**
     * validation for missing parameters in request object
     *
     * @return array
     */
    private function parametersValidation () {
        foreach ( $this->request as $key => $value ) {
            $this->request->script = str_replace('#' . $key . '#', $value, $this->request->script);
        }

        preg_match_all("/#[a-z]+/", $this->script, $params);

        if ( count($params[0]) )
            return $this->response = failure_message('Parameters missing');
    }
}