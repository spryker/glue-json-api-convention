<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention;

use Spryker\Glue\GlueJsonApiConvention\ContentTypeExtractor\ContentTypeExtractor;
use Spryker\Glue\GlueJsonApiConvention\ContentTypeExtractor\ContentTypeExtractorInterface;
use Spryker\Glue\GlueJsonApiConvention\Controller\ControllerResolver;
use Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface;
use Spryker\Glue\GlueJsonApiConvention\Decoder\JsonDecoder;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceInterface;
use Spryker\Glue\GlueJsonApiConvention\Encoder\EncoderInterface;
use Spryker\Glue\GlueJsonApiConvention\Encoder\JsonEncoder;
use Spryker\Glue\GlueJsonApiConvention\Request\RequestRelationshipBuilder;
use Spryker\Glue\GlueJsonApiConvention\Request\RequestRelationshipBuilderInterface;
use Spryker\Glue\GlueJsonApiConvention\Request\RequestSparseFieldBuilder;
use Spryker\Glue\GlueJsonApiConvention\Request\RequestSparseFieldBuilderInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceExecutor;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceExecutorInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceBuilder;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceBuilderInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceExtractor;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceExtractorInterface;
use Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseBuilder;
use Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseBuilderInterface;
use Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseFormatter;
use Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseFormatterInterface;
use Spryker\Glue\GlueJsonApiConvention\Response\RelationshipResponseFormatter;
use Spryker\Glue\GlueJsonApiConvention\Response\RelationshipResponseFormatterInterface;
use Spryker\Glue\GlueJsonApiConvention\Router\RequestResourcePluginFilter;
use Spryker\Glue\GlueJsonApiConvention\Router\RequestResourcePluginFilterInterface;
use Spryker\Glue\GlueJsonApiConvention\Router\RequestRoutingMatcher;
use Spryker\Glue\GlueJsonApiConvention\Router\RequestRoutingMatcherInterface;
use Spryker\Glue\Kernel\AbstractFactory;
use Spryker\Shared\Kernel\ClassResolver\Controller\AbstractControllerResolver;

/**
 * @method \Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig getConfig()
 */
class GlueJsonApiConventionFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Request\RequestSparseFieldBuilderInterface
     */
    public function createRequestSparseFieldBuilder(): RequestSparseFieldBuilderInterface
    {
        return new RequestSparseFieldBuilder();
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Request\RequestRelationshipBuilderInterface
     */
    public function createRequestRelationshipBuilder(): RequestRelationshipBuilderInterface
    {
        return new RequestRelationshipBuilder();
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\ResourceExtractorInterface
     */
    public function createResourceExtractor(): ResourceExtractorInterface
    {
        return new ResourceExtractor(
            $this->createJsonDecoder(),
        );
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Router\RequestRoutingMatcherInterface
     */
    public function createRequestRoutingMatcher(): RequestRoutingMatcherInterface
    {
        return new RequestRoutingMatcher(
            $this->createRequestResourcePluginFilter(),
            $this->createResourceBuilder(),
        );
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Router\RequestResourcePluginFilterInterface
     */
    protected function createRequestResourcePluginFilter(): RequestResourcePluginFilterInterface
    {
        return new RequestResourcePluginFilter();
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\ResourceBuilderInterface
     */
    protected function createResourceBuilder(): ResourceBuilderInterface
    {
        return new ResourceBuilder(
            $this->createControllerResolver(),
        );
    }

    /**
     * @return \Spryker\Shared\Kernel\ClassResolver\Controller\AbstractControllerResolver
     */
    protected function createControllerResolver(): AbstractControllerResolver
    {
        return new ControllerResolver();
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\ContentTypeExtractor\ContentTypeExtractorInterface
     */
    public function createContentTypeExtractor(): ContentTypeExtractorInterface
    {
        return new ContentTypeExtractor();
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface
     */
    public function createJsonDecoder(): DecoderInterface
    {
        return new JsonDecoder($this->getUtilEncodingService());
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Encoder\EncoderInterface
     */
    public function createJsonEncoder(): EncoderInterface
    {
        return new JsonEncoder($this->getUtilEncodingService());
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceExecutorInterface
     */
    public function createJsonApiResourceExecutor(): JsonApiResourceExecutorInterface
    {
        return new JsonApiResourceExecutor(
            $this->createResourceExtractor(),
        );
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseFormatterInterface
     */
    public function createJsonGlueResponseFormatter(): JsonGlueResponseFormatterInterface
    {
        return new JsonGlueResponseFormatter(
            $this->createJsonEncoder(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseBuilderInterface
     */
    public function createJsonGlueResponseBuilder(): JsonGlueResponseBuilderInterface
    {
        return new JsonGlueResponseBuilder(
            $this->createJsonGlueResponseFormatter(),
        );
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Response\RelationshipResponseFormatterInterface
     */
    public function createRelationshipResponseFormatter(): RelationshipResponseFormatterInterface
    {
        return new RelationshipResponseFormatter();
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): GlueJsonApiConventionToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(GlueJsonApiConventionDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RequestBuilderPluginInterface>
     */
    public function getRequestBuilderPlugins(): array
    {
        return $this->getProvidedDependency(GlueJsonApiConventionDependencyProvider::PLUGINS_REQUEST_BUILDER);
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RequestValidatorPluginInterface>
     */
    public function getRequestValidatorPlugins(): array
    {
        return $this->getProvidedDependency(GlueJsonApiConventionDependencyProvider::PLUGINS_REQUEST_VALIDATOR);
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RouteMatcherPluginInterface>
     */
    public function getRouteMatcherPlugins(): array
    {
        return $this->getProvidedDependency(GlueJsonApiConventionDependencyProvider::PLUGINS_ROUTE_MATCHER);
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\RequestAfterRoutingValidatorPluginInterface>
     */
    public function getRequestAfterRoutingValidatorPlugins(): array
    {
        return $this->getProvidedDependency(GlueJsonApiConventionDependencyProvider::PLUGINS_REQUEST_AFTER_ROUTING_VALIDATOR);
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceExecutorInterface>
     */
    public function getResourceExecutorPlugins(): array
    {
        return $this->getProvidedDependency(GlueJsonApiConventionDependencyProvider::PLUGINS_RESOURCE_EXECUTOR);
    }

    /**
     * @return array<\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResponseFormatterPluginInterface>
     */
    public function getResponseFormatterPlugins(): array
    {
        return $this->getProvidedDependency(GlueJsonApiConventionDependencyProvider::PLUGINS_RESPONSE_FORMATTER);
    }
}
