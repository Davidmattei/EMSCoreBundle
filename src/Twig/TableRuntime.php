<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Twig;

use EMS\CoreBundle\Core\Table\TableInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

final class TableRuntime implements RuntimeExtensionInterface
{
    private Environment $twig;

    private const TEMPLATE = '@EMSCore/table/table.html.twig';

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(TableInterface $table): string
    {
        //@todo catch errors
        return $this->twig->load(self::TEMPLATE)->renderBlock('table', ['table' => $table]);
    }
}