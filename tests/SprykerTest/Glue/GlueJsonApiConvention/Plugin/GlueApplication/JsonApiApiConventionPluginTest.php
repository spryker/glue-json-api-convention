<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Plugin\GlueApplication;

use Codeception\Test\Unit;
use Spryker\Glue\GlueApplication\ApiApplication\Type\ApiConventionPluginInterface;
use Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig;
use Spryker\Glue\GlueJsonApiConvention\Plugin\GlueApplication\JsonApiApiConventionPlugin;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\JsonApiResourceInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Plugin
 * @group GlueApplication
 * @group JsonApiApiConventionPluginTest
 * Add your own group annotations below this line
 */
class JsonApiApiConventionPluginTest extends Unit
{
    /**
     * @var \SprykerTest\Glue\GlueJsonApiConvention\GlueJsonApiConventionTester
     */
    protected $tester;

    /**
     * @return \Spryker\Glue\GlueApplication\ApiApplication\Type\ApiConventionPluginInterface
     */
    public function createJsonApiApiConventionPlugin(): ApiConventionPluginInterface
    {
        return new JsonApiApiConventionPlugin();
    }

    /**
     * @return void
     */
    public function testJsonApiApiConventionPluginIsApplicable(): void
    {
        //Arrange
        $glueRequestTransfer = $this->tester->createGlueRequestTransfer();

        //Act
        $jsonApiApiConventionPlugin = $this->createJsonApiApiConventionPlugin();
        $isApplicable = $jsonApiApiConventionPlugin->isApplicable($glueRequestTransfer);

        //Assert
        $this->assertTrue($isApplicable);
    }

    /**
     * @return void
     */
    public function testJsonApiApiConventionPluginGetName(): void
    {
        //Act
        $jsonApiApiConventionPlugin = $this->createJsonApiApiConventionPlugin();
        $jsonApiApiConventionName = $jsonApiApiConventionPlugin->getName();

        //Assert
        $this->assertSame(GlueJsonApiConventionConfig::CONVENTION_JSON_API, $jsonApiApiConventionName);
    }

    /**
     * @return void
     */
    public function testJsonApiApiConventionPluginGetResourceType(): void
    {
        //Act
        $jsonApiApiConventionPlugin = $this->createJsonApiApiConventionPlugin();
        $jsonApiApiConventionResourceType = $jsonApiApiConventionPlugin->getResourceType();

        //Assert
        $this->assertSame(JsonApiResourceInterface::class, $jsonApiApiConventionResourceType);
    }

    /**
     * @return void
     */
    public function testJsonApiApiConventionPluginBuildRequest(): void
    {
        //Arrange
        $glueRequestTransfer = $this->tester->createGlueRequestTransfer();

        //Act
        $jsonApiApiConventionPlugin = $this->createJsonApiApiConventionPlugin();
        $jsonApiApiConventionPlugin->buildRequest($glueRequestTransfer);
    }

    /**
     * @return void
     */
    public function testJsonApiApiConventionPluginValidateRequest(): void
    {
        //Arrange
        $glueRequestTransfer = $this->tester->createGlueRequestTransfer();

        //Act
        $jsonApiApiConventionPlugin = $this->createJsonApiApiConventionPlugin();
        $jsonApiApiConventionPlugin->validateRequest($glueRequestTransfer);
    }

    /**
     * @return void
     */
    public function testJsonApiApiConventionPluginValidateRequestAfterRouting(): void
    {
        //Arrange
        $glueRequestTransfer = $this->tester->createGlueRequestTransfer();
        $jsonApiResourceInterfaceMock = $this->getMockBuilder(JsonApiResourceInterface::class)->getMock();

        //Act
        $jsonApiApiConventionPlugin = $this->createJsonApiApiConventionPlugin();
        $jsonApiApiConventionPlugin->validateRequestAfterRouting($glueRequestTransfer, $jsonApiResourceInterfaceMock);
    }

    /**
     * @return void
     */
    public function testJsonApiApiConventionPluginFormatResponse(): void
    {
        //Arrange
        $glueRequestTransfer = $this->tester->createGlueRequestTransfer();
        $glueResponseTransfer = $this->tester->createGlueResponseTransfer();

        //Act
        $jsonApiApiConventionPlugin = $this->createJsonApiApiConventionPlugin();
        $jsonApiApiConventionPlugin->formatResponse($glueResponseTransfer, $glueRequestTransfer);
    }
}
