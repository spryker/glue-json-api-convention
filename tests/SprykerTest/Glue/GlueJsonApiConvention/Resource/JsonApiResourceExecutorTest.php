<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Resource;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueApplication\Resource\ResourceInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface;
use Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface;
use Spryker\Glue\GlueJsonApiConvention\Decoder\JsonDecoder;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceBridge;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceExecutor;
use Spryker\Glue\GlueJsonApiConvention\Resource\JsonApiResourceInterface;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceExtractor;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceExtractorInterface;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Resource
 * @group JsonApiResourceExecutorTest
 *
 * Add your own group annotations below this line
 */
class JsonApiResourceExecutorTest extends Unit
{
    /**
     * @var \SprykerTest\Glue\GlueJsonApiConvention\GlueJsonApiConventionTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testIfResourceNotInstanceOfJsonApiResourceInterface(): void
    {
        $resource = $this->createMock(ResourceInterface::class);
        $jsonApiResourceExecutor = new JsonApiResourceExecutor($this->createResourceExtractor());
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueResponseTransfer = $jsonApiResourceExecutor->executeResource($resource, $glueRequestTransfer);
        $this->assertNull($glueResponseTransfer->getStatus());
    }

    /**
     * @return void
     */
    public function testWithoutRequestedId(): void
    {
        $resource = $this->createMock(JsonApiResourceInterface::class);
        $resource->expects($this->once())
            ->method('getResource')
            ->willReturn(function ($glueRequestTransfer, $glueResponse): GlueResponseTransfer {
                $this->assertSame('test', $glueRequestTransfer->getResource()->getType());

                return (new GlueResponseTransfer())->setStatus('200');
            });
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer->setResource((new GlueResourceTransfer())->setType('test'));

        $jsonApiResourceExecutor = new JsonApiResourceExecutor($this->createResourceExtractor());
        $result = $jsonApiResourceExecutor->executeResource($resource, $glueRequestTransfer);
    }

    /**
     * @return void
     */
    public function testWithResourceIdWithoutRequestContent(): void
    {
        $resource = $this->createMock(JsonApiResourceInterface::class);
        $resource->expects($this->once())
            ->method('getResource')
            ->willReturn(function ($resourceId, $glueRequestTransfer, $glueResponse): GlueResponseTransfer {
                $this->assertSame(1, $resourceId);
                $this->assertSame('test', $glueRequestTransfer->getResource()->getType());

                return (new GlueResponseTransfer())->setStatus('200');
            });
        $glueRequestTransfer = new GlueRequestTransfer();
        $glueRequestTransfer->setResource((new GlueResourceTransfer())->setType('test')->setId(1));

        $jsonApiResourceExecutor = new JsonApiResourceExecutor($this->createResourceExtractor());
        $result = $jsonApiResourceExecutor->executeResource($resource, $glueRequestTransfer);
        $this->assertSame('200', $result->getStatus());
    }

    /**
     * @return void
     */
    public function testWithRequestContent(): void
    {
        $resource = $this->createMock(JsonApiResourceInterface::class);
        $resource->expects($this->once())
            ->method('getResourceRoutePlugin')
            ->willReturn($this->getResourceRoutePluginMock());
        $resource->expects($this->once())
            ->method('getResource')
            ->willReturn(function ($resourceTransfer, $glueRequestTransfer, $glueResponse): GlueResponseTransfer {
                $this->assertSame('articles', $glueRequestTransfer->getResource()->getType());

                return (new GlueResponseTransfer())->setStatus('200');
            });

        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setContent($this->getTestContentData())
            ->setResource(new GlueResourceTransfer());

        $jsonApiResourceExecutor = new JsonApiResourceExecutor($this->createResourceExtractor());
        $result = $jsonApiResourceExecutor->executeResource($resource, $glueRequestTransfer);
        $this->assertSame('200', $result->getStatus());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface
     */
    protected function getResourceRoutePluginMock(): ResourceRoutePluginInterface
    {
        $transferMock = $this->createMock(AbstractTransfer::class);
        $pluginMock = $this->createMock(ResourceRoutePluginInterface::class);
        $pluginMock->expects($this->once())
            ->method('getResourceAttributesClassName')
            ->willReturn(get_class($transferMock));

        return $pluginMock;
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceInterface
     */
    protected function getUtilEncodingService(): GlueJsonApiConventionToUtilEncodingServiceInterface
    {
        return new GlueJsonApiConventionToUtilEncodingServiceBridge(
            $this->tester->getLocator()->utilEncoding()->service(),
        );
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface
     */
    protected function createJsonDecoder(): DecoderInterface
    {
        return new JsonDecoder($this->getUtilEncodingService());
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Resource\ResourceExtractorInterface
     */
    protected function createResourceExtractor(): ResourceExtractorInterface
    {
        return new ResourceExtractor($this->createJsonDecoder());
    }

    /**
     * @return string
     */
    protected function getTestContentData(): string
    {
        return json_encode([
            'data' => [
                'type' => 'articles',
                'id' => '1',
                'attributes' => [
                    'title' => 'Test title',
                ],
            ],
        ]);
    }
}
