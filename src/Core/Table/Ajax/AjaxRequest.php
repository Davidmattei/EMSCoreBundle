<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Core\Table\Ajax;

use Symfony\Component\HttpFoundation\Request;

final class AjaxRequest
{
    private int $start;
    private int $length;

    public function __construct(Request $request)
    {
        $this->start = $request->query->getInt('start', 0);
        $this->length = $request->query->getInt('length', 0);
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getLength(): int
    {
        return $this->length;
    }
}