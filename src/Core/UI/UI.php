<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\UI;

use EMS\CoreBundle\Core\UI\Sidebar\Sidebar;

final class UI
{

    private Sidebar $sidebar;

    public function __construct(Sidebar $sidebar)
    {
        $this->sidebar = $sidebar;
    }


    public function renderSidebar(): string
    {

    }


}