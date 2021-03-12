<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table;

use EMS\CoreBundle\Core\Table\Column\Columns;
use EMS\CoreBundle\Core\Table\Type\TableTypeInterface;

final class Table implements TableInterface
{
    private TableTypeInterface $type;
    private Columns $columns;

    private array $data;
    private ?string $ajaxUrl = null;

    public function __construct(TableTypeInterface $type, array $options)
    {
        $columns = new Columns();
        $type->configureColumns($columns, $options);

        $this->type = $type;
        $this->columns = $columns;
        $this->data = $type->getData($columns, $options);
    }

    public function getAttributes(): array
    {
        $attributes = [
            'id' => $this->type->getName(),
            'data-columns' => \json_encode($this->columns->config()),
        ];

        if (null !== $this->ajaxUrl) {
            $attributes['data-server-side'] = "true";
            $attributes['data-ajax'] = \json_encode([
                'url' => $this->ajaxUrl
            ]);
        }

        return $attributes;
    }

    public function getColumns(): Columns
    {
        return $this->columns;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getType(): TableTypeInterface
    {
        return $this->type;
    }

    public function setAjaxUrl(?string $ajaxUrl): void
    {
        $this->ajaxUrl = $ajaxUrl;
    }
}