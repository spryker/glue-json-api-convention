<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Router;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface;
use Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface;

interface RequestResourcePluginFilterInterface
{
    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequest
     * @param array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface> $routePlugins
     *
     * @return \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface|null
     */
    public function filterPlugins(GlueRequestTransfer $glueRequest, array $routePlugins): ?JsonApiResourceInterface;
}
