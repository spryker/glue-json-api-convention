<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\Resource\ResourceInterface;

class JsonApiResourceExecutor implements JsonApiResourceExecutorInterface
{
    /**
     * @var \Spryker\Glue\GlueJsonApiConvention\Resource\ResourceExtractorInterface
     */
    protected $resourceExtractor;

    /**
     * @param \Spryker\Glue\GlueJsonApiConvention\Resource\ResourceExtractorInterface $resourceExtractor
     */
    public function __construct(ResourceExtractorInterface $resourceExtractor)
    {
        $this->resourceExtractor = $resourceExtractor;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Resource\ResourceInterface $resource
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function executeResource(ResourceInterface $resource, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        $glueResponse = new GlueResponseTransfer();

        if (!$resource instanceof JsonApiResourceInterface) {
            return $glueResponse;
        }

        if ($glueRequestTransfer->getContent()) {
            $transferClass = $resource->getResourceRoutePlugin()->getResourceAttributesClassName();
            $resourceTransfer = new $transferClass();

            $parsedRequestBody = $this->resourceExtractor->extract($glueRequestTransfer);
            $resourceTransfer->fromArray($parsedRequestBody[ResourceExtractor::RESOURCE_ATTRIBUTES], true);
            $glueRequestTransfer->getResource()->setAttributes($resourceTransfer);
            $glueRequestTransfer->getResource()->setType($parsedRequestBody[ResourceExtractor::RESOURCE_TYPE]);
            $glueRequestTransfer->getResource()->setId($parsedRequestBody[ResourceExtractor::RESOURCE_ID]);

            return call_user_func($resource->getResource($glueRequestTransfer->getMethod()), $resourceTransfer, $glueRequestTransfer, $glueResponse);
        }

        if ($glueRequestTransfer->getResource()->getId()) {
            return call_user_func($resource->getResource($glueRequestTransfer->getMethod()), $glueRequestTransfer->getResource()->getId(), $glueRequestTransfer, $glueResponse);
        }

        return call_user_func($resource->getResource($glueRequestTransfer->getMethod()), $glueRequestTransfer, $glueResponse);
    }
}
