<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\Resource\ResourceInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipCollectionInterface;
use Spryker\Glue\Kernel\AbstractRestResource;

/**
 * @method \Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionFactory getFactory()
 */
class GlueJsonApiConventionResource extends AbstractRestResource implements GlueJsonApiConventionResourceInterface
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
    public function buildRequestSparseField(GlueRequestTransfer $glueRequestTransfer): GlueRequestTransfer
    {
        return $this->getFactory()->createRequestSparseFieldBuilder()->extract($glueRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueRequestTransfer
     */
    public function buildRequestRelationship(GlueRequestTransfer $glueRequestTransfer): GlueRequestTransfer
    {
        return $this->getFactory()->createRequestRelationshipBuilder()->extract($glueRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface> $resourceRoutePlugins
     *
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface
     */
    public function routeMatcher(
        GlueRequestTransfer $glueRequestTransfer,
        array $resourceRoutePlugins
    ): JsonApiResourceInterface {
        return $this->getFactory()->createRequestRoutingMatcher()->matchRequest($glueRequestTransfer, $resourceRoutePlugins);
    }

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
    public function executeJsonApiResource(ResourceInterface $resource, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        return $this->getFactory()->createJsonApiResourceExecutor()->executeResource($resource, $glueRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueResponseTransfer $glueResponseTransfer
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipCollectionInterface $resourceRelationshipPlugins
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function buildJsonGlueResponse(
        GlueResponseTransfer $glueResponseTransfer,
        GlueRequestTransfer $glueRequestTransfer,
        ResourceRelationshipCollectionInterface $resourceRelationshipPlugins
    ): GlueResponseTransfer {
        return $this->getFactory()->createJsonGlueResponseBuilder()->buildResponse($glueResponseTransfer, $glueRequestTransfer, $resourceRelationshipPlugins);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueResponseTransfer $glueResponseTransfer
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipCollectionInterface $resourceRelationshipPlugins
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function buildRelationshipResponse(
        GlueResponseTransfer $glueResponseTransfer,
        GlueRequestTransfer $glueRequestTransfer,
        ResourceRelationshipCollectionInterface $resourceRelationshipPlugins
    ): GlueResponseTransfer {
        return $this->getFactory()->createRelationshipResponseFormatter()->buildResponse($glueResponseTransfer, $glueRequestTransfer, $resourceRelationshipPlugins);
    }
}
