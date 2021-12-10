<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Router;

use ArrayObject;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueVersionTransfer;
use RuntimeException;
use Spryker\Glue\GlueApplication\Resource\ResourceInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface;
use Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface;
use Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRouteWithParentsPluginInterface;
use Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\VersionedResourceRoutePluginInterface;

class RequestResourcePluginFilter implements RequestResourcePluginFilterInterface
{
    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequest
     * @param array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface> $routePlugins
     *
     * @return \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface|null
     */
    public function filterPlugins(GlueRequestTransfer $glueRequest, array $routePlugins): ?JsonApiResourceInterface
    {
        $urlParts = $this->splitUrl($glueRequest);
        $resources = $this->extractResources($urlParts);
        $glueRequest->setResource(array_pop($resources));
        $glueRequest->setParentResources(new ArrayObject($resources));

        if (!$glueRequest->getResource()) {
            return null;
        }

        $filteredRoutePlugins = $this->filterByResource($routePlugins, $glueRequest);
        $filteredRoutePlugins = $this->filterByParents($filteredRoutePlugins, $glueRequest->getParentResources());

        return $this->findBestMatchingRouteByVersion($glueRequest, $filteredRoutePlugins);
    }

    /**
     * @param array<\Spryker\Glue\GlueApplication\Resource\ResourceInterface> $resourcePlugins
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface>
     */
    protected function filterByResource(array $resourcePlugins, GlueRequestTransfer $glueRequestTransfer): array
    {
        return array_filter(
            $resourcePlugins,
            function (ResourceInterface $resourcePlugin) use ($glueRequestTransfer): bool {
                return $resourcePlugin instanceof JsonApiResourceInterface && $glueRequestTransfer->getResource()->getType() === $resourcePlugin->getName();
            },
        );
    }

    /**
     * @param array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface> $routePlugins
     * @param \Generated\Shared\Transfer\GlueVersionTransfer $versionTransfer
     *
     * @return array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface>
     */
    protected function filterByVersion(array $routePlugins, GlueVersionTransfer $versionTransfer): array
    {
        $versionedPlugins = array_filter($routePlugins, function (JsonApiResourceInterface $routePlugin) {
            return $routePlugin instanceof VersionedResourceRoutePluginInterface;
        });

        if (count($versionedPlugins) === 0) {
            return $routePlugins;
        }

        return array_filter(
            $versionedPlugins,
            function (VersionedResourceRoutePluginInterface $resourceRoutePlugin) use ($versionTransfer): bool {
                $matchingVersion = $resourceRoutePlugin->getMatchingVersion();

                if (
                    $matchingVersion->getMajor() === $versionTransfer->getMajor()
                    && $matchingVersion->getMinor() === null
                ) {
                    return true;
                }

                if (
                    $matchingVersion->getMajor() === $versionTransfer->getMajor()
                    && $matchingVersion->getMinor() === $versionTransfer->getMinor()
                ) {
                    return true;
                }

                return false;
            },
        );
    }

    /**
     * @param array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface> $routePlugins
     * @param \ArrayObject<\Generated\Shared\Transfer\GlueResourceTransfer> $parentResources
     *
     * @return array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface>
     */
    protected function filterByParents(array $routePlugins, ArrayObject $parentResources): array
    {
        $pluginsWithParents = array_filter($routePlugins, function (JsonApiResourceInterface $routePlugin) {
            return $routePlugin instanceof ResourceRouteWithParentsPluginInterface;
        });

        if (count($pluginsWithParents) === 0) {
            return $routePlugins;
        }

        if ($parentResources->count() === 0) {
            return [];
        }

        $parentTypes = array_keys($parentResources->getArrayCopy());

        return array_filter(
            $pluginsWithParents,
            function (ResourceRouteWithParentsPluginInterface $pluginWithParents) use ($parentTypes): bool {
                $diff = array_diff($pluginWithParents->getParentResourceTypes(), $parentTypes);

                return count($diff) === 0;
            },
        );
    }

    /**
     * @param array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface> $filteredRoutePlugins
     *
     * @throws \RuntimeException
     *
     * @return \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface|null
     */
    protected function findNewestRoutePlugin(array $filteredRoutePlugins): ?JsonApiResourceInterface
    {
        $newestRoutePlugin = null;

        foreach ($filteredRoutePlugins as $currentRoutePlugin) {
            if (!$currentRoutePlugin instanceof VersionedResourceRoutePluginInterface) {
                continue;
            }

            if (!$newestRoutePlugin) {
                $newestRoutePlugin = $currentRoutePlugin;
            }

            $resourceVersion = $currentRoutePlugin->getMatchingVersion()->getMajor() . $currentRoutePlugin->getMatchingVersion()->getMinor();
            $newestVersion = $newestRoutePlugin->getMatchingVersion()->getMajor() . $newestRoutePlugin->getMatchingVersion()->getMinor();

            if ($resourceVersion > $newestVersion) {
                $newestRoutePlugin = $currentRoutePlugin;
            }
        }

        if ($newestRoutePlugin) {
            return $newestRoutePlugin;
        }

        $nonVersionedPlugins = array_filter($filteredRoutePlugins, function (JsonApiResourceInterface $routePlugin): bool {
            return !$routePlugin instanceof VersionedResourceRoutePluginInterface;
        });

        if (count($nonVersionedPlugins) <= 1) {
            return array_shift($nonVersionedPlugins);
        }

        throw $this->createAmbiguousRouteException();
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequest
     * @param array<\Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface> $filteredRoutePlugins
     *
     * @throws \RuntimeException
     *
     * @return \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface|null
     */
    protected function findBestMatchingRouteByVersion(GlueRequestTransfer $glueRequest, array $filteredRoutePlugins): ?JsonApiResourceInterface
    {
        if ($glueRequest->getVersion() && $glueRequest->getVersion()->getMajor()) {
            $filteredRoutePlugins = $this->filterByVersion($filteredRoutePlugins, $glueRequest->getVersion());

            if (count($filteredRoutePlugins) > 1) {
                throw $this->createAmbiguousRouteException();
            }

            return array_shift($filteredRoutePlugins);
        }

        return $this->findNewestRoutePlugin($filteredRoutePlugins);
    }

    /**
     * @return \RuntimeException
     */
    protected function createAmbiguousRouteException(): RuntimeException
    {
        return new RuntimeException(sprintf(
            'More than one %s plugin was found to match',
            JsonApiResourceInterface::class,
        ));
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequest
     *
     * @return array<string>
     */
    protected function splitUrl(GlueRequestTransfer $glueRequest): array
    {
        return explode('/', trim($glueRequest->getPath(), '\/'));
    }

    /**
     * @param array<string> $urlParts
     *
     * @return array<string, GlueResourceTransfer>
     */
    protected function extractResources(array $urlParts): array
    {
        $urlPartsCount = count($urlParts);
        $resources = [];
        $index = 0;

        while ($index < $urlPartsCount) {
            $type = $urlParts[$index];

            if ($type === '') {
                $index += 2;

                continue;
            }

            $resource = new GlueResourceTransfer();
            $resource->setType($type);
            $resource->setId($urlParts[$index + 1] ?? null);
            $resources[$type] = $resource;

            $index += 2;
        }

        return $resources;
    }
}
