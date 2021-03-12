<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table\Type;

use Doctrine\ORM\QueryBuilder;
use EMS\CoreBundle\Core\Table\Column\Columns;

abstract class DoctrineQueryBuilderTable extends AbstractTableType
{
    abstract public function getQueryBuilder(Columns $columns, array $options): QueryBuilder;

    public function getData(Columns $columns, array $options): array
    {
        $qb = $this->getQueryBuilder($columns, $options);

        return $qb->getQuery()->getResult();
    }
}