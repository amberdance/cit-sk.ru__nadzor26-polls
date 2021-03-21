<?php
namespace Citsk\Models;

use Citsk\Exceptions\DataBaseException;
use Citsk\Library\PDOBase;
use Exception;
use PDO;

/**
 *
 * @property string $queryString
 * @property string $dbTable
 *
 */
class DatabaseModel
{

    /**
     * @var PDOBase
     */
    private $dbConnection;

    /**
     * @var \PDO
     */
    private $PDOInstance;

    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * @var string
     */
    private $queryString;

    /**
     * @var string
     */
    private $dbTable;

    /**
     * @var bool
     */
    private $isTransactionBegin = false;

    /**
     * @var array
     */
    private $transactions = [];

    /**
     * @var int
     */
    private $lastInsertedId;

    /**
     * @var array
     */
    private $args = [];

    /**
     * @var bool
     */
    private $isSkipArgs = false;

    public function __construct()
    {

        $this->dbConnection = new PDOBase;
    }

    /**
     * @param string $dbTable
     *
     * @return DatabaseModel
     */
    public function setDbTable(string $dbTable): DatabaseModel
    {
        $this->dbTable = $dbTable;

        return $this;
    }

    /**
     * @param null $fieldsFields
     * @param array|null $filter
     * @param array|null $join
     *
     * @return DatabaseModel
     */
    public function select($fieldsFields = null, ?array $filter = null, ?array $join = null): DatabaseModel
    {

        $this->setFields($fieldsFields)
            ->setJoinFieldsIfExists($join)
            ->setFilterFieldsIfExists($filter);

        if (!$this->isSkipArgs) {
            $this->setArgs($filter);
        }

        return $this;
    }

    /**
     * @param string $query
     * @param array $args
     *
     * @return DatabaseModel
     */
    public function customQuery(string $query, array $args = []): DatabaseModel
    {

        $this->queryString = $query;
        $this->args        = $args;

        $this->runQuery();

        return $this;
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->runQuery()->rowCount(null, $this->statement);
    }

    /**
     * @param int $fetchStyle
     *
     * 2- PDO::FETCH_ASSOC
     * 5- PDO::FETCH_OBJ
     * 7 PDO::FETCH_COLUMN
     *
     * @return array|object|null
     */
    public function getRow(int $fetchType = 2)
    {

        $result = $this->runQuery()->fetch(null, $fetchType, $this->statement);

        return $result ? $result : null;

    }

    /**
     * @param int $fetchStyle
     * 2- PDO::FETCH_ASSOC
     * 5- PDO::FETCH_OBJ
     * 7 PDO::FETCH_COLUMN
     *
     * @return array|null
     */
    public function getRows(int $fetchType = 2): ?array
    {
        $this->runQuery();

        return $this->getConnection()->fetchAll(null, $fetchType, $this->statement);

    }

    /**
     * @return string|null
     */
    public function getColumn(): ?string
    {

        return $this->runQuery()->fetchColumn(null, $this->statement);
    }

    /**
     * @return void
     */
    public function startTransaction(): void
    {
        $this->isTransactionBegin = true;
    }

    /**
     * @return void
     *
     * @throws DataBaseException
     */
    public function executeTransaction(): void
    {

        try {
            $this->PDOInstance = $this->getConnection()->getInstance();
            $this->PDOInstance->beginTransaction();

            foreach ($this->transactions as $transaction) {
                $statement = $this->PDOInstance->prepare($transaction['query']);
                $statement->execute($transaction['args']);
            }

            $this->PDOInstance->commit();

        } catch (Exception $e) {
            $this->PDOInstance->rollBack();

            throw new DataBaseException($e->getMessage());
        }
    }

    /**
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->queryString;
    }

    /**
     * @return int
     */
    public function getInsertedId(): int
    {
        return $this->lastInsertedId;
    }

    /**
     * @return string|null
     */
    public function getDbTable(): ?string
    {
        return $this->dbTable;
    }

    /**
     * @param array|null $args
     *
     * @return DatabaseModel
     */
    public function setArgs(?array $args): DatabaseModel
    {
        if (!$args) {
            return $this;
        }

        unset($this->args);

        foreach ($args as $key => $value) {
            if (is_numeric(strpos($key, ":"))) {
                $this->skipArgs();
                $this->args[$key] = $value;
            } else {
                $this->args[$this->getPlaceHolder($key)] = $value;
            }
        }

        return $this;
    }

