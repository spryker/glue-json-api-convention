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
use Spryker\Glue\GlueJsonApiConvention\Response\RelationshipResponseFormatter;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipPluginInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Response
 * @group RelationshipResponseFormatterTest
 * Add your own group annotations below this line
 */
class RelationshipResponseFormatterTest extends Unit
{
    /**
     * @var \SprykerTest\Glue\GlueJsonApiConvention\GlueJsonApiConventionTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testFormatResponseData(): void
    {
        $relationshipResponseFormatter = new RelationshipResponseFormatter();
        $glueResponseTransfer = $relationshipResponseFormatter->buildResponse(
            $this->getGlueResponseTransfer(),
            $this->getGlueRequestTransfer(),
            [
                $this->getResourceRoutePluginMock(),
                $this->getResourceWithoutRelationshipType(),
            ],
        );

        $this->assertNotNull($glueResponseTransfer->getResource()->getRelationships()->getArrayCopy());
    }

    /**
     * @return void
     */
    public function testBuildResponseDataWithExcludeRelationships(): void
    {
        $relationshipResponseFormatter = new RelationshipResponseFormatter();
        $resourceRelationshipPlugin = $this->createMock(ResourceRelationshipPluginInterface::class);

        $glueResponseTransfer = $relationshipResponseFormatter->buildResponse(
            $this->getGlueResponseTransfer(),
            $this->getGlueRequestTransfer()->setExcludeRelationships(true),
            [$resourceRelationshipPlugin],
        );

        $this->assertEmpty($glueResponseTransfer->getResource()->getRelationships()->getArrayCopy());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\ResourceRelationshipPluginInterface
     */
    protected function getResourceRoutePluginMock(): ResourceRelationshipPluginInterface
    {
        $resourceRelationshipPlugin = $this->createMock(ResourceRelationshipPluginInterface::class);

        $resourceRelationshipPlugin
            ->expects($this->once())
            ->method('getRelationshipType')
            ->willReturn('author');

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
     * @return \Generated\Shared\Transfer\GlueRequestTransfer
     */
    protected function getGlueRequestTransfer(): GlueRequestTransfer
    {
        $includedRelationships = [];
        $includedRelationships['type'] = 'author';

        return (new GlueRequestTransfer())
            ->setIncludedRelationships($includedRelationships);
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
