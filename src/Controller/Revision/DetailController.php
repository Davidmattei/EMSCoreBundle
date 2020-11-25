<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Controller\Revision;

use Doctrine\ORM\EntityManager;
use EMS\CommonBundle\Elasticsearch\Response\Response as CommonResponse;
use EMS\CommonBundle\Helper\EmsFields;
use EMS\CommonBundle\Service\ElasticaService;
use EMS\CommonBundle\Storage\NotFoundException;
use EMS\CoreBundle\Entity\ContentType;
use EMS\CoreBundle\Entity\Form\Search;
use EMS\CoreBundle\Entity\Form\SearchFilter;
use EMS\CoreBundle\Entity\Revision;
use EMS\CoreBundle\Form\Form\RevisionType;
use EMS\CoreBundle\Repository\ContentTypeRepository;
use EMS\CoreBundle\Repository\EnvironmentRepository;
use EMS\CoreBundle\Repository\RevisionRepository;
use EMS\CoreBundle\Service\ContentTypeService;
use EMS\CoreBundle\Service\DataService;
use EMS\CoreBundle\Service\SearchService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends AbstractController
{
    /** @var DataService */
    private $dataService;
    /** @var ContentTypeService */
    private $contentTypeService;
    /** @var LoggerInterface */
    private $logger;
    /** @var SearchService */
    private $searchService;
    /** @var ElasticaService */
    private $elasticaService;

    public function __construct(DataService $dataService, ContentTypeService $contentTypeService, LoggerInterface $logger, SearchService $searchService, ElasticaService $elasticaService)
    {
        $this->dataService = $dataService;
        $this->contentTypeService = $contentTypeService;
        $this->logger = $logger;
        $this->searchService = $searchService;
        $this->elasticaService = $elasticaService;
    }

    /**
     * @param int|false $revisionId
     * @param int|false $compareId
     *
     * @Route("/data/revisions/{type}:{ouuid}/{revisionId}/{compareId}", defaults={"revisionId"=false, "compareId"=false}, name="data.revisions")
     * @Route("/data/revisions/{type}:{ouuid}/{revisionId}/{compareId}", defaults={"revisionId"=false, "compareId"=false}, name="ems_content_revisions_view")
     */
    public function revisionsDataAction(Request $request, string $type, string $ouuid, $revisionId, $compareId): Response
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var ContentTypeRepository $contentTypeRepo */
        $contentTypeRepo = $em->getRepository('EMSCoreBundle:ContentType');

        $contentTypes = $contentTypeRepo->findBy([
            'deleted' => false,
            'name' => $type,
        ]);
        if (!$contentTypes || 1 != \count($contentTypes)) {
            throw new NotFoundHttpException('Content Type not found');
        }

        $contentType = $contentTypes[0];
        if (!$contentType instanceof ContentType) {
            throw new NotFoundHttpException('Content Type not found');
        }

        $defaultEnvironment = $contentType->getEnvironment();
        if (null === $defaultEnvironment) {
            throw new \RuntimeException('Unexpected nul environment');
        }

        if (!$defaultEnvironment->getManaged()) {
            return $this->redirectToRoute('data.view', [
                'environmentName' => $defaultEnvironment->getName(),
                'type' => $type,
                'ouuid' => $ouuid,
            ]);
        }

        /** @var RevisionRepository $repository */
        $repository = $em->getRepository('EMSCoreBundle:Revision');

        /* @var Revision $revision */
        if (!$revisionId) {
            $revision = $repository->findOneBy([
                'endTime' => null,
                'ouuid' => $ouuid,
                'deleted' => false,
                'contentType' => $contentType,
            ]);
        } else {
            $revision = $repository->findOneById((int) $revisionId);
        }

        if (!$revision instanceof Revision) {
            throw new NotFoundException('Revision not found!');
        }

        $compareData = false;
        if ($compareId) {
            $this->logger->warning('log.data.revision.compare_beta', []);

            try {
                $compareRevision = $repository->findOneById($compareId);
                $compareData = $compareRevision->getRawData();
                if ($revision->getContentType() === $compareRevision->getContentType() && $revision->getOuuid() == $compareRevision->getOuuid()) {
                    if ($compareRevision->getCreated() <= $revision->getCreated()) {
                        $this->logger->notice('log.data.revision.compare', [
                            EmsFields::LOG_OUUID_FIELD => $revision->getOuuid(),
                            EmsFields::LOG_CONTENTTYPE_FIELD => $revision->getContentTypeName(),
                            EmsFields::LOG_REVISION_ID_FIELD => $revision->getId(),
                            'compare_revision_id' => $compareRevision->getId(),
                        ]);
                    } else {
                        $this->logger->warning('log.data.revision.compare_more_recent', [
                            EmsFields::LOG_OUUID_FIELD => $revision->getOuuid(),
                            EmsFields::LOG_CONTENTTYPE_FIELD => $revision->getContentTypeName(),
                            EmsFields::LOG_REVISION_ID_FIELD => $revision->getId(),
                            'compare_revision_id' => $compareRevision->getId(),
                        ]);
                    }
                } else {
                    $this->logger->notice('log.data.document.compare', [
                        EmsFields::LOG_OUUID_FIELD => $revision->getOuuid(),
                        EmsFields::LOG_CONTENTTYPE_FIELD => $revision->getContentTypeName(),
                        EmsFields::LOG_REVISION_ID_FIELD => $revision->getId(),
                        'compare_contenttype' => $compareRevision->getContentTypeName(),
                        'compare_ouuid' => $compareRevision->getOuuid(),
                        'compare_revision_id' => $compareRevision->getId(),
                    ]);
                }
            } catch (\Throwable $e) {
                $this->logger->warning('log.data.revision.compare_revision_not_found', [
                    EmsFields::LOG_OUUID_FIELD => $revision->getOuuid(),
                    EmsFields::LOG_CONTENTTYPE_FIELD => $revision->getContentTypeName(),
                    EmsFields::LOG_REVISION_ID_FIELD => $revision->getId(),
                    'compare_revision_id' => $compareId,
                ]);
            }
        }

        if ($revision->getOuuid() != $ouuid || $revision->getContentType() !== $contentType || $revision->getDeleted()) {
            throw new NotFoundHttpException('Revision not found');
        }

        $this->dataService->testIntegrityInIndexes($revision);

        $this->loadAutoSavedVersion($revision, $this->logger);

        $page = $request->query->get('page', 1);

        $revisionsSummary = $repository->getAllRevisionsSummary($ouuid, $contentType, $page);
        $lastPage = $repository->revisionsLastPage($ouuid, $contentType);
        $counter = $repository->countRevisions($ouuid, $contentType);
        $firstElemOfPage = $repository->firstElemOfPage($page);
        /** @var EnvironmentRepository $envRepository */
        $envRepository = $em->getRepository('EMSCoreBundle:Environment');
        $availableEnv = $envRepository->findAvailableEnvironements($defaultEnvironment);

        $form = $this->createForm(RevisionType::class, $revision, ['raw_data' => $revision->getRawData()]);

        $objectArray = $form->getData()->getRawData();

        $dataFields = $this->dataService->getDataFieldsStructure($form->get('data'));

        $searchForm = new Search();
        $searchForm->setContentTypes($this->contentTypeService->getAllNames());
        $searchForm->setEnvironments([$defaultEnvironment->getName()]);
        $searchForm->setSortBy('_uid');
        $searchForm->setSortOrder('asc');

        $filter = $searchForm->getFilters()[0];
        $filter->setBooleanClause('should');
        $filter->setField($contentType->getRefererFieldName());
        $filter->setPattern(\sprintf('%s:%s', $type, $ouuid));
        $filter->setOperator('term');

        $filter = new SearchFilter();
        $filter->setBooleanClause('should');
        $filter->setField($contentType->getRefererFieldName());
        $filter->setPattern(\sprintf('"%s:%s"', $type, $ouuid));
        $filter->setOperator('match_and');
        $searchForm->addFilter($filter);

        $searchForm->setMinimumShouldMatch(1);
        $esSearch = $this->searchService->generateSearch($searchForm);
        $esSearch->setSize(100);
        $esSearch->setSources([]);

        $referrerResultSet = $this->elasticaService->search($esSearch);
        $referrerResponse = CommonResponse::fromResultSet($referrerResultSet);

        return $this->render('@EMSCore/data/revisions-data.html.twig', [
            'revision' => $revision,
            'revisionsSummary' => $revisionsSummary,
            'availableEnv' => $availableEnv,
            'object' => $revision->getObject($objectArray),
            'referrerResponse' => $referrerResponse,
            'page' => $page,
            'lastPage' => $lastPage,
            'counter' => $counter,
            'firstElemOfPage' => $firstElemOfPage,
            'dataFields' => $dataFields,
            'compareData' => $compareData,
            'compareId' => $compareId,
            'referrersForm' => $searchForm,
        ]);
    }

    private function loadAutoSavedVersion(Revision $revision, LoggerInterface $logger): void
    {
        if (null != $revision->getAutoSave()) {
            $revision->setRawData($revision->getAutoSave());
            $logger->warning('log.data.revision.load_from_auto_save', [
                EmsFields::LOG_CONTENTTYPE_FIELD => $revision->getContentTypeName(),
                EmsFields::LOG_OPERATION_FIELD => EmsFields::LOG_OPERATION_READ,
                EmsFields::LOG_OUUID_FIELD => $revision->getOuuid(),
                EmsFields::LOG_REVISION_ID_FIELD => $revision->getId(),
            ]);
        }
    }
}
