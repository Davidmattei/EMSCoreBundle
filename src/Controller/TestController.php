<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class TestController extends AbstractController
{
    /**
     * @Route("/test")
     */
    public function test()
    {
        return $this->render('@EMSCore/base.html.twig');
    }
}