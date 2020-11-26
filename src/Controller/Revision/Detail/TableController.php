<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Controller\Revision\Detail;

use EMS\CoreBundle\Entity\Revision;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TableController extends AbstractController
{
    /**
     * @Route("/revision/{id}/revisions", name="revision.revisions")
     */
    public function tableRevisions(Revision $revision): Response
    {
        return $this->render('@EMSCore/Revision/Detail/table-revisions.html.twig', [
            'revision' => $revision
        ]);
    }

}

