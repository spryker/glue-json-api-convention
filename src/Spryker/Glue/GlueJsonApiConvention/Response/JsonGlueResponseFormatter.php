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
     * @var string
     */
    protected const RESOURCE_TYPE = 'type';

    /**
     * @var string
     */
    protected const RESOURCE_ID = 'id';

    /**
     * @var string
     */
    protected const RESOURCE_ATTRIBUTES = 'attributes';

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
     * @param array<\Generated\Shared\Transfer\GlueResourceTransfer> $glueResources
     * @param array<string, mixed> $sparseFields
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    public function formatResponseData(
        array $glueResources,
        array $sparseFields,
        GlueRequestTransfer $glueRequestTransfer
    ): string {
        $data = $this->getResourceData($glueResources, $glueRequestTransfer);
        $responseData = [];
        if ($this->isSingleObjectRequest($glueRequestTransfer, $glueResources)) {
            $responseData[static::RESPONSE_DATA] = $data[0];
        } else {
            $responseData[static::RESPONSE_DATA] = $data;
            $responseData[static::RESPONSE_LINKS] = $this->buildCollectionLink($glueRequestTransfer);
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
        $responseData[static::RESPONSE_LINKS] = $this->buildCollectionLink($glueRequestTransfer);

        return $this->jsonEncoder->encode($responseData);
    }

    /**
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
        $idResource = $glueRequestTransfer->getResourceOrFail()->getId();

        if ($method === Request::METHOD_GET && ($idResource === null || $this->isCurrentUserCollectionResource($idResource))) {
            $linkParts = [];
            $linkParts[] = $glueRequestTransfer->getResourceOrFail()->getType();
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
     * @param array<\Generated\Shared\Transfer\GlueResourceTransfer> $glueResources
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getResourceData(array $glueResources, GlueRequestTransfer $glueRequestTransfer): array
    {
        $resourcesData = [];
        foreach ($glueResources as $resource) {
            $resource = array_filter($resource->toArray());
            if (!array_key_exists(static::RESPONSE_LINKS, $resource)) {
                $resource[static::RESPONSE_LINKS] = $this->getResponseLink($resource, $glueRequestTransfer);
            }
            $resourcesData[] = $resource;
        }

        return $resourcesData;
    }

    /**
     * @param array<string, mixed> $resource
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return array<string, string>
     */
    protected function getResponseLink(array $resource, GlueRequestTransfer $glueRequestTransfer): array
    {
        $link = $resource[static::RESOURCE_TYPE];
        if ($resource[static::RESOURCE_ID]) {
            $link .= '/' . $resource[static::RESOURCE_ID];
        }
        $queryString = $this->buildQueryString($glueRequestTransfer);

        return $this->formatLinks([static::LINK_SELF => $link . $queryString]);
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
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param array<\Generated\Shared\Transfer\GlueResourceTransfer> $glueResources
     *
     * @return bool
     */
    protected function isSingleObjectRequest(GlueRequestTransfer $glueRequestTransfer, array $glueResources): bool
    {
        $resourceId = $glueRequestTransfer->getResourceOrFail()->getId();
        $method = $glueRequestTransfer->getMethod();

        return count($glueResources) === 1 && (($resourceId && $resourceId !== static::COLLECTION_IDENTIFIER_CURRENT_USER) || $method === Request::METHOD_POST);
    }

    /**
     * @param array<string, string> $links
     *
     * @return array<string, string>
     */
    protected function formatLinks(array $links): array
    {
        $formattedLinks = [];

        $domainName = $this->jsonApiConventionConfig->getGlueDomain();

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

        if ($queryFields) {
            $queryString = urldecode(http_build_query($queryFields));

            if (mb_strlen($queryString)) {
                $queryString = '?' . $queryString;
            }
        }

        return $queryString;
    }
}
