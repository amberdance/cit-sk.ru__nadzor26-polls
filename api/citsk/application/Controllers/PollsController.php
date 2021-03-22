<?php

namespace Citsk\Controllers;

use Citsk\Exceptions\DataBaseException;
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

    /**
     * @return void
     */
    public function getQuestions(): void
    {

        $this->dataResponse($this->model->getQuestions());
    }

    /**
     * @return void
     */
    public function getResult(): void
    {
        $accessToken = "CD0rK9TtLo3IU2t6psQyttwwxqo3xzSd";
        $ipAddress   = $this->model->getIpAddress();

        if ($_GET['token'] != $accessToken) {
            exit(http_response_code(404));
        }

        if ($ipAddress == "127.0.0.1" || $ipAddress == "95.173.149.135" || $ipAddress = '95.173.149.136') {
            $this->dataResponse($this->model->getResults());
        }

        exit(http_response_code(404));
    }

    /**
     * @return void
     */
    public function validate(): void
    {
        $this->dataResponse(['isVoted' => $this->model->isVotedBefore()]);
    }

    /**
     * @return void
     */
    public function vote(): void
    {

        try {
            $voteId = $this->model->addVote($_POST['companyId']);

            array_walk($_POST['scores'], function ($item) use ($voteId) {
                $this->model->addScore(array_merge(['voteId' => $voteId], $item));
            });

            $this->successResponse(['id' => $voteId]);
        } catch (DataBaseException $e) {
            if ($e->getCode() == 102) {
                $this->errorResponse(102, $this->model->getIpAddress());
            }
        }
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
