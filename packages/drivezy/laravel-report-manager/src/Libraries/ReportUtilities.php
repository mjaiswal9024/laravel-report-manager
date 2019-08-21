<?php

namespace Drivezy\LaravelReportManager\Libraries;

use Drivezy\LaravelAccessManager\Models\RoleAssignment;
use Drivezy\LaravelRecordManager\Models\Column;
use Drivezy\LaravelRecordManager\Models\DataModel;
use Drivezy\LaravelRecordManager\Models\SystemScript;
use Drivezy\LaravelReportManager\Models\ModelParameters;
use Drivezy\LaravelReportManager\Models\ParameterDefaults;
use Drivezy\LaravelUtility\Library\DateUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class contains utility methods required for reporting framework
 *
 * Class ReportUtilities
 * @package Drivezy\LaravelReportManager\Libraries
 */
class ReportUtilities {

    /**
     * key string to save parameter default value in cache
     * @var string
     */
    private static $parameterDefaultCacheKey = 'model-parameter-default-key';

    /**
     * @var string
     */
    private static $modelRoleCacheKey = 'model-role-key-';

    /**
     * @var string
     */
    private static $userRoleCacheKey = 'user-role-key-';

    /**
     * parse the default parameter value of a report.
     *
     * @param $defaultValue string
     * @return mixed
     */
    public static function convertParameterDefaultValues ($defaultValue) {
        $parsePhrase = self::getParameterDefaults();

        foreach ( $parsePhrase as $literal => $value ) {
            if ( $defaultValue === $literal ) {
                return eval($value);
            }
        }

        return $defaultValue;
    }

    /**
     * get parameter default value from cache
     *
     * @return mixed|object
     */
    public static function getParameterDefaults () {
        $cacheObject = Cache::get(self::$parameterDefaultCacheKey);

        if ( !$cacheObject )
            return self::setParameterDefaults();

        if ( DateUtil::getDateTimeDifference($cacheObject->refreshed_time, DateUtil::getDateTime()) > 24 * 60 * 60 * 7 )
            return self::setParameterDefaults();

        return $cacheObject;
    }

    /**
     * store parameter default value in cache
     *
     * @return object
     */
    public static function setParameterDefaults () {
        $defaultValues = ParameterDefaults::all();

        $castValues = (object) [];

        foreach ( $defaultValues as $values )
            $castValues->{$values->name} = $values->backend_parse_phrase;

        $castValues->refresh_time = DateUtil::getDateTime();

        Cache::forever(self::$parameterDefaultCacheKey, $castValues);

        return $castValues;
    }

    /**
     * get model roles object from cache
     *
     * @param $id
     * @return mixed|object
     */
    public static function getModelRoleObject ($id) {
        $cacheObject = Cache::get(self::$modelRoleCacheKey . $id);

        if ( !$cacheObject )
            return self::setModelRoleObject($id);

        if ( DateUtil::getDateTimeDifference($cacheObject->refreshed_time, DateUtil::getDateTime()) > 24 * 60 * 60 )
            return self::setModelRoleObject($id);

        return $cacheObject;
    }

    /**
     * store model roles object in cache
     *
     * @param $id
     * @return object
     */
    public static function setModelRoleObject ($id) {
        $modelRoles = RoleAssignment::with('role')->where('source_type', md5(DataModel::class))
            ->where('source_id', $id)
            ->get();

        $roleObj = (object) [];
        $rolesArr = [];
        $roleIdentifiers = [];

        foreach ( $modelRoles as $role ) {
            array_push($rolesArr, $role->role_id);
            array_push($roleIdentifiers, $role->role->identifier);
        }

        $roleObj->roles = $rolesArr;
        $roleObj->roleIdentifiers = $roleIdentifiers;
        $roleObj->refresh_time = DateUtil::getDateTime();

        Cache::forever(self::$modelRoleCacheKey . $id, $roleObj);

        return $roleObj;
    }

    /**
     * get user role object from cache
     *
     * @return mixed|object
     */
    public static function getUserRoleObject () {
        $userId = Auth::id();

        if ( !$userId )
            return self::setUserRoleObject();

        $cacheObject = Cache::get(self::$userRoleCacheKey . $userId);

        if ( !$cacheObject )
            return self::setUserRoleObject();

        if ( !isset($cacheObject->refreshed_time) )
            return self::setUserRoleObject();

        if ( DateUtil::getDateTimeDifference($cacheObject->refreshed_time, DateUtil::getDateTime()) > 24 * 60 * 60 )
            return self::setUserRoleObject();

        return $cacheObject;
    }

    /**
     * store user role object to cache
     *
     * @return object
     */
    public static function setUserRoleObject () {
        $userId = Auth::id();
        $roleObj = (object) [];
        $rolesArr = [];
        $roleIdentifiers = [];

        if ( !$userId ) {
            $roleObj->roles = $rolesArr;
            $roleObj->roleIdentifiers = $roleIdentifiers;

            return $roleObj;
        }

        $modelRoles = RoleAssignment::with('role')->where('source_type', md5(DataModel::class))
            ->where('source_id', $userId)
            ->get();

        foreach ( $modelRoles as $role ) {
            array_push($rolesArr, $role->role_id);
            array_push($roleIdentifiers, $role->role->identifier);
        }

        $roleObj->roles = $rolesArr;
        $roleObj->roleIdentifiers = $roleIdentifiers;
        $roleObj->refresh_time = DateUtil::getDateTime();

        Cache::forever(self::$userRoleCacheKey . $userId, $roleObj);

        return $roleObj;
    }

    /**
     * populate script variable parameters in dz_model_parameter_details table
     *
     * @param $modelId
     * @return array
     */
    public static function populateScriptParameters ($modelId) {
        $script = SystemScript::where('source_type', md5(DataModel::class))
            ->where('source_id', $modelId)->first();

        if ( !$script ) return failure_message('No script found.');

        preg_match_all("/#[^#]+#/", $script->script, $params);

        foreach ( $params[0] as $param ) {
            if ( $param === '#user_id#' )
                continue;

            $displayName = ucwords(str_replace('#', '', ( str_replace('_', ' ', $param) )));
            ModelParameters::where('model_id', $modelId)
                ->firstOrCreate(['model_id'     => $modelId,
                                 'parameter'    => str_replace('#', '', $param),
                                 'display_name' => $displayName]);
        }

        return success_message('Parameters updated successfully');
    }

    /**
     * populate script column details in dz_column_details table
     *
     * @param $modelId
     * @return array
     */
    public static function populateScriptColumns ($modelId) {
        $script = SystemScript::where('source_type', md5(DataModel::class))
            ->where('source_id', $modelId)->first();

        if ( !$script ) return failure_message('No script found.');

        $params = ModelParameters::where('model_id', $modelId)->get();

        $query = $script->script;

        $query = str_replace('#user_id#', Auth::id(), $query);

        if ( count($params) == 0 ) {
            foreach ( $params as $param ) {
                if ( $param->default_value === null ) return failure_message('Parameter default value missing.');

                $value = self::convertParameterDefaultValues($param->default_value);
                $query = str_replace('#' . $param->name . '#', $value, $query);
            }
        }

        try {
            $data = self::runDBScript($query . ' LIMIT 1');
        } catch ( QueryException $e ) {
            return failure_message('Script issue.');
        }

        if ( count($data) == 0 ) return failure_message('No records found for default params.');

        foreach ( $data[0] as $key => $value ) {
            Column::where('model_id', $modelId)
                ->firstOrCreate(['source_type'  => md5(DataModel::class),
                                 'source_id'    => $modelId,
                                 'name'         => strtolower($key),
                                 'display_name' => ucwords(str_replace('_', ' ', $key))]);
        }

        return success_message('Columns populated.');
    }

    /**
     * run DB script
     *
     * @param $script
     * @return array
     */
    public static function runDBScript ($script) {
        return DB::select(DB::raw($script));
    }
}