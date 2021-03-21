<?php

namespace Citsk\Library;

use Citsk\Exceptions\DataBaseException;
use PDO;
use PDOException;
use PDOStatement;

final class PDOBase
{

    /**
     * @return PDO
     */
    public function getInstance(): PDO
    {

        try {
            $config   = require "{$_SERVER['DOCUMENT_ROOT']}/api/citsk/config/database.php";
            $instance = new PDO("mysql:host={$config['host']};port={$config['port']};dbname={$config['db_name']}; charset={$config['char_set']}", $config['user'], $config['password']);
            $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $instance;
        } catch (PDOException $e) {
            throw new DataBaseException($e->getMessage());
        }
    }

    /**
     * @param string $query
     * @param array|null $args
     * @param bool $isReturnPDO
     *
     * @return mixed PDOStatement or PDO
     */
    public function executeQuery(string $query, ?array $args = null, $isReturnPDO = false)
    {

        try {
            $instance  = $this->getInstance();
            $statement = $instance->prepare($query);
            $statement->execute($args);

            return $isReturnPDO ? $instance : $statement;
        } catch (PDOException $e) {
            if (strripos($e->getMessage(), "only_full_group_by")) {
                $this->executeQuery("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
                $statement->execute($args);

                return $isReturnPDO ? $instance : $statement;
            }

            if (strripos($e->getMessage(), 'Duplicate entry')) {
                throw new DataBaseException('Duplicate entry', 102);
            }

            if (strripos($e->getMessage(), 'Data too long')) {
                throw new DataBaseException('Data too long', 111);
            }

            throw new DataBaseException($e->getMessage());
        }
    }

    /**
     * @param string|null $query
     * @param array|null $args
     * @param PDO|null $instance
     *
     * @return int
     */
    public function getLastInsertId(?string $query = null, ?array $args = null, ?PDO $instance = null): int
    {
        try {

            if ($instance) {
                return (int) $instance->lastInsertId();
            }

            $instance  = $this->getInstance();
            $statement = $instance->prepare($query);

            if ($args) {
                $statement->execute($args);
            }

            return (int) $instance->lastInsertId();

        } catch (PDOException $e) {
            if (strripos($e->getMessage(), 'Duplicate entry')) {
                throw new DataBaseException('Duplicate entry', 102);
            }

            throw new DataBaseException($e->getMessage());
        }
    }

    /**
     * @param string|null $query
     * @param int $fetchType
     * @param PDOStatement|null $statement
     *
     * @return mixed
     */
    public function fetch(?string $query, int $fetchType, ?PDOStatement $statement = null)
    {
        try {
            return $statement
            ? $statement->fetch($fetchType)
            : $this->getInstance()->query($query)->fetch($fetchType);
        } catch (PDOException $e) {
            throw new DataBaseException($e->getMessage());
        }
    }

    /**
     * @param string|null $query
     * @param int $fetchType
     * @param PDOStatement|null $statement
     *
     * @return array|null
     */
    public function fetchAll(?string $query, int $fetchType, ?PDOStatement $statement = null): ?array
    {

        try {
            return $statement
            ? $statement->fetchAll($fetchType)
            : $this->getInstance()->query($query)->fetchAll($fetchType);
        } catch (PDOException $e) {
            throw new DataBaseException($e->getMessage());
        }

    }

    /**
     * @param string|null $query
     * @param PDOStatement|null $statement
     *
     * @return string|null
     */
    public function fetchColumn(?string $query, ?PDOStatement $statement = null): ?string
    {
        try {

            return $statement
            ? $statement->fetchColumn()
            : $this->getInstance()->query($query)->fetchColumn();
        } catch (PDOException $e) {
            throw new DataBaseException($e->getMessage());
        }
    }

    /**
     * @param string|null $query
     * @param PDOStatement|null $statement
     *
     * @return int
     */
    public function rowCount(?string $query, ?PDOStatement $statement = null): int
    {

        try {

            if ($statement) {
                return $statement->rowCount();
            }

            $instance  = $this->getInstance();
            $statement = $instance->prepare($query);
            $statement->execute();

            return $statement->rowCount();
        } catch (PDOException $e) {
            throw new DataBaseException($e->getMessage());
        }

    }
}
