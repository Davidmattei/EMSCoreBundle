<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table;

use EMS\CoreBundle\Core\Table\Column\Columns;
use EMS\CoreBundle\Core\Table\Type\TableTypeInterface;

interface TableInterface
{
    public function getColumns(): Columns;
    public function getData(): array;
    public function getType(): TableTypeInterface;
}