<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\GlueJsonApiConvention\Plugin\GlueApplication;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionFactory;
use Spryker\Glue\GlueJsonApiConvention\Plugin\GlueApplication\AttributesRequestValidatorPlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Plugin
 * @group GlueApplication
 * @group AttributesRequestValidatorPluginTest
 * Add your own group annotations below this line
 */
class AttributesRequestValidatorPluginTest extends Unit
{
    protected const string CONTENT_ATTRIBUTES_STRING = '{"data":{"type":"items","attributes":"oops"}}';

    protected const string CONTENT_ATTRIBUTES_OBJECT = '{"data":{"type":"items","attributes":{"name":"Item"}}}';

    public function testGivenPostWithStringAttributesWhenValidatingThroughThePluginThenBadRequestIsReturned(): void
    {
        // Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())->setMethod(Request::METHOD_POST)->setContent(static::CONTENT_ATTRIBUTES_STRING);

        // Act
        $glueRequestValidationTransfer = (new AttributesRequestValidatorPlugin())->validate($glueRequestTransfer);

        // Assert
        $this->assertFalse($glueRequestValidationTransfer->getIsValid());
        $this->assertSame(Response::HTTP_BAD_REQUEST, $glueRequestValidationTransfer->getStatus());
    }

    public function testGivenPostWithObjectAttributesWhenValidatingThroughThePluginThenTheRequestIsValid(): void
    {
        // Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())->setMethod(Request::METHOD_POST)->setContent(static::CONTENT_ATTRIBUTES_OBJECT);

        // Act
        $glueRequestValidationTransfer = (new AttributesRequestValidatorPlugin())->validate($glueRequestTransfer);

        // Assert
        $this->assertTrue($glueRequestValidationTransfer->getIsValid());
    }

    public function testGivenTheConventionsOwnWiringWhenGettingRequestValidatorPluginsThenTheAttributesValidatorIsIncluded(): void
    {
        // Act
        $requestValidatorPlugins = (new GlueJsonApiConventionFactory())->getRequestValidatorPlugins();

        // Assert
        $this->assertNotEmpty(array_filter(
            $requestValidatorPlugins,
            static fn (object $plugin): bool => $plugin instanceof AttributesRequestValidatorPlugin,
        ));
    }
}
