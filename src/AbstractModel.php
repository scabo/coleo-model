<?php

declare(strict_types=1);

namespace Coleo\Model;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractModel
{
    public function __construct(protected Connection $conn)
    {
    }

    public function add(array $options)
    {
        if (empty($options)) {
            throw new \Exception("No options for update", 1);
        }

        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder->insert($this->getTable());

        $columns = array_keys($options);
        foreach ($columns as $column) {
            $queryBuilder->setValue($column, '?');
        }

        $values = array_values($options);
        foreach ($values as $key => $value) {
            $queryBuilder->setParameter($key, $value);
        }

        $result = $queryBuilder->executeQuery();
        return $result->fetchNumeric();
    }

    public function update(int $id, array $options)
    {
        if (empty($options)) {
            throw new \Exception("No options for update", 1);
        }

        $builder = $this->getQueryBuilder();
        $builder->update($this->getTable());

        $columns = array_keys($options);
        foreach ($columns as $column) {
            $builder->set($column, '?');
        }

        $values = array_values($options);
        foreach ($values as $key => $value) {
            $builder->setParameter($key, $value);
        }

        $builder->where('id = ?')->setParameter(count($values), $id, ParameterType::INTEGER);
        $result = $builder->executeQuery();

        return $result->fetchNumeric();
    }

    public function delete(int $id)
    {
        return $this->getQueryBuilder()->
            delete($this->getTable())->
            where('id = ?')->
            setParameter(0, $id, ParameterType::INTEGER)->
            executeQuery()->
            fetchOne();
    }

    public function findById($id)
    {
        return $this->getQueryBuilder()->
            select("*")->
            from($this->getTable())->
            where("id = ?")->
            setParameter(0, $id, ParameterType::INTEGER)->
            executeQuery()->
            fetchAssociative();
    }

    public function selectAll($offset = 0, $limit = 0)
    {
        $builder = $this->getQueryBuilder()->
            select("*")->
            from($this->getTable())->
            setFirstResult($offset);

        if ($limit > 0) {
            $builder->setMaxResults($limit);
        }

        return $builder->executeQuery()->fetchAllAssociative();
    }

    protected function query($sql, $values = [])
    {
        $stmt = $this->conn->prepare($sql);
        foreach ($values as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        return $this->conn->createQueryBuilder();
    }

    abstract protected function getTable(): string;
}
