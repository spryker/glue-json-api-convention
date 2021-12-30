<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Response;

use ArrayObject;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\Encoder\EncoderInterface;
use Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig;
use Symfony\Component\HttpFoundation\Request;

class JsonGlueResponseFormatter implements JsonGlueResponseFormatterInterface
{
    /**
     * @var string
     */
    protected const RESPONSE_INCLUDED = 'included';

    /**
     * @var string
     */
    protected const RESPONSE_RELATIONSHIPS = 'relationships';

    /**
     * @var string
     */
    protected const RESPONSE_LINKS = 'links';

    /**
     * @var string
     */
    protected const RESPONSE_DATA = 'data';

    /**
     * @var string
     */
    protected const RESPONSE_ERRORS = 'errors';

    /**
     * @var string
     */
    protected const COLLECTION_IDENTIFIER_CURRENT_USER = 'mine';

    /**
     * @var string
     */
    protected const LINK_SELF = 'self';

    /**
     * @var \Spryker\Glue\GlueJsonApiConvention\Encoder\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig
     */
    protected $jsonApiConventionConfig;

    /**
     * @param \Spryker\Glue\GlueJsonApiConvention\Encoder\EncoderInterface $jsonEncoder
     * @param \Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig $jsonApiConventionConfig
     */
    public function __construct(EncoderInterface $jsonEncoder, GlueJsonApiConventionConfig $jsonApiConventionConfig)
    {
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonApiConventionConfig = $jsonApiConventionConfig;
    }

    /**
     * @param array<string, mixed> $mainResource
     * @param array<string, mixed> $sparseFields
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    public function formatResponseData(
        array $mainResource,
        array $sparseFields,
        GlueRequestTransfer $glueRequestTransfer
    ): string {
        $responseData = [];
        $responseData[static::RESPONSE_DATA] = $this->getResourceData($mainResource);
        //$responseData[static::RESPONSE_LINKS] = $this->buildCollectionLink($glueRequestTransfer);

        if (!$glueRequestTransfer->getExcludeRelationships()) {
            $responseData[static::RESPONSE_INCLUDED] = [];
            foreach ($mainResource[static::RESPONSE_RELATIONSHIPS] as $relationshipType => $relationship) {
                $responseData[static::RESPONSE_DATA][static::RESPONSE_RELATIONSHIPS][$relationshipType][static::RESPONSE_LINKS] = $relationship[static::RESPONSE_LINKS];
                if ($sparseFields && isset($sparseFields[$relationshipType])) {
                    $relationshipData = [];
                    foreach ($sparseFields[$relationshipType] as $sparseField) {
                        $relationshipData[$sparseField] = $relationship[$sparseField];
                    }
                    $responseData[static::RESPONSE_INCLUDED][$relationshipType] = $relationshipData;
                } else {
                    $responseData[static::RESPONSE_INCLUDED][$relationshipType] = $this->getResourceData($relationship);
                }
            }
        }

        return $this->jsonEncoder->encode($responseData);
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    public function formatResponseWithEmptyResource(GlueRequestTransfer $glueRequestTransfer): string
    {
        $responseData = [];
        $responseData[static::RESPONSE_DATA] = [];
//        $responseData[static::RESPONSE_LINKS] = $this->buildCollectionLink($glueRequestTransfer);

        return $this->jsonEncoder->encode($responseData);
    }

    /**
     * *
     * @param \ArrayObject<int, \Generated\Shared\Transfer\RestErrorMessageTransfer> $restErrorMessageTransfers
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    public function formatErrorResponse(
        ArrayObject $restErrorMessageTransfers,
        GlueRequestTransfer $glueRequestTransfer
    ): string {
        $response = [];
        foreach ($restErrorMessageTransfers as $restErrorMessageTransfer) {
            $response[static::RESPONSE_ERRORS][] = $restErrorMessageTransfer->toArray();
        }

        return $this->jsonEncoder->encode($response);
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return array<mixed>
     */
    protected function buildCollectionLink(GlueRequestTransfer $glueRequestTransfer): array
    {
        $method = $glueRequestTransfer->getMethod();
        $idResource = $glueRequestTransfer->getResource()->getId();

        if ($method === Request::METHOD_GET && ($idResource === null || $this->isCurrentUserCollectionResource($idResource))) {
            $linkParts = [];
            $linkParts[] = $glueRequestTransfer->getResource()->getType();
            if ($this->isCurrentUserCollectionResource($idResource)) {
                $linkParts[] = static::COLLECTION_IDENTIFIER_CURRENT_USER;
            }
            $queryString = $this->buildQueryString($glueRequestTransfer);

            return $this->formatLinks([
                static::LINK_SELF => implode('/', $linkParts) . $queryString,
            ]);
        }

        return [];
    }

    /**
     * @param array<string, mixed> $mainResource
     *
     * @return array<string, mixed>
     */
    protected function getResourceData(array $mainResource): array
    {
        $resourceData = [];
        foreach ($mainResource as $field => $value) {
            if ($field === static::RESPONSE_RELATIONSHIPS) {
                continue;
            }
            if ($value) {
                $resourceData[$field] = $value;
            }
        }

        return $resourceData;
    }

    /**
     * @param string|null $idResource
     *
     * @return bool
     */
    protected function isCurrentUserCollectionResource(?string $idResource): bool
    {
        return $idResource === static::COLLECTION_IDENTIFIER_CURRENT_USER;
    }

    /**
     * @param array<string, string> $links
     *
     * @return array<string, string>
     */
    protected function formatLinks(array $links): array
    {
        $formattedLinks = [];

        $domainName = $this->jsonApiConventionConfig->getGlueDomainName();

        foreach ($links as $key => $link) {
            $formattedLinks[$key] = $domainName . '/' . $link;
        }

        return $formattedLinks;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    protected function buildQueryString(GlueRequestTransfer $glueRequestTransfer): string
    {
        $queryFields = $glueRequestTransfer->getQueryFields();
        $queryString = '';

        foreach ($queryFields as $queryType => $queryField) {
            $queryString .= $queryType . '=' . implode(',', $queryField);
        }

        if (mb_strlen($queryString)) {
            $queryString = '?' . $queryString;
        }

        return $queryString;
    }
}
