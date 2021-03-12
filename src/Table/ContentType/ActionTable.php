<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Table\ContentType;

use EMS\CoreBundle\Core\Table\Column\Columns;
use Doctrine\ORM\QueryBuilder;
use EMS\CoreBundle\Core\Table\Type\DoctrineQueryBuilderTable;
use EMS\CoreBundle\Entity\ContentType;
use EMS\CoreBundle\Repository\TemplateRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ActionTable extends DoctrineQueryBuilderTable
{
    private TemplateRepository $actionRepository;

    public function __construct(TemplateRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;
    }

    public function getQueryBuilder(Columns $columns, array $options): QueryBuilder
    {
        $qb = $this->actionRepository->createQueryBuilder('a');
        $qb
            ->select('a.name as name')
            ->addSelect('a.renderOption as type')
            ->andWhere('a.contentType = :contentType')
            ->orderBy('a.orderKey', 'ASC')
            ->setParameter('contentType', $options['content_type']);

        return $qb;
    }

    public function configureColumns(Columns $columns, array $options): void
    {
        $columns
            ->add(Columns::TYPE_STRING, 'name')
            ->add(Columns::TYPE_STRING, 'type')
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setRequired(['content_type'])
            ->setAllowedTypes('content_type', ContentType::class);
    }

    public function getName(): string
    {
        return 'contentType-action';
    }
}