<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Plugin\GlueApplication;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestValidationTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\ApiApplication\Type\ApiConventionPluginInterface;
use Spryker\Glue\GlueApplication\Resource\MissingResource;
use Spryker\Glue\GlueApplication\Resource\ResourceInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface;
use Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig;
use Spryker\Glue\Kernel\AbstractPlugin;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionFactory getFactory()
 */
class JsonApiApiConventionPlugin extends AbstractPlugin implements ApiConventionPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(GlueRequestTransfer $glueRequestTransfer): bool
    {
        //TODO: remove 1
        return 1 || $glueRequestTransfer->getMeta()['content-type'] === 'application/vnd.api-json';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return GlueJsonApiConventionConfig::CONVENTION_JSON_API;
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
    public function buildRequest(GlueRequestTransfer $glueRequestTransfer): GlueRequestTransfer
    {
        foreach ($this->getFactory()->getRequestBuilderPlugins() as $builderRequestPlugin) {
            $glueRequestTransfer = $builderRequestPlugin->build($glueRequestTransfer);
        }

        return $glueRequestTransfer;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueRequestValidationTransfer
     */
    public function validateRequest(GlueRequestTransfer $glueRequestTransfer): GlueRequestValidationTransfer
    {
        foreach ($this->getFactory()->getRequestValidatorPlugins() as $validateRequestPlugin) {
            $glueRequestValidationTransfer = $validateRequestPlugin->validate($glueRequestTransfer);

            if ($glueRequestValidationTransfer->getIsValid() === false) {
                break;
            }
        }

        return $glueRequestValidationTransfer ?? new GlueRequestValidationTransfer();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Resource\ResourceInterface
     */
    public function route(GlueRequestTransfer $glueRequestTransfer, array $routes): ResourceInterface
    {
        foreach ($this->getFactory()->getRouteMatcherPlugins() as $routeMatcherPlugin) {
            $resource = $routeMatcherPlugin->route($glueRequestTransfer, $routes);

            if ($resource instanceof MissingResource === false) {
                break;
            }
        }

        return $resource ?? new MissingResource(Response::HTTP_BAD_REQUEST, 'No route found');
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface $resourceRoutePlugin
     *
     * @return \Generated\Shared\Transfer\GlueRequestValidationTransfer
     */
    public function validateRequestAfterRouting(
        GlueRequestTransfer $glueRequestTransfer,
        ResourceRoutePluginInterface $resourceRoutePlugin
    ): GlueRequestValidationTransfer {
        foreach ($this->getFactory()->getRequestAfterRoutingValidatorPlugins() as $validateRequestAfterRoutingPlugin) {
            $glueRequestValidationTransfer = $validateRequestAfterRoutingPlugin->validateRequest($glueRequestTransfer, $resourceRoutePlugin);
        }

        return $glueRequestValidationTransfer ?? new GlueRequestValidationTransfer();
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
    public function executeResource(ResourceInterface $resource, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        foreach ($this->getFactory()->getResourceExecutorPlugins() as $executeResourcePlugin) {
            $glueResponseTransfer = $executeResourcePlugin->execute($resource, $glueRequestTransfer);

            if ($glueResponseTransfer->getResource() instanceof GlueResourceTransfer) {
                break;
            }
        }

        return $glueResponseTransfer;
    }

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
    public function formatResponse(GlueResponseTransfer $glueResponseTransfer, GlueRequestTransfer $glueRequestTransfer): GlueResponseTransfer
    {
        foreach ($this->getFactory()->getResponseFormatterPlugins() as $formatResponsePlugin) {
            $glueResponseTransfer = $formatResponsePlugin->build($glueResponseTransfer, $glueRequestTransfer);
        }

        return $glueResponseTransfer;
    }

    public function getRoutes(): array
    {
        return [];
    }
}
