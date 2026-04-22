<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Response;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRelationshipTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResourceTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceRelationshipLoaderInterface;
use Spryker\Glue\GlueJsonApiConvention\Response\RelationshipResponseBuilder;
use Spryker\Glue\GlueJsonApiConvention\Response\RelationshipResponseBuilderInterface;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipPluginInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Response
 * @group RelationshipResponseBuilderTest
 *
 * Add your own group annotations below this line
 */
class RelationshipResponseBuilderTest extends Unit
{
    /**
     * @var string
     */
    protected const FAKE_PARENT_RESOURCE_NAME = 'parentResource';

    /**
     * @var string
     */
    protected const FAKE_RELATIONSHIP_RESOURCE_NAME = 'relationshipResource';

    public function testLoadRelationshipsShouldIncludeRelationsByPluginAndIncludedRelationships(): void
    {
        //arrange
        $relationshipPluginMock = $this->createResourceRelationshipPluginMock();
        $relationshipPluginMock
            ->method('addRelationships')
            ->willReturnCallback(
                function (array $resources, GlueRequestTransfer $glueRequestTransfer): void {
                    foreach ($resources as $resource) {
                        $resource->addRelationship(
                            (new GlueRelationshipTransfer())->addResource(
                                $this->createResource(static::FAKE_RELATIONSHIP_RESOURCE_NAME, 1),
                            ),
                        );
                    }
                },
            );
        $relationshipPluginMock
            ->method('getRelationshipResourceType')
            ->willReturn(static::FAKE_RELATIONSHIP_RESOURCE_NAME);

        $relationshipLoaderMock = $this->createRelationshipLoaderMock();
        $relationshipLoaderMock
            ->method('load')
            ->willReturn([$relationshipPluginMock]);

        $glueResponseTransfer = (new GlueResponseTransfer())
            ->setResources(new ArrayObject(
                [
                    $this->createResource(static::FAKE_PARENT_RESOURCE_NAME, 1),
                ],
            ));
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource(
                $this->createResource(static::FAKE_PARENT_RESOURCE_NAME, 1),
            )
            ->setIncludedRelationships([static::FAKE_RELATIONSHIP_RESOURCE_NAME]);

        //act
        $glueResponseTransfer = $this->createRelationshipResponseBuilder($relationshipLoaderMock)
            ->buildResponse($glueResponseTransfer, $glueRequestTransfer);

        //assert
        $relationships = $glueResponseTransfer->getResources()[0]->getRelationships();
        $this->assertCount(1, $relationships);
        $this->assertSame(1, $relationships[0]->getResources()[0]->getId());
        $this->assertSame(static::FAKE_RELATIONSHIP_RESOURCE_NAME, $relationships[0]->getResources()[0]->getType());

        $included = $glueResponseTransfer->getIncludedRelationships()->getArrayCopy();
        $this->assertCount(1, $included);
        $this->assertSame(1, $included[0]->getId());
        $this->assertSame(static::FAKE_RELATIONSHIP_RESOURCE_NAME, $included[0]->getType());
    }

