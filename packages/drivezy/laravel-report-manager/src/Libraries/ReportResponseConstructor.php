<?php

namespace Drivezy\LaravelReportManager\Libraries;

use Drivezy\LaravelRecordManager\Models\Column;
use Drivezy\LaravelRecordManager\Models\DataModel;

/**
 * Class ReportScriptConstructor
 * @package Drivezy\LaravelReportManager\Libraries
 */
class ReportResponseConstructor {

    /**
     * @var null
     */
    public $request = null;

    /**
     * @var null
     */
    public $response = null;

    /**
     * @var null
     */
    private $stats = null;

    /**
     * @var null
     */
    private $dictionary = null;

    /**
     * @var null
     */
    private $data = null;

    /**
     * ReportScriptConstructor constructor.
     * @param $request
     */
    public function __construct ($request) {
        $this->request = $request;
        $this->response = failure_message('Something went wrong');

        $this->initializeConstruction();
    }

    /**
     * initialize report response construction
     */
    private function initializeConstruction () {
        $this->constructScript();
        $this->setStats();
        $this->setDictionary();
        $this->getReportData();
        $this->setResponse();
    }

    /**
     * construct query by attaching group, aggregation, filters, order, limit, offset
     */
    private function constructScript () {
        if ( $this->request->grouping_columns || $this->request->aggrigate_columns || $this->request->export ) {
            $groupColumns = $this->request->grouping_columns ? $this->request->grouping_columns : null;
            $groupBy = $this->request->grouping_columns ? ' GROUP BY ' . $this->request->grouping_columns : null;

            $aggregationString = !$this->request->aggrigate_columns || !$this->request->grouping_columns ? $groupColumns . $this->request->aggrigate_columns : $this->request->aggrigate_columns . ',' . $groupColumns;

            $this->request->script = "SELECT " . $aggregationString . " FROM(" . $this->request->script . ") a WHERE " . $this->request->filter_query . $groupBy;

            return;
        }

        $this->request->script = "SELECT * FROM(" . $this->request->script . ") a WHERE " . $this->request->filter_query . ' ORDER BY ' . $this->request->order . ' LIMIT ' . $this->request->limit . ' OFFSET ' . ( $this->request->page - 1 ) * $this->request->limit;
    }

    /**
     * get stats data
     */
    private function setStats () {
        $getRecordCount = ReportUtilities::runDBScript($this->request->script)[0]->count;

        $this->stats = (object) [
            'total'  => $getRecordCount,
            'page'   => $this->request->page,
            'record' => $this->request->limit,
        ];
    }

    /**
     * get column details of script
     */
    private function setDictionary () {
        $this->dictionary = Column::where('source_type', md5(DataModel::class))
            ->where('model_id', $this->request->model_id)->get();
    }

    /**
     * run final script and fetch the data
     */
    private function getReportData () {
        $this->data = ReportUtilities::runDBScript($this->request->script);
    }

    /**
     * set return response object
     */
    private function setResponse () {
        $response = (object) [
            'data'       => $this->data,
            'stats'      => $this->stats,
            'dictionary' => $this->dictionary,
        ];

        $this->response = success_message($response);

        if ( $this->request->export )
            $this->setExportEvent();
    }

    /**
     * set event to export data in excel sheet and mail to logged in user
     */
    private function setExportEvent () {

    }
}