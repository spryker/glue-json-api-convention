<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RequestBuilderPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionFactory getFactory()
 */
class RequestSparseFieldBuilderPlugin extends AbstractPlugin implements RequestBuilderPluginInterface
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
        return $this->getFactory()->createRequestSparseFieldBuilder()->extract($glueRequestTransfer);
    }
}
