<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table\Column;

final class Columns implements \IteratorAggregate
{
    private array $columns = [];

    public const TYPE_STRING    = 'string';
    public const TYPE_DATE      = 'date';
    public const TYPE_DATE_TIME = 'date_time';

    public function add(string $type, string $name, array $options = []): self
    {
        // @todo check type, resolve options

        $this->columns[] = new Column($name);

        return $this;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->columns);
    }

    public function config(): array
    {
        return array_map(fn (Column $c) => [
            'title' => strtoupper($c->getName()),
            'data' => $c->getName()
        ], $this->columns);
    }


}