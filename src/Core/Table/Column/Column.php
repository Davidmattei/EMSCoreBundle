<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table\Column;

final class Column
{
    private string $name;
    private string $label;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}