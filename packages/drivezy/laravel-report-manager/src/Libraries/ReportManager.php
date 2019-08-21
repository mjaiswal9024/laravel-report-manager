<?php

namespace Drivezy\LaravelReportManager\Libraries;

/**
 * Class ReportManager
 * @package Drivezy\LaravelReportManager\Libraries
 */
class ReportManager {

    /**
     * @var object|null
     */
    public $request = null;

    /**
     * @var null
     */
    public $response = null;

    /**
     * ReportManager constructor.
     * @param $request
     */
    public function __construct ($request) {
        $this->request = (object) $request;

        $this->initializeReport();
    }

    /**
     * initialize report process
     *
     * @return null
     */
    private function initializeReport () {
        $this->initializeValidationProcess();

        if ( !$this->response['success'] )
            return;

        $this->initializeResponseConstruction();
    }

    /**
     * initialize different validation process before running script and creating response object
     *
     * @return array|null
     */
    private function initializeValidationProcess () {
        $preValidation = new ReportPreValidation($this->request);

        if ( !$preValidation->response['success'] )
            return $this->response = $preValidation->response;

        $this->request = ( new ReportSanitizer($this->request) )->request;

        $postValidation = new ReportPostValidation($this->request);

        if ( $postValidation->response['success'] )
            return $this->response = $preValidation->response;
    }

    /**
     * method to initiate response construction
     */
    private function initializeResponseConstruction () {
        $this->response = ( new ReportResponseConstructor() )->response;
    }
}