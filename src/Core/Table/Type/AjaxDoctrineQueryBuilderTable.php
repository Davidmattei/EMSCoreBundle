<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table\Type;

use Doctrine\ORM\QueryBuilder;
use EMS\CoreBundle\Core\Table\Ajax\AjaxRequest;
use EMS\CoreBundle\Core\Table\Type\AbstractTableType;

abstract class AjaxDoctrineQueryBuilderTable extends AbstractTableType implements AjaxTableTypeInterface
{
    abstract public function getQueryBuilder(): QueryBuilder;

    public function getAjaxData(AjaxRequest $ajaxRequest): array
    {
        $qbCount = $this->getQueryBuilder();
        $rootAliases = $qbCount->getRootAliases();
        $totalResult = $qbCount->select(sprintf('count(%s.id) as count', $rootAliases[0]))->getQuery()->getResult();

        $qb = $this->getQueryBuilder();
        $qb
            ->setFirstResult($ajaxRequest->getStart())
            ->setMaxResults($ajaxRequest->getLength());

        return [
            'recordsTotal' => $totalResult[0]['count'] ?? 0,
            'recordsFiltered' => $totalResult[0]['count'] ?? 0,
            'data' => $qb->getQuery()->getResult()
        ];
    }
}