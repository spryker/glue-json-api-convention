<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

use Spryker\Glue\GlueApplication\Resource\Resource as GlueApplicationResource;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface;
use Spryker\Glue\GlueRestApiConvention\Resource\ResourceRouteCollectionInterface;

class JsonApiResource extends GlueApplicationResource implements JsonApiResourceInterface
{
    /**
     * @param callable $executableResource
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface $resourceRoutePlugin
     */
    public function __construct(
        callable $executableResource,
        ResourceRoutePluginInterface $resourceRoutePlugin
    ) {
        parent::__construct($executableResource, $resourceRoutePlugin);
    }

    public function getName(): string
    {
        return '';
    }

    public function getResourceAttributesClassName(): string
    {
        return '';
    }
}