    protected function createRelationshipResponseBuilder(
        ?ResourceRelationshipLoaderInterface $relationshipLoaderMock = null
    ): RelationshipResponseBuilderInterface {
        if (!$relationshipLoaderMock) {
            $relationshipLoaderMock = $this->createRelationshipLoaderMock();
        }

        return new RelationshipResponseBuilder($relationshipLoaderMock);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueJsonApiConvention\Resource\ResourceRelationshipLoaderInterface
     */
    protected function createRelationshipLoaderMock(): ResourceRelationshipLoaderInterface
    {
        return $this->getMockBuilder(ResourceRelationshipLoaderInterface::class)
            ->onlyMethods(['load'])
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipPluginInterface
     */
    protected function createResourceRelationshipPluginMock(): ResourceRelationshipPluginInterface
    {
        return $this->getMockBuilder(ResourceRelationshipPluginInterface::class)
            ->onlyMethods(['addRelationships', 'getRelationshipResourceType'])
            ->getMock();
    }

    public function testProcessIncludedDoesNotIncludeBackRelationResourcesOfIncludedResource(): void
    {
        // Arrange
        $servicesType = 'services';
        $servicePointsType = 'service-points';

        // service-point with back-references to plain service stubs (no relationships on them)
        $servicePoint = $this->createResource($servicePointsType, 'sp-1');
        $servicePoint->addRelationship(
            (new GlueRelationshipTransfer())
                ->addResource($this->createResource($servicesType, 'svc-1'))
                ->addResource($this->createResource($servicesType, 'svc-2')),
        );

        $service1 = $this->createResource($servicesType, 'svc-1');
        $service1->addRelationship((new GlueRelationshipTransfer())->addResource($servicePoint));

        $service2 = $this->createResource($servicesType, 'svc-2');
        $service2->addRelationship((new GlueRelationshipTransfer())->addResource($servicePoint));

        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource($this->createResource($servicesType, null))
            ->setIncludedRelationships([$servicePointsType]);

        // Act — test processIncluded directly to avoid loadRelationships cycling on buggy code
        $included = $this->createRelationshipResponseBuilder()
            ->processIncluded([$service1, $service2], $glueRequestTransfer);

        // Assert — included must contain only service-points, not back-referenced services
        $this->assertCount(1, $included);
        $this->assertSame($servicePointsType, $included[0]->getType());
        $this->assertSame('sp-1', $included[0]->getId());
    }

    public function testLoadRelationshipsDoesNotDuplicateRelationshipsOnIncludedResourceSharedByMultipleMainResources(): void
    {
        // Arrange
        $servicesType = 'services';
        $servicePointsType = 'service-points';

        $service1 = $this->createResource($servicesType, 'svc-1');
        $service2 = $this->createResource($servicesType, 'svc-2');
        $servicePoint = $this->createResource($servicePointsType, 'sp-1');

        $servicesByServicePointsPlugin = $this->createResourceRelationshipPluginMock();
        $servicesByServicePointsPlugin->method('getRelationshipResourceType')->willReturn($servicesType);
        $servicesByServicePointsPlugin->method('addRelationships')->willReturnCallback(
            function (array $resources) use ($servicesType): void {
                foreach ($resources as $resource) {
                    $resource->addRelationship(
                        (new GlueRelationshipTransfer())
                            ->addResource((new GlueResourceTransfer())->setType($servicesType)->setId('svc-1'))
                            ->addResource((new GlueResourceTransfer())->setType($servicesType)->setId('svc-2')),
                    );
                }
            },
        );

        $servicePointsByServicesPlugin = $this->createResourceRelationshipPluginMock();
        $servicePointsByServicesPlugin->method('getRelationshipResourceType')->willReturn($servicePointsType);
        $servicePointsByServicesPlugin->method('addRelationships')->willReturnCallback(
            function (array $resources) use ($servicePoint): void {
                foreach ($resources as $resource) {
                    $resource->addRelationship(
                        (new GlueRelationshipTransfer())->addResource($servicePoint),
                    );
                }
            },
        );

        $relationshipLoaderMock = $this->createRelationshipLoaderMock();
        $relationshipLoaderMock->method('load')->willReturnCallback(
            function (string $resourceType) use ($servicesType, $servicePointsType, $servicePointsByServicesPlugin, $servicesByServicePointsPlugin): array {
                if ($resourceType === $servicesType) {
                    return [$servicePointsByServicesPlugin];
                }

                if ($resourceType === $servicePointsType) {
                    return [$servicesByServicePointsPlugin];
                }

                return [];
            },
        );

        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setResource($this->createResource($servicesType, null))
            ->setIncludedRelationships([$servicePointsType]);

        // Act
        $this->createRelationshipResponseBuilder($relationshipLoaderMock)
            ->loadRelationships($servicesType, [$service1, $service2], $glueRequestTransfer);

        // Assert
        $servicesRelationshipIds = [];
        foreach ($servicePoint->getRelationships() as $relationship) {
            foreach ($relationship->getResources() as $resource) {
                $servicesRelationshipIds[] = $resource->getId();
            }
        }

        $this->assertCount(2, $servicesRelationshipIds, 'Each service must appear exactly once.');
        $this->assertSame(
            array_unique($servicesRelationshipIds),
            $servicesRelationshipIds,
            'Each service must appear exactly once in service-point relationships.',
        );
    }

    protected function createResource(string $type, mixed $id): GlueResourceTransfer
    {
        return (new GlueResourceTransfer())
            ->setType($type)
            ->setId($id);
    }
}
