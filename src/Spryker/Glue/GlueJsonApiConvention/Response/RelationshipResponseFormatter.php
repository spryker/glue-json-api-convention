<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Response;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipCollectionInterface;

class RelationshipResponseFormatter implements RelationshipResponseFormatterInterface
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
    ): GlueResponseTransfer {
        $resourceType = $glueResponseTransfer->getResource()->getType();

        if (
            $glueRequestTransfer->getExcludeRelationships() ||
            !$glueResponseTransfer->getResource() ||
            !$resourceRelationshipPlugins->hasRelationships($resourceType)
        ) {
            return $glueResponseTransfer;
        }

        foreach ($resourceRelationshipPlugins->getRelationships() as $resourceRelationshipPlugin) {
            if (!in_array($resourceRelationshipPlugin->getRelationshipType(), $glueRequestTransfer->getIncludedRelationships())) {
                continue;
            }
            $glueResourceRelationships[] = $resourceRelationshipPlugin;
        }
        $glueResponseTransfer->getResource()->setRelationships($glueResourceRelationships);

        return $glueResponseTransfer;
    }
}
