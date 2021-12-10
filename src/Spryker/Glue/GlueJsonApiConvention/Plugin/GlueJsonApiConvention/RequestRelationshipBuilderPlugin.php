<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueStorefrontApiApplicationExtension\Dependency\Plugin\RequestBuilderPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \Spryker\Glue\StorefrontApiApplicationGlueJsonApiConventionConnector\StorefrontApiApplicationGlueJsonApiConventionConnectorFactory getFactory()
 */
class RequestRelationshipBuilderPlugin extends AbstractPlugin implements RequestBuilderPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueRequestTransfer
     */
    public function build(GlueRequestTransfer $glueRequestTransfer): GlueRequestTransfer
    {
        return $this->getFactory()->getGlueJsonApiConventionResource()->buildRequestRelationship($glueRequestTransfer);
    }
}
