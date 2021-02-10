<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Twig;

use EMS\CoreBundle\Core\UI\UI;
use Twig\Extension\RuntimeExtensionInterface;

final class UIRuntime implements RuntimeExtensionInterface
{
    private UI $ui;

    public function __construct(UI $ui)
    {
        $this->ui = $ui;
    }

    public function getUI(): UI
    {
        return $this->ui;
    }
}