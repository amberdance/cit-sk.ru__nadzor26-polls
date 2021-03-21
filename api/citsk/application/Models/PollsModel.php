<?php

namespace Citsk\Models;

use Citsk\Models\DatabaseModel;
use Citsk\Models\Structure\CommonStructure;

final class PollsModel extends DatabaseModel
{

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
        return [
            'groups'    => $this->getQuestionGroups()->structure,
            'items' => (new CommonStructure($this->setDbTable("questionList")->select("id, label")->setSorting(['groupId' => 'asc'])->getRows()))->structure,
        ];
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
     * @return CommonStructure
     */
    private function getQuestionGroups(): CommonStructure
    {
        return new CommonStructure($this->setDbTable("questionGroups")->select("id, label")->setSorting(['id' => 'asc'])->getRows());
    }
}
