<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Controller;

use EMS\CoreBundle\Entity\ContentType;
use EMS\CoreBundle\Table\ContentType\ActionTable;
use EMS\CoreBundle\Core\Table\TableManager;
use EMS\CoreBundle\Table\RevisionTable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TestController extends AbstractController
{
    private TableManager $tableBuilder;

    public function __construct(TableManager $tableBuilder)
    {
        $this->tableBuilder = $tableBuilder;
    }

    /**
     * @Route("/test", "ems_core_test")
     */
    public function test(): Response
    {
        $ctCompany = $this->getDoctrine()->getRepository(ContentType::class)->find(3);

        $actionTable = $this->tableBuilder->create(ActionTable::class, [
            'content_type' => $ctCompany
        ]);

        $revisionsTable = $this->tableBuilder->create(RevisionTable::class, []);

        return $this->render('@EMSCore/test.html.twig', [
            'actionTable' => $actionTable,
            'revisionsTable' => $revisionsTable,
        ]);
    }

}