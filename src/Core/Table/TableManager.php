<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table;

use EMS\CoreBundle\Core\Table\Ajax\AjaxRequest;
use EMS\CoreBundle\Core\Table\Type\AjaxTableTypeInterface;
use EMS\CoreBundle\Core\Table\Type\TableTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class TableManager
{
    /** @var iterable|TableTypeInterface[] */
    private iterable $tableTypes;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(iterable $tableTypes, UrlGeneratorInterface $urlGenerator)
    {
        $this->tableTypes = $tableTypes;
        $this->urlGenerator = $urlGenerator;
    }

    public function getAjaxData(AjaxRequest $ajaxRequest, string $name): array
    {
        $tableType = $this->getTypeByName($name);

        if (!$tableType instanceof AjaxTableTypeInterface) {
            return [];
        }

        return $tableType->getAjaxData($ajaxRequest);
    }

    public function create(string $class, array $options): TableInterface
    {
        $tableType = $this->getTypeByClass($class);

        $optionResolver = new OptionsResolver();
        $tableType->configureOptions($optionResolver);

        $table = new Table($tableType, $optionResolver->resolve($options));

        if ($tableType instanceof AjaxTableTypeInterface) {
            $table->setAjaxUrl($this->urlGenerator->generate('ems.table.data', [
                'name' => $tableType->getName()
            ]));
        }

        return $table;
    }

    private function getTypeByClass(string $class): TableTypeInterface
    {
        foreach ($this->tableTypes as $tableType) {
            if ($tableType instanceof $class) {
                return $tableType;
            }
        }

        throw new \RuntimeException(sprintf('Table type by class "%s" not found', $class));
    }

    private function getTypeByName(string $name): TableTypeInterface
    {
        foreach ($this->tableTypes as $tableType) {
            if ($name === $tableType->getName()) {
                return $tableType;
            }
        }

        throw new \RuntimeException(sprintf('Table type by name "%s" not found', $name));
    }

}