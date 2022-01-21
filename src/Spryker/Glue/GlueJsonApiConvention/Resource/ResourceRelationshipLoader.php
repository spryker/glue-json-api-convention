<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

class ResourceRelationshipLoader implements ResourceRelationshipLoaderInterface
{
    /**
     * @var array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\StorefrontApiRelationshipProviderPluginInterface>
     */
    protected $resourceRelationships;

    /**
     * @param array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\StorefrontApiRelationshipProviderPluginInterface> $resourceRelationships
     */
    public function __construct(array $resourceRelationships)
    {
        $this->resourceRelationships = $resourceRelationships;
    }

    /**
     * @param string $resourceName
     *
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipPluginInterface>
     */
    public function load(string $resourceName): array
    {
        foreach ($this->resourceRelationships as $resourceRelationship) {
            $resourceRelationshipCollection = $resourceRelationship->getResourceRelationshipCollection();
            if ($resourceRelationshipCollection->hasRelationships($resourceName)) {
                return $resourceRelationshipCollection->getRelationships($resourceName);
            }
        }

        return [];
    }
}
