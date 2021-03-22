<?php

namespace Citsk\Models;

use Citsk\Models\DatabaseModel;
use Citsk\Models\Structure\CommonStructure;

final class PollsModel extends DatabaseModel
{

    /**
     * @return bool
     */
    public function isVotedBefore(): bool
    {
        return boolval($this->setDbTable("votingList")->select("id", ['ip' => $this->getIpAddress()])->getColumn());
    }

    /**
     * @return CommonStructure
     */
    public function getCompanies(?array $filter = null): array
    {

        return $this->setDbTable("companyList")->select("id, label, inn, ogrn, address", $filter)->getRows();
    }

    /**
     * @return array
     */
    public function getQuestions(): array
    {

        $result = [];
        $groups = $this->getQuestionGroups();
        $this->setDbTable("questionList");

        foreach ($groups as $key => $item) {
            $result[$key]['group'] = [
                'id'    => $item['id'],
                'label' => $item['label'],
            ];

            $result[$key]['questions'] = $this->select("id, label", ['groupId' => $item['id']])->getRows();
        }

        return $result;
    }

    /**
     * @param array|null $filter
     *
     * @return array
     */
    public function getResults(?array $filter = null): array
    {

        $fields = [
            "vote.id",
            "vote.ip",
            "vote.isValid",
            "company.label",
            "ROUND(SUM(score.score) / (select count(id) from votingList WHERE companyId = company.id), 1)" => "score",
            "(select count(id) from votingList WHERE companyId = company.id)"                              => "count",
        ];

        $join = [
            "scoreList score"     => "score.voteId = vote.id",
            "companyList company" => "company.id = vote.companyId",
        ];

        return $this->setDbTable("votingList vote")->select($fields, $filter, $join)->setGrouping(['company.label'])->setSorting(['score' => 'desc'])->getRows();
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function addCompany(array $params): int
    {

        return $this->setDbTable("companyList")->add($params)->getInsertedId();
    }

    /**
     * @param int $companyId
     *
     * @return int
     */
    public function addVote(int $companyId): int
    {

        $ipAddress = $this->getIpAddress();

        $fields = [
            'companyId' => $companyId,
            'ip'        => $ipAddress,
            'isValid'   => intval($this->isRussianIpAddress()),
        ];

        return $this->setDbTable("votingList")->add($fields)->getInsertedId();
    }

    /**
     * @param int $voteId
     *
     * @return int
     */
    public function addScore(array $params): int
    {

        return $this->setDbTable("scoreList")->add(array_merge($params))->getInsertedId();
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function removeVote(int $id): void
    {

        $filter = [
            "vl.id" => $id,
        ];

        $join = [
            "scoreList sl" => "sl.voteId = vl.id",
        ];

        $this->setDbTable("votingList vl")->deleteWithJoin(["vl", "sl"], $filter, $join);
    }

    /**
     * @param string $dbTable
     * @param array $filter
     *
     * @return void
     */
    public function removeData(string $dbTable, array $filter): void
    {

        $this->setDbTable($dbTable)->delete($filter);
    }

    public function getIpAddress(): string
    {

        $ip = null;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * @param string|null $ip
     *
     * @return array
     */
    public function getGeoIp(?string $ip = null): array
    {

        $ip    = $ip ?? $this->getIpAddress();
        $isBot = preg_match(
            "~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i",
            $_SERVER['HTTP_USER_AGENT']
        );

        return !$isBot ? json_decode(file_get_contents("http://api.sypexgeo.net/G5Twm/json/$ip"), true) : [];
    }

    /**
     * @param string|null $ip
     *
     * @return bool
     */
    private function isRussianIpAddress(?string $ip = null): bool
    {
        return $this->getGeoIp($ip)['country']['iso'] == 'RU' ? true : false;
    }

    /**
     * @param string $src
     *
     * @return void
     */
    public function getCompaniesFromCsv(string $src): array
    {

        $csvData = file_get_contents($src);
        $lines   = explode(PHP_EOL, $csvData);
        $result  = [];

        foreach ($lines as $line) {
            $result[] = str_getcsv($line, ";");
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getQuestionGroups(): array
    {
        return $this->setDbTable("questionGroups")->select("id, label")->setSorting(['id' => 'asc'])->getRows();
    }
}
