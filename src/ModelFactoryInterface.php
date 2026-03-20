<?php

declare(strict_types=1);

namespace Coleo\Model;

interface ModelFactoryInterface
{
    public function create(string $modelName): AbstractModel;
}
