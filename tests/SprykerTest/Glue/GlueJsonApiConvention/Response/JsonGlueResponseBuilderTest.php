<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Response;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Generated\Shared\Transfer\GlueSparseResourceTransfer;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceBridge;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceInterface;
use Spryker\Glue\GlueJsonApiConvention\Encoder\EncoderInterface;
use Spryker\Glue\GlueJsonApiConvention\Encoder\JsonEncoder;
use Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseBuilder;
use Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseFormatter;
use Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseFormatterInterface;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipPluginInterface;
use SprykerTest\Shared\Kernel\Transfer\Fixtures\AbstractTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Response
 * @group JsonGlueResponseBuilderTest
 *
 * Add your own group annotations below this line
 */
class JsonGlueResponseBuilderTest extends Unit
{
    /**
     * @var \SprykerTest\Glue\GlueJsonApiConvention\GlueJsonApiConventionTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testBuildResponseData(): void
    {
        $jsonGlueResponseBuilder = new JsonGlueResponseBuilder($this->createJsonGlueResponseFormatter());
        $buildResponse = $jsonGlueResponseBuilder->buildResponse(
            $this->getGlueResponseTransfer(),
            $this->getGlueRequestTransfer(),
            [
                $this->getResourceRoutePluginMock(),
                $this->getResourceWithoutRelationshipType(),
            ],
        );

        $content = $buildResponse->getContent();

        $this->assertNotNull($content);
        $this->assertIsString($content);
        $this->assertStringContainsString('Author title', $content);
        $this->assertStringContainsString('articles', $content);
        $decodedContent = json_decode($content, true);
        $this->assertArrayHasKey('relationships', $decodedContent['data']);
        $this->assertArrayHasKey('included', $decodedContent);
        $this->assertArrayHasKey('author', $decodedContent['included']);
        $this->assertArrayHasKey('attributes', $decodedContent['included']['author']);
        $this->assertArrayHasKey('type', $decodedContent['included']['author']);
        $this->assertArrayNotHasKey('id', $decodedContent['included']['author']);
        $this->assertArrayNotHasKey('test-type', $decodedContent['included']);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipPluginInterface
     */
    protected function getResourceRoutePluginMock(): ResourceRelationshipPluginInterface
    {
        $relationshipResourceMock = $this->createMock(AbstractTransfer::class);
        $relationshipResourceMock->expects($this->any())
            ->method('toArray')
            ->willReturn(['title' => 'Author title']);
        $resourceRelationshipPlugin = $this->createMock(ResourceRelationshipPluginInterface::class);
        $resourceRelationshipPlugin
            ->expects($this->exactly(1))
            ->method('getRelationshipType')
            ->willReturn('author');
        $resourceRelationshipPlugin
            ->expects($this->once())
            ->method('addRelationships')
            ->willReturnCallback(function (GlueResourceTransfer $resourceTransfer, GlueRequestTransfer $request) use ($relationshipResourceMock) {
                $relationshipResource = new GlueResourceTransfer();
                $relationshipResource->setType('author');
                $relationshipResource->setAttributes($relationshipResourceMock);
                $relationshipResource->setId('9');

                return $resourceTransfer->addRelationship('author', $relationshipResource);
            });

        return $resourceRelationshipPlugin;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipPluginInterface
     */
    protected function getResourceWithoutRelationshipType(): ResourceRelationshipPluginInterface
    {
        $resourceRelationshipPlugin = $this->createMock(ResourceRelationshipPluginInterface::class);

        return $resourceRelationshipPlugin;
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
     * @return \Spryker\Glue\GlueJsonApiConvention\Encoder\EncoderInterface
     */
    protected function createJsonEncoder(): EncoderInterface
    {
        return new JsonEncoder($this->getUtilEncodingService());
    }

    /**
     * @return \Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseFormatterInterface
     */
    protected function createJsonGlueResponseFormatter(): JsonGlueResponseFormatterInterface
    {
        return new JsonGlueResponseFormatter($this->createJsonEncoder());
    }

    /**
     * @return \Generated\Shared\Transfer\GlueRequestTransfer
     */
    protected function getGlueRequestTransfer(): GlueRequestTransfer
    {
        $sparseResources = new ArrayObject();
        $sparseResources[] = (new GlueSparseResourceTransfer())
            ->setResourceType('author')
            ->setFields(['type', 'attributes']);
        $sparseResources[] = (new GlueSparseResourceTransfer())
            ->setFields(['type', 'attributes']);

        $includedRelationships = [];
        $includedRelationships['type'] = 'author';

        return (new GlueRequestTransfer())
            ->setIncludedRelationships($includedRelationships)
            ->setSparseResources($sparseResources);
    }

    /**
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    protected function getGlueResponseTransfer(): GlueResponseTransfer
    {
        $links = new ArrayObject();
        $links['self'] = 'http://example.com/articles/1';

        return (new GlueResponseTransfer())
            ->setResource((new GlueResourceTransfer())
                ->setType('articles')
                ->setId('1')
                ->setLinks($links));
    }
}
