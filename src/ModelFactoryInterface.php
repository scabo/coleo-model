<?php

namespace Coleo\Model;

interface ModelFactoryInterface
{
    public function create(string $modelName): AbstractModel;
}
