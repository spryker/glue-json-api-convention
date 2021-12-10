<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Router;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueApplication\Resource\ResourceInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceBuilderInterface;
use Spryker\Glue\GlueRestApiConvention\Exception\Router\MissingRequestMethodException;
use Spryker\Glue\GlueRestApiConvention\Router\ResourceRouteCollection;

class RequestRoutingMatcher implements RequestRoutingMatcherInterface
{
    /**
     * @var \Spryker\Glue\GlueJsonApiConvention\Router\RequestResourcePluginFilterInterface
     */
    protected $resourcePluginFilter;

    /**
     * @var \Spryker\Glue\GlueJsonApiConvention\Resource\ResourceBuilderInterface
     */
    protected $resourceBuilder;

    /**
     * @param \Spryker\Glue\GlueJsonApiConvention\Router\RequestResourcePluginFilterInterface $resourcePluginFilter
     * @param \Spryker\Glue\GlueJsonApiConvention\Resource\ResourceBuilderInterface $resourceBuilder
     */
    public function __construct(
        RequestResourcePluginFilterInterface $resourcePluginFilter,
        ResourceBuilderInterface $resourceBuilder
    ) {
        $this->resourcePluginFilter = $resourcePluginFilter;
        $this->resourceBuilder = $resourceBuilder;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param array<\Spryker\Glue\GlueApplication\Resource\ResourceInterface> $resourcePlugins
     *
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface
     */
    public function matchRequest(GlueRequestTransfer $glueRequestTransfer, array $resourcePlugins): JsonApiResourceInterface
    {
        $resourceRoutePlugin = $this->resourcePluginFilter->filterPlugins($glueRequestTransfer, $resourcePlugins);

        if (!$resourceRoutePlugin) {
            return $this->resourceBuilder->buildMissingResource();
        }

        //$resourceMethodCollection = $resourceRoutePlugin->configure(new ResourceRouteCollection());

        $requestMethod = $this->getRequestMethod($glueRequestTransfer);

//        if (
//            $requestMethod === ResourceRouteCollection::METHOD_OPTIONS
//            && !$resourceMethodCollection->has(ResourceRouteCollection::METHOD_OPTIONS)
//        ) {
//            return $this->resourceBuilder->buildPreFlightResource($resourceMethodCollection, $resourceRoutePlugin);
//        }


        return $resourceRoutePlugin;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @throws \Spryker\Glue\GlueRestApiConvention\Exception\Router\MissingRequestMethodException
     *
     * @return string
     */
    protected function getRequestMethod(GlueRequestTransfer $glueRequestTransfer): string
    {
        if (empty($glueRequestTransfer->getMethod())) {
            throw new MissingRequestMethodException('Empty request method can not be mapped to a controller action');
        }

        $method = strtoupper($glueRequestTransfer->getMethod());

        if (
            $method === ResourceRouteCollection::METHOD_GET
            && $glueRequestTransfer->getResource()
            && $glueRequestTransfer->getResource()->getId() === null
        ) {
            return ResourceRouteCollection::METHOD_GET_COLLECTION;
        }

        return $method;
    }
}
