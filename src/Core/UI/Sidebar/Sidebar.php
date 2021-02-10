<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\UI\Sidebar;


use Twig\Environment;

final class Sidebar
{
    private Environment $twig;

    public function render(): string
    {
        return $this->twig->render('@EMSCore/UI/sidebar.html.twig', [

        ]);
    }
}