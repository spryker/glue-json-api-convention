<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use Spryker\Glue\StoresRestApi\Controller\StoresResourceController;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

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

    // TODO: move to GlueApplication
    public function executeResource(ResourceInterface $resource, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        $glueResponseTransfer = new GlueResponseTransfer();

        $executableResource = $resource->getResource($glueRequestTransfer);

        if ($glueRequestTransfer->getContent()) {
            // TODO: find this class through reflection
//            $executable1 = [StoresResourceController::class, 'getAction'];
//            $method1 = new ReflectionMethod($executable1[0], $executable1[1]);
//            $method1->getParameters()[0]->getType()->getName(); // [controller, action] first parameter
//
//            $executable2 = function(AbstractTransfer $transfer) {};
//            $method2 = new ReflectionFunction($executable2);
//            $method2->getParameters()[0]->getType()->getName(); // closure first parameter
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
