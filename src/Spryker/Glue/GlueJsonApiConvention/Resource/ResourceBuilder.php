<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueRestApiConvention\Cors\CorsConstants;
use Spryker\Glue\GlueRestApiConvention\Resource\ResourceRouteCollectionInterface;
use Spryker\Glue\GlueRestApiConvention\Router\ResourceRouteCollection;
use Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface;
use Spryker\Glue\Kernel\BundleControllerAction;
use Spryker\Shared\Kernel\ClassResolver\Controller\AbstractControllerResolver;
use Spryker\Shared\Kernel\ClassResolver\Controller\ControllerNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ResourceBuilder implements ResourceBuilderInterface
{
    /**
     * @var \Spryker\Shared\Kernel\ClassResolver\Controller\AbstractControllerResolver
     */
    protected $controllerResolver;

    /**
     * @param \Spryker\Shared\Kernel\ClassResolver\Controller\AbstractControllerResolver $controllerResolver
     */
    public function __construct(AbstractControllerResolver $controllerResolver)
    {
        $this->controllerResolver = $controllerResolver;
    }

    /**
     * @param \Spryker\Glue\GlueRestApiConvention\Resource\ResourceRouteCollectionInterface $resourceRouteCollection
     * @param \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface $resourceRoutePlugin
     *
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface
     */
    public function buildPreFlightResource(
        ResourceRouteCollectionInterface $resourceRouteCollection,
        ResourceRoutePluginInterface $resourceRoutePlugin
    ): JsonApiResourceInterface {
        return $this->createResource(
            function () use ($resourceRouteCollection): GlueResponseTransfer {
                return (new GlueResponseTransfer())
                    ->addMeta(CorsConstants::HEADER_ACCESS_CONTROL_ALLOW_METHODS, implode(', ', $resourceRouteCollection->getAvailableMethods()));
            },
            $resourceRoutePlugin,
            $resourceRouteCollection,
        );
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\MissingResource
     */
    public function buildMissingResource(): MissingResource
    {
        return new MissingResource(
            (string)Response::HTTP_NOT_FOUND,
            'No route found',
        );
    }

    /**
     * @param \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface $resourceRoutePlugin
     * @param \Spryker\Glue\GlueRestApiConvention\Resource\ResourceRouteCollectionInterface $resourceRouteCollection
     * @param string $requestMethod
     *
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface
     */
    public function buildResource(
        ResourceRoutePluginInterface $resourceRoutePlugin,
        ResourceRouteCollectionInterface $resourceRouteCollection,
        string $requestMethod
    ): JsonApiResourceInterface {
        $method = $this->stripMethodName(
            $resourceRouteCollection->get($requestMethod)[ResourceRouteCollection::CONTROLLER_ACTION],
        );

        try {
            $controller = $this->getController($resourceRoutePlugin, $method);
        } catch (ControllerNotFoundException $exception) {
            return new MissingResource((string)Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }

        if (method_exists($controller, $method)) {
            return $this->createResource([$controller, $method], $resourceRoutePlugin, $resourceRouteCollection);
        }

        $methodAction = $method . 'Action';

        if (method_exists($controller, $methodAction)) {
            return $this->createResource([$controller, $methodAction], $resourceRoutePlugin, $resourceRouteCollection);
        }

        return new MissingResource((string)Response::HTTP_INTERNAL_SERVER_ERROR, sprintf(
            'Neither %s() nor %s() found in %s',
            $method,
            $methodAction,
            get_class($controller),
        ));
    }

    /**
     * @param callable $action
     * @param \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface $resourceRoutePlugin
     * @param \Spryker\Glue\GlueRestApiConvention\Resource\ResourceRouteCollectionInterface $resourceRouteCollection
     *
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface
     */
    protected function createResource(
        callable $action,
        ResourceRoutePluginInterface $resourceRoutePlugin,
        ResourceRouteCollectionInterface $resourceRouteCollection
    ): JsonApiResourceInterface {
        return new JsonApiResource($action, $resourceRoutePlugin, $resourceRouteCollection);
    }

    /**
     * @param \Spryker\Glue\GlueRestApiConventionExtension\Dependency\Plugin\ResourceRoutePluginInterface $resourceRoutePlugin
     * @param string $action
     *
     * @return object
     */
    protected function getController(ResourceRoutePluginInterface $resourceRoutePlugin, string $action): object
    {
        // Reflection would be faster but will trigger an autoloading error if the controller does not exists in core
        $controllerNameParts = explode('\\', $resourceRoutePlugin->getController());
        $controllerName = array_pop($controllerNameParts);
        $controllerName = $controllerName . 'Controller';

        $bundleControllerAction = new BundleControllerAction(
            $resourceRoutePlugin->getModuleName(),
            $controllerName,
            $action,
        );

        return $this->controllerResolver->resolve($bundleControllerAction);
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function stripMethodName(string $method): string
    {
        return str_replace('_', '', $method);
    }
}
