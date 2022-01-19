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
class RelationshipResponseFormatterPlugin extends AbstractPlugin implements ResponseFormatterPluginInterface
{
    //TODO spec
    //called in Bundles/GlueJsonApiConvention/src/Spryker/Glue/GlueJsonApiConvention/Plugin/GlueApplication/JsonApiApiConventionPlugin.php
    public function build(GlueResponseTransfer $glueResponseTransfer, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        return $this->getFactory()->createRelationshipResponse()->buildResponse($glueResponseTransfer, $glueRequestTransfer);
    }
}
