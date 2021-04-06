<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Form\Data;

class TableColumn
{
    private string $titleKey;
    private string $attribute;
    private ?string $routePath = null;
    private ?\Closure $routeCallback;
    private ?string $routeTarget = '_blank';
    private ?string $iconProperty = null;
    private ?string $iconClass = null;

    public function __construct(string $titleKey, string $attribute)
    {
        $this->titleKey = $titleKey;
        $this->attribute = $attribute;
    }

    public function getTitleKey(): string
    {
        return $this->titleKey;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function setRoutePath(string $routePath, ?\Closure $callback = null): void
    {
        $this->routePath = $routePath;
        $this->routeCallback = $callback;
    }

    public function getRoutePath(): ?string
    {
        return $this->routePath;
    }

    /**
     * @param mixed $data
     *
     * @return array<string, mixed>
     */
    public function getRouteProperties($data): array
    {
        if (null === $this->routeCallback) {
            return [];
        }

        return $this->routeCallback->call($this, $data);
    }

    public function setRouteTarget(?string $target): ?string
    {
        return $this->routeTarget = $target;
    }

    public function getRouteTarget(): ?string
    {
        return $this->routeTarget;
    }

    public function getIconProperty(): ?string
    {
        return $this->iconProperty;
    }

    public function setIconProperty(?string $iconProperty): void
    {
        $this->iconProperty = $iconProperty;
    }

    public function setIconClass(string $iconClass): void
    {
        if (\strlen($iconClass) > 0) {
            $this->iconClass = $iconClass;
        } else {
            $this->iconClass = null;
        }
    }

    public function getIconClass(): ?string
    {
        return $this->iconClass;
    }

    public function tableDataBlock(): string
    {
        return 'emsco_form_table_column_data';
    }

    public function getOrderable(): bool
    {
        return true;
    }
}
