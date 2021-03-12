<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Table;

use Doctrine\ORM\QueryBuilder;
use EMS\CoreBundle\Core\Table\Column\Columns;
use EMS\CoreBundle\Core\Table\Type\AjaxDoctrineQueryBuilderTable;
use EMS\CoreBundle\Repository\RevisionRepository;

final class RevisionTable extends AjaxDoctrineQueryBuilderTable
{
    private RevisionRepository $revisionRepository;

    public function __construct(RevisionRepository $revisionRepository)
    {
        $this->revisionRepository = $revisionRepository;
    }

    public function getName(): string
    {
        return 'revision';
    }

    public function getQueryBuilder(): QueryBuilder
    {
        $qb = $this->revisionRepository->createQueryBuilder('r');
        $qb
            ->select('r.id, r.sha1, c.name as contentType')
            ->join('r.contentType', 'c');

        return $qb;
    }

    public function configureColumns(Columns $columns, array $options): void
    {
        $columns
            ->add(Columns::TYPE_STRING, 'id')
            ->add(Columns::TYPE_STRING, 'sha1')
            ->add(Columns::TYPE_STRING, 'contentType')
        ;
    }
}