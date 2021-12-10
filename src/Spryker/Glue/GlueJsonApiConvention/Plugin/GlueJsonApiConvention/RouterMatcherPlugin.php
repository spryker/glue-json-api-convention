<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RouteMatcherPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionFactory getFactory()
 */
class RouterMatcherPlugin extends AbstractPlugin implements RouteMatcherPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param array<\Spryker\Glue\GlueApplication\Resource\ResourceInterface> $resources
     *
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface
     */
    public function route(GlueRequestTransfer $glueRequestTransfer, array $resources): JsonApiResourceInterface
    {
        return $this->getFactory()->createRequestRoutingMatcher()->matchRequest($glueRequestTransfer, $resources);
    }
}
