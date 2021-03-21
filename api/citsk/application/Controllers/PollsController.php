<?php

namespace Citsk\Controllers;

use Citsk\Interfaces\Controllerable;
use Citsk\Models\PollsModel;

final class PollsController extends ControllerBase implements Controllerable
{

    /**
     * @var PollsModel
     */
    protected $model;

    public function initializeController(): void
    {
        $this->model = new PollsModel;
    }

    /**
     * @return void
     */
    public function getCompanies(): void
    {

        $this->dataResponse($this->model->getCompanies());
    }

    public function getQuestions(): void
    {

        $this->dataResponse($this->model->getQuestions());
    }

    /**
     * @return void
     */
    public function addFromCsv(): void
    {

        $companies = $this->model->getCompaniesFromCsv($_SERVER['DOCUMENT_ROOT'] . '/api/citsk/application/list.csv');

        array_walk($companies, function ($item) {
            $params = [
                'label'   => trim($item[0]),
                'inn'     => trim($item[1]),
                'address' => trim($item[2]),
                'ogrn'    => trim($item[3]),
            ];

            $this->model->addCompany($params);
        });
    }
}