    /**
     * @return DatabaseModel
     */
    public function skipArgs(): DatabaseModel
    {
        $this->isSkipArgs = true;

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return DatabaseModel
     */
    public function setSorting(array $fields): DatabaseModel
    {

        $sortStroke = "ORDER BY ";

        foreach ($fields as $key => $field) {
            $sortStroke .= "$key $field, ";
        }

        $sortStroke = trim($sortStroke, ', ');

        $this->queryString .= " $sortStroke";

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return DatabaseModel
     */
    public function setGrouping(array $fields): DatabaseModel
    {

        $groupStroke = null;

        foreach ($fields as $field) {
            $groupStroke .= "GROUP BY $field, ";
        }

        $groupStroke = trim($groupStroke, ', ');

        $this->queryString .= " $groupStroke";

        return $this;
    }

    /**
     * @param int|null $limiter
     *
     * @return DatabaseModel
     */
    public function setLimit(?int $limiter): DatabaseModel
    {
        if (!$limiter) {
            return $this;
        }

        $this->queryString .= " LIMIT $limiter";

        return $this;

    }

    /**
     * @param array $fields
     *
     * @return DatabaseModel
     */
    public function add(array $fields): DatabaseModel
    {

        $this->setInsertFileds($fields);

        if (!$this->isSkipArgs) {
            $this->setArgs($fields);
        }

        if ($this->isTransactionBegin) {

            $this->transactions[] = [
                'query' => $this->queryString,
                'args'  => $this->args,
            ];

            return $this;
        }

        $this->runQuery(true);
        $this->lastInsertedId = $this->PDOInstance->lastInsertId();

        if (!boolval($this->lastInsertedId)) {
            throw new DataBaseException("Insert failed");
        }

        return $this;
    }

    /**
     * @param array $updateFields
     * @param array $filter
     * @param array|null $join
     *
     * @return DatabaseModel
     */
    public function update(array $updateFields, array $filter = [], ?array $join = null): DatabaseModel
    {
        $joinString = null;

        if ($join) {
            $joinString = $this->setJoinFieldsIfExists($join, true);
        }

        $this->setUpdateFields($updateFields, $joinString)->setFilterFieldsIfExists($filter);

        if (!$this->isSkipArgs) {
            $this->setArgs(array_merge($updateFields, $filter));
        }

        if ($this->isTransactionBegin) {
            $this->transactions[] = [
                'query' => $this->queryString,
                'args'  => $this->args,
            ];

            return $this;
        }

        $this->runQuery();

        return $this;

    }

    /**
     * @param array|null $filter
     *
     * @return DatabaseModel
     */
    public function delete(?array $filter = null): DatabaseModel
    {
        $this->setFields(null, 'DELETE')->setFilterFieldsIfExists($filter);

        if (!$this->isSkipArgs) {
            $this->setArgs($filter);
        }

        if ($this->isTransactionBegin) {

            $this->transactions[] = [
                'query' => $this->queryString,
                'args'  => $this->args,
            ];

            return $this;

        } else {
            $this->runQuery();
        }

        return $this;
    }

    /**
     * @param string|array $deleteFields
     * @param array|null $filter
     * @param array $join
     *
     * @return DatabaseModel
     */
    public function deleteWithJoin($deleteFields, ?array $filter = null, array $join): DatabaseModel
    {
        $this->setFields($deleteFields, 'DELETE')
            ->setJoinFieldsIfExists($join)
            ->setFilterFieldsIfExists($filter);

        if (!$this->isSkipArgs) {
            $this->setArgs($filter);
        }

        if ($this->isTransactionBegin) {

            $this->transactions[] = [
                'query' => $this->queryString,
                'args'  => $this->args,
            ];

            return $this;

        } else {
            $this->runQuery();
        }

        return $this;
    }

    /**
     * @return void
     */
    public function closeConnection(): void
    {
        $this->dbConnection       = null;
        $this->statement          = null;
        $this->queryString        = null;
        $this->dbTable            = null;
        $this->isTransactionBegin = false;
    }

    /**
     * @return PDOBase
     */
    private function getConnection()
    {

        return $this->dbConnection ?? new PDOBase;

    }

    /**
     * @param bool $isReturnPDOInstance
     *
     * @return PDOBase
     */
    private function runQuery(bool $isReturnPDOInstance = false): PDOBase
    {

        if ($isReturnPDOInstance) {
            $this->PDOInstance = $this->getConnection()->executeQuery($this->queryString, $this->args, true);
        } else {
            $this->statement = $this->getConnection()->executeQuery($this->queryString, $this->args);
        }

        $this->isSkipArgs = false;

        return $this->dbConnection;
    }

    /**
     * @param array|string|null $fields
     * @param string $action
     *
     * @return DatabaseModel
     */
    private function setFields($fields, string $action = "SELECT"): DatabaseModel
    {
        $queryFields = null;

        if (is_array($fields) && !empty($fields)) {

            foreach ($fields as $key => $field) {
                $key = is_string($key) ? "$key as" : '';
                $queryFields .= "$key $field, ";
            }

            $queryFields = trim($queryFields, ', ');

        } elseif (is_string($fields)) {
            $queryFields = $fields;
        } elseif ($action == 'SELECT') {
            $queryFields = "*";
        }

        $this->queryString = "$action $queryFields FROM {$this->dbTable}";

        return $this;
    }

    /**
     * @param array $rawFields
     *
     * @return DatabaseModel
     */
    private function setInsertFileds(array $rawFields): DatabaseModel
    {

        $fields = [];

        array_walk($rawFields, function ($value, $key) use (&$fields) {
            $param        = $this->isSkipArgs ? $value : $this->getPlaceHolder($key);
            $fields[$key] = $param;
        });

        $keys   = implode(', ', array_keys($fields));
        $values = implode(', ', array_values($fields));

        $this->queryString = "INSERT INTO {$this->dbTable} ($keys) VALUES ($values)";

        return $this;

    }

    /**
     * @param array $rawFields
     * @param string $joinUpdateString
     *
     * @return DatabaseModel
     */
    private function setUpdateFields(array $rawFields, ?string $joinUpdateString = ""): DatabaseModel
    {

        $fields = "";

        array_walk($rawFields, function ($value, $key) use (&$fields) {
            $param = $this->isSkipArgs ? $value : $this->getPlaceHolder($key);
            $fields .= "$key =  $param, ";
        });

        $fields = trim($fields, ', ');

        $this->queryString = "UPDATE {$this->dbTable} $joinUpdateString SET $fields";

        return $this;
    }

    /**
     * @param array|null $filter
     *
     * @return DatabaseModel
     */
    private function setFilterFieldsIfExists(?array $filter = null): DatabaseModel
    {

        if (!$filter) {
            return $this;
        }

        $filterStroke = 'WHERE ';
        $glue         = ' AND ';
        $comparsion   = null;

        foreach ($filter as $key => $field) {
            if (preg_match("/^[^\w\s:']+/", $field, $match)) {
                $comparsion = $this->getFilterComparsion($match[0]);

                $field = ($match[0] == "()")
                ? str_replace($match[0], null, "($field)")
                : str_replace($match[0], null, $field);

            } else {
                $comparsion = "=";
            }

            $param = $this->isSkipArgs ? $field : $this->getPlaceHolder($key);
            $filterStroke .= "$key $comparsion $param $glue";
        }

        $filterStroke = str_replace('OR AND', "OR", trim($filterStroke, "$glue"));

        $this->queryString .= " $filterStroke";

        return $this;
    }

    /**
     * @param array|null $join
     * @param bool $isJoinUpdate
     *
     * @return DatabaseModel|string
     */
    private function setJoinFieldsIfExists(?array $join = null, bool $isJoinUpdate = false)
    {

        if (!$join) {
            return $this;
        } else {
            $joinStroke = "";
            $glue       = "LEFT JOIN";

            foreach ($join as $key => $field) {
                if ($key == 'inner') {
                    $glue = 'JOIN';

                    continue;
                }

                $joinStroke .= "$glue $key ON $field ";
            }

            $joinStroke = trim($joinStroke, 'ON ');

            if ($isJoinUpdate) {
                return $joinStroke;
            } else {
                $this->queryString .= " $joinStroke";
            }

            return $this;
        }
    }

    /**
     * @param string $param
     *
     * @return string
     */
    private function getPlaceHolder(string $param): string
    {
        return ":" . preg_replace("/[\W_]/", "_$1", $param);

    }

    /**
     * convert symbols tags to condition query statement
     *
     * @param  string $comparsion
     *
     * @return string
     */
    private function getFilterComparsion(string $comparsion): string
    {

        switch ($comparsion) {
            case '<>':
                return "BETWEEN";

            case "!!":
                return "IS NOT NULL";

            case "()":
                return "IN";

            case "!()":
                return "NOT IN";

            case "%%":
                return "LIKE";

            case "<=":
                return "<=";

            case ">=":
                return ">= ";

            case "!=":
                return "!=";

            default:
                return '';
        }
    }
}
