<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Router;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface;

interface RequestRoutingMatcherInterface
{
    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface> $resourcePlugins
     *
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface
     */
    public function matchRequest(GlueRequestTransfer $glueRequestTransfer, array $resourcePlugins): JsonApiResourceInterface;
}
