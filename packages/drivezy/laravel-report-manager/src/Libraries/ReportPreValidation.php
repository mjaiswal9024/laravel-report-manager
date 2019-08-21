<?php

namespace Drivezy\LaravelReportManager\Libraries;

/**
 * Class ReportPreValidation
 * @package Drivezy\LaravelReportManager\Libraries
 */
class ReportPreValidation {

    /**
     * @var null
     */
    public $request = null;

    /**
     * @var array|null
     */
    public $response = null;

    /**
     * ReportPreValidation constructor.
     * @param $request
     */
    public function __construct ($request) {
        $this->request = $request;
        $this->response = success_message('Pre validation completed');

        $this->initializePreValidation();
    }

    /**
     * initialize pre validation process
     */
    private function initializePreValidation () {
        $this->checkUserAccess();
    }

    /**
     * check report access as per the logged in user
     */
    private function checkUserAccess () {
        $userRoleObject = ReportUtilities::getUserRoleObject();

        if ( in_array(1, $userRoleObject->roles) || in_array('super-admin', $userRoleObject->roleIdentifiers) )
            return;

        $modelRoleObject = ReportUtilities::getModelRoleObject($this->request->model_id);

        if ( in_array(2, $modelRoleObject->roles) || in_array('public', $modelRoleObject->roleIdentifiers) )
            return;

        $matchRole = array_diff($userRoleObject->role, $modelRoleObject->role);
        if ( count($matchRole) )
            return;

        $matchRoleIdentifiers = array_diff($userRoleObject->roleIdentifiers, $modelRoleObject->roleIdentifiers);
        if ( count($matchRoleIdentifiers) )
            return;

        $this->response = failure_message('Access denied');
    }
}