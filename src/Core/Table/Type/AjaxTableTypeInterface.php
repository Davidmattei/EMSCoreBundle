<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table\Type;

use EMS\CoreBundle\Core\Table\Ajax\AjaxRequest;

interface AjaxTableTypeInterface
{
    public function getAjaxData(AjaxRequest $ajaxRequest): array;
}