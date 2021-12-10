<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention;

use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceBridge;
use Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention\JsonApiResponseBuilderPlugin;
use Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention\RequestSparseFieldBuilderPlugin;
use Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention\RouterMatcherPlugin;
use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;

/**
 * @method \Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig getConfig()
 */
class GlueJsonApiConventionDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @var string
     */
    public const PLUGINS_REQUEST_BUILDER = 'PLUGINS_REQUEST_BUILDER';

    /**
     * @var string
     */
    public const PLUGINS_REQUEST_VALIDATOR = 'PLUGINS_REQUEST_VALIDATOR';

    /**
     * @var string
     */
    public const PLUGINS_ROUTE_MATCHER = 'PLUGINS_ROUTE_MATCHER';

    /**
     * @var string
     */
    public const PLUGINS_REQUEST_AFTER_ROUTING_VALIDATOR = 'PLUGINS_REQUEST_AFTER_ROUTING_VALIDATOR';

    /**
     * @var string
     */
    public const PLUGINS_RESOURCE_EXECUTOR = 'PLUGINS_RESOURCE_EXECUTOR';

    /**
     * @var string
     */
    public const PLUGINS_RESPONSE_FORMATTER = 'PLUGINS_RESPONSE_FORMATTER';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addRequestBuilderPlugins($container);
        $container = $this->addRequestValidatorPlugins($container);
        $container = $this->addRouteMatcherPlugins($container);
        $container = $this->addRequestAfterRoutingValidatorPlugins($container);
        $container = $this->addResourceExecutorPlugins($container);
        $container = $this->addResponseFormatterPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new GlueJsonApiConventionToUtilEncodingServiceBridge($container->getLocator()->utilEncoding()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addRequestBuilderPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_REQUEST_BUILDER, function (Container $container) {
            return $this->getRequestBuilderPlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addRequestValidatorPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_REQUEST_VALIDATOR, function (Container $container) {
            return $this->getRequestValidatorPlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addRouteMatcherPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_ROUTE_MATCHER, function (Container $container) {
            return $this->getRouteMatcherPlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addRequestAfterRoutingValidatorPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_REQUEST_AFTER_ROUTING_VALIDATOR, function (Container $container) {
            return $this->getRequestAfterRoutingValidatorPlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addResourceExecutorPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_RESOURCE_EXECUTOR, function (Container $container) {
            return $this->getResourceExecutorPlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addResponseFormatterPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_RESPONSE_FORMATTER, function (Container $container) {
            return $this->getResponseFormatterPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface>
     */
    protected function getApplicationPlugins(): array
    {
        return [];
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RequestBuilderPluginInterface>
     */
    protected function getRequestBuilderPlugins(): array
    {
        return [
            new RequestSparseFieldBuilderPlugin(),
        ];
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RequestValidatorPluginInterface>
     */
    protected function getRequestValidatorPlugins(): array
    {
        return [];
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RouteMatcherPluginInterface>
     */
    protected function getRouteMatcherPlugins(): array
    {
        return [
            // StorageRouter(),
            new RouterMatcherPlugin(),
        ];
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RequestAfterRoutingValidatorPluginInterface>
     */
    protected function getRequestAfterRoutingValidatorPlugins(): array
    {
        return [];
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceExecutorInterface>
     */
    protected function getResourceExecutorPlugins(): array
    {
        return [];
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResponseFormatterPluginInterface>
     */
    protected function getResponseFormatterPlugins(): array
    {
        return [
            new JsonApiResponseBuilderPlugin(),
        ];
    }
}
