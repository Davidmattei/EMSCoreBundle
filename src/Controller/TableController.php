<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Controller;

use EMS\CoreBundle\Core\Table\Ajax\AjaxRequest;
use EMS\CoreBundle\Core\Table\TableManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class TableController extends AbstractController
{
    private TableManager $tableManager;

    public function __construct(TableManager $tableBuilder)
    {
        $this->tableManager = $tableBuilder;
    }

    /**
     * @Route("/table/{name}/data", name="ems.table.data", requirements={"name": "\S+"})
     */
    public function getData(Request $request, string $name): JsonResponse
    {
        $ajaxRequest = new AjaxRequest($request);

        $data = $this->tableManager->getAjaxData($ajaxRequest, $name);


//
//        $data = [];
//        $rows = 10000;
//
//        while ($rows-- > 0) {
//            $data[] = [
//                'id' => $rows,
//                'sha1' => 'test'
//            ];
//        }

        return new JsonResponse($data);
    }


}