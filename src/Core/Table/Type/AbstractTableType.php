<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table\Type;

use EMS\CoreBundle\Core\Table\Column\Columns;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractTableType implements TableTypeInterface
{
    public function configureColumns(Columns $columns, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
    }

    public function getData(Columns $columns, array $options): array
    {
        return [];
    }
}