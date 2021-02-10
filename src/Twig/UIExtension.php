<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UIExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('UI', [UIRuntime::class, 'getUI'])
        ];
    }
}