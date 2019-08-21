<?php

Route::group(['namespace' => 'Drivezy\LaravelReportManager\Controllers',
              'prefix'    => 'api/record'], function () {

    Route::resource('modelParameters', 'ModelParameterController');
    Route::get('getModelParameters/{id}', 'ModelParameterController@getModelParameters');
    Route::get('getModelColumns/{id}', 'ReportingQueryController@getModelColumns');
    Route::match(['get', 'post'], 'getReportData/{id}', 'ReportingQueryController@getReportData');
});