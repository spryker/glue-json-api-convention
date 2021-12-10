<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

use Spryker\Glue\GlueRestApiConvention\Resource\ResourceRouteCollectionInterface;
use Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface;

interface ResourceBuilderInterface
{
    /**
     * @param \Spryker\Glue\GlueRestApiConvention\Resource\ResourceRouteCollectionInterface $resourceRouteCollection
     * @param \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface $resourceRoutePlugin
     *
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface
     */
    public function buildPreFlightResource(
        ResourceRouteCollectionInterface $resourceRouteCollection,
        ResourceRoutePluginInterface $resourceRoutePlugin
    ): JsonApiResourceInterface;

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\MissingResource
     */
    public function buildMissingResource(): MissingResource;

    /**
     * @param \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface $resourceRoutePlugin
     * @param \Spryker\Glue\GlueRestApiConvention\Resource\ResourceRouteCollectionInterface $resourceRouteCollection
     * @param string $requestMethod
     *
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface
     */
    public function buildResource(
        ResourceRoutePluginInterface $resourceRoutePlugin,
        ResourceRouteCollectionInterface $resourceRouteCollection,
        string $requestMethod
    ): JsonApiResourceInterface;
}
