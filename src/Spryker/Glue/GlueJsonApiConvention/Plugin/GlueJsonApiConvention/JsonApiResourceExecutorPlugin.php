<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\Resource\ResourceInterface;
use Spryker\Glue\GlueStorefrontApiApplicationExtension\Dependency\Plugin\ResourceExecutorInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \Spryker\Glue\StorefrontApiApplicationGlueJsonApiConventionConnector\StorefrontApiApplicationGlueJsonApiConventionConnectorFactory getFactory()
 */
class JsonApiResourceExecutorPlugin extends AbstractPlugin implements ResourceExecutorInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Spryker\Glue\GlueApplication\Resource\ResourceInterface $resource
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function execute(ResourceInterface $resource, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        return $this->getFactory()->getGlueJsonApiConventionResource()->executeJsonApiResource($resource, $glueRequestTransfer);
    }
}
