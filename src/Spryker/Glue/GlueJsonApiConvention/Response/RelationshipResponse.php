<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Response;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceRelationshipLoaderInterface;

class RelationshipResponse implements RelationshipResponseInterface
{
    /**
     * @var string
     */
    protected const RESPONSE_INCLUDED = 'included';

    /**
     * @var \Spryker\Glue\GlueJsonApiConvention\Resource\ResourceRelationshipLoaderInterface
     */
    protected $resourceRelationshipProviderLoader;

    /**
     * @var array
     */
    protected $alreadyLoadedResources = [];

    /**
     * @param \Spryker\Glue\GlueJsonApiConvention\Resource\ResourceRelationshipLoaderInterface $resourceRelationshipProviderLoader
     */
    public function __construct(ResourceRelationshipLoaderInterface $resourceRelationshipProviderLoader)
    {
        $this->resourceRelationshipProviderLoader = $resourceRelationshipProviderLoader;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueResponseTransfer $glueResponseTransfer
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     */
    public function buildResponse(GlueResponseTransfer $glueResponseTransfer, GlueRequestTransfer $glueRequestTransfer)
    {
//        dd([$glueResponseTransfer, $glueRequestTransfer]);

        $mainResourceType = $glueResponseTransfer->getResources()[0]->getType();

        $this->loadRelationships(
            $mainResourceType,
            $glueResponseTransfer->getResources(),
            $glueRequestTransfer,
        );

        $included = $this->processIncluded($glueResponseTransfer->getResources(), $glueRequestTransfer);

        //TODO change to json decoder
        $response = json_decode($glueResponseTransfer->getContent(), true);

        if ($included) {
            //TODO add resourcesToArray
            $response[static::RESPONSE_INCLUDED] = $this->resourcesToArray($included, $glueRequestTransfer, $mainResourceType);
        }

        return $glueResponseTransfer;
    }

    /**
     * @param string $resourceName
     * @param array<\Generated\Shared\Transfer\GlueResourceTransfer> $resources
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param string|null $parentResourceId
     *
     * @return void
     */
    public function loadRelationships(
        string $resourceName,
        array $resources,
        GlueRequestTransfer $glueRequestTransfer,
        ?string $parentResourceId = null
    ): void {
        if (!$this->canLoadResource($resourceName, $parentResourceId)) {
            return;
        }

        $resources = $this->applyRelationshipPlugins($resourceName, $resources, $glueRequestTransfer);

        $this->alreadyLoadedResources[$resourceName . $parentResourceId] = true;

        foreach ($resources as $resource) {
            foreach ($resource->getRelationships() as $resourceType => $resourceRelationships) {
                if (!$this->hasRelationship($resourceType, $glueRequestTransfer)) {
                    continue;
                }
                $this->loadRelationships($resourceType, $resourceRelationships, $glueRequestTransfer, $resource->getId());
            }
        }
    }

    /**
     * @param array<\Generated\Shared\Transfer\GlueResourceTransfer> $resources
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return array
     */
    public function processIncluded(array $resources, GlueRequestTransfer $glueRequestTransfer): array
    {
        $included = [];
        foreach ($resources as $resource) {
            $this->processRelationships($resource->getRelationships(), $glueRequestTransfer, $included);
        }

        return array_values($included);
    }

    /**
     * @param array $resourceRelationships
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param array $included
     *
     * @return void
     */
    protected function processRelationships(
        array $resourceRelationships,
        GlueRequestTransfer $glueRequestTransfer,
        array &$included
    ): void {
        /** @var array<\Generated\Shared\Transfer\GlueResourceTransfer> $resources */
        foreach ($resourceRelationships as $resourceType => $resources) {
            if (!$this->hasRelationship($resourceType, $glueRequestTransfer)) {
                continue;
            }
            foreach ($resources as $resource) {
                if ($resource->getRelationships()) {
                    $this->processRelationships($resource->getRelationships(), $glueRequestTransfer, $included);
                }

                $resourceId = $resourceType . ':' . $resource->getId();
                if ($this->isResourceCanBeIncluded($included, $resourceId)) {
                    $included[$resourceId] = $resource;
                }
            }
        }
    }

    /**
     * @param array<\Generated\Shared\Transfer\GlueResourceTransfer> $included
     * @param string $resourceId
     *
     * @return bool
     */
    protected function isResourceCanBeIncluded(array $included, string $resourceId): bool
    {
        if (!isset($included[$resourceId])) {
            return true;
        }

        $resource = $included[$resourceId];

        return !$resource->getRelationships();
    }

    /**
     * @param string $resourceType
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return bool
     */
    public function hasRelationship(string $resourceType, GlueRequestTransfer $glueRequestTransfer): bool
    {
        if ($glueRequestTransfer->getResource()->getType() === $resourceType) {
            return true;
        }

        $includes = $glueRequestTransfer->getInclude();

        return ($includes && isset($includes[$resourceType])) || (!$includes && !$glueRequestTransfer->getExcludeRelationship());
    }

    /**
     * @param string $resourceName
     * @param array $resources
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return array
     */
    protected function applyRelationshipPlugins(string $resourceName, array $resources, GlueRequestTransfer $glueRequestTransfer): array
    {
        $relationshipPlugins = $this->resourceRelationshipProviderLoader->load($resourceName);
        foreach ($relationshipPlugins as $relationshipPlugin) {
            if (!$this->hasRelationship($relationshipPlugin->getRelationshipResourceType(), $glueRequestTransfer)) {
                continue;
            }

            $relationshipPlugin->addRelationships($resources, $glueRequestTransfer);
        }

        return $resources;
    }

    /**
     * @param string $resourceType
     * @param string|null $parentResourceId
     *
     * @return bool
     */
    protected function canLoadResource(
        string $resourceType,
        ?string $parentResourceId = null
    ): bool {
        $resourceIndex = $resourceType . $parentResourceId;

        return !isset($this->alreadyLoadedResources[$resourceIndex]);
    }
}
