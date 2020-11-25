<?php

declare(strict_types=1);

namespace EMS\CoreBundle\Service\Revision;

use EMS\CommonBundle\Helper\EmsFields;
use EMS\CoreBundle\Entity\Revision;

final class LoggingContext
{
    /**
     * @param array<string, int|string> $extra
     *
     * @return array<string, int|string|null>
     */
    public static function read(Revision $revision, array $extra = []): array
    {
        $context = self::context($revision);
        $context[EmsFields::LOG_OPERATION_FIELD] = EmsFields::LOG_OPERATION_READ;

        return \array_merge($context, $extra);
    }

    /**
     * @return array<string, int|string|null>
     */
    public static function update(Revision $revision): array
    {
        $context = self::context($revision);
        $context[EmsFields::LOG_OPERATION_FIELD] = EmsFields::LOG_OPERATION_UPDATE;

        return $context;
    }

    /**
     * @return array<string, int|string|null>
     */
    public static function compare(Revision $revision, Revision $compareRevision): array
    {
        return [
            EmsFields::LOG_OUUID_FIELD => $revision->getOuuid(),
            EmsFields::LOG_CONTENTTYPE_FIELD => $revision->getContentTypeName(),
            EmsFields::LOG_REVISION_ID_FIELD => $revision->getId(),
            'compare_revision_id' => $compareRevision->getId(),
            'compare_contenttype' => $compareRevision->getContentTypeName(),
            'compare_ouuid' => $compareRevision->getOuuid(),
        ];
    }

    /**
     * @return array<string, int|string|null>
     */
    private static function context(Revision $revision): array
    {
        $context = [
            EmsFields::LOG_OUUID_FIELD => $revision->getOuuid(),
            EmsFields::LOG_REVISION_ID_FIELD => $revision->getId(),
        ];

        if ($contentType = $revision->getContentType()) {
            $context[EmsFields::LOG_CONTENTTYPE_FIELD] = $contentType->getName();

            if ($environment = $contentType->getEnvironment()) {
                $context[EmsFields::LOG_ENVIRONMENT_FIELD] = $environment->getName();
            }
        }

        return $context;
    }
}
