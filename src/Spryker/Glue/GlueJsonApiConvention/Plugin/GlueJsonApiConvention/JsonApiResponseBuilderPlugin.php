<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResponseFormatterPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionFactory getFactory()
 */
class JsonApiResponseBuilderPlugin extends AbstractPlugin implements ResponseFormatterPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueResponseTransfer $glueResponseTransfer
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function build(GlueResponseTransfer $glueResponseTransfer, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        return $this->getFactory()->createJsonGlueResponseBuilder()->buildResponse(
            $glueResponseTransfer,
            $glueRequestTransfer,
        );
    }
}
