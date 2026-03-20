<?php

declare(strict_types=1);

namespace Coleo\Model;

use Doctrine\DBAL\Connection;

class ModelFactory implements ModelFactoryInterface
{
    public function __construct(protected Connection $conn, protected string $namespace)
    {
    }

    public function create(string $modelName): AbstractModel
    {
        $classname = $this->namespace . ucfirst($modelName);
        if (
            class_exists($classname)
            && in_array(AbstractModel::class, class_parents($classname))
        ) {
            return new $classname($this->conn);
        } else {
            throw new \Exception($classname . ": class not found or it is not model", 1);
        }
    }
}
