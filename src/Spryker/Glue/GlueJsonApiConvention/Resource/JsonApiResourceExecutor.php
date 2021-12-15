<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;

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
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface $resource
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function executeResource(ResourceInterface $resource, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        $glueResponseTransfer = new GlueResponseTransfer();

        // TODO: check if this ever happens. The non-JsonApiResourceInterface are filtered out in routing.
        if (!$resource instanceof JsonApiResourceInterface) {
            return $glueResponseTransfer;
        }

        $executableResource = $resource->getResource($glueRequestTransfer);

        if ($glueRequestTransfer->getContent()) {
            $transferClass = $resource->getResourceAttributesClassName();
            $resourceAttributesTransfer = new $transferClass();

            $parsedRequestBody = $this->resourceExtractor->extract($glueRequestTransfer);
            $resourceAttributesTransfer->fromArray($parsedRequestBody[ResourceExtractor::RESOURCE_ATTRIBUTES], true);
            $glueRequestTransfer->getResource()->setAttributes($resourceAttributesTransfer);
            //$glueRequestTransfer->getResource()->setType($parsedRequestBody[ResourceExtractor::RESOURCE_TYPE]);
            //$glueRequestTransfer->getResource()->setId($parsedRequestBody[ResourceExtractor::RESOURCE_ID]);

            return call_user_func($resource->getResource($glueRequestTransfer), $resourceAttributesTransfer, $glueRequestTransfer, $glueResponseTransfer);
        }

        if ($glueRequestTransfer->getResource()->getId()) {
            return call_user_func($resource->getResource($glueRequestTransfer), $glueRequestTransfer->getResource()->getId(), $glueRequestTransfer, $glueResponseTransfer);
        }

        return call_user_func($resource->getResource($glueRequestTransfer), $glueRequestTransfer, $glueResponseTransfer);
    }
}
