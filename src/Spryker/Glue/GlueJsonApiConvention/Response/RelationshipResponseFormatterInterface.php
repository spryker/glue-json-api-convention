<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Response;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipCollectionInterface;

interface RelationshipResponseFormatterInterface
{
    /**
     * @param \Generated\Shared\Transfer\GlueResponseTransfer $glueResponseTransfer
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipCollectionInterface $resourceRelationshipPlugins
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function buildResponse(
        GlueResponseTransfer $glueResponseTransfer,
        GlueRequestTransfer $glueRequestTransfer,
        ResourceRelationshipCollectionInterface $resourceRelationshipPlugins
    ): GlueResponseTransfer;
}
