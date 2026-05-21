<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Validator\Request;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use Spryker\Glue\GlueJsonApiConvention\Decoder\JsonDecoder;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceBridge;
use Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig;
use Spryker\Glue\GlueJsonApiConvention\Validator\Request\ResourceTypeRequestAfterRoutingValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Validator
 * @group Request
 * @group ResourceTypeRequestAfterRoutingValidatorTest
 * Add your own group annotations below this line
 */
class ResourceTypeRequestAfterRoutingValidatorTest extends Unit
{
    protected const RESOURCE_TYPE = 'warehouse-user-assignments';

    /**
     * @var \SprykerTest\Glue\GlueJsonApiConvention\GlueJsonApiConventionTester
     */
    protected $tester;

    public function testValidateReturnsTrueForGetRequest(): void
    {
        //Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())->setMethod(Request::METHOD_GET);
        $resourceMock = $this->createResourceMock(static::RESOURCE_TYPE);

        //Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer, $resourceMock);

        //Assert
        $this->assertTrue($glueRequestValidationTransfer->getIsValid());
    }

    public function testValidateReturnsTrueWhenContentIsEmpty(): void
    {
        //Arrange
        $glueRequestTransfer = (new GlueRequestTransfer())->setMethod(Request::METHOD_POST);
        $resourceMock = $this->createResourceMock(static::RESOURCE_TYPE);

        //Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer, $resourceMock);

        //Assert
        $this->assertTrue($glueRequestValidationTransfer->getIsValid());
    }

    public function testValidateReturnsTrueWhenBodyHasNoDataType(): void
    {
        //Arrange
        $content = json_encode(['data' => ['attributes' => ['userUuid' => 'some-uuid']]]);
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setMethod(Request::METHOD_POST)
            ->setContent($content);
        $resourceMock = $this->createResourceMock(static::RESOURCE_TYPE);

        //Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer, $resourceMock);

        //Assert
        $this->assertTrue($glueRequestValidationTransfer->getIsValid());
    }

    public function testValidateReturnsTrueWhenResourceTypeMatches(): void
    {
        //Arrange
        $content = json_encode(['data' => ['type' => static::RESOURCE_TYPE, 'attributes' => ['userUuid' => 'some-uuid']]]);
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setMethod(Request::METHOD_POST)
            ->setContent($content);
        $resourceMock = $this->createResourceMock(static::RESOURCE_TYPE);

        //Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer, $resourceMock);

        //Assert
        $this->assertTrue($glueRequestValidationTransfer->getIsValid());
    }

    public function testValidateReturnsFalseWhenResourceTypeMismatchOnPost(): void
    {
        //Arrange
        $content = json_encode(['data' => ['type' => 'incorrect', 'attributes' => ['userUuid' => 'some-uuid']]]);
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setMethod(Request::METHOD_POST)
            ->setContent($content);
        $resourceMock = $this->createResourceMock(static::RESOURCE_TYPE);

        //Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer, $resourceMock);

        //Assert
        $this->assertFalse($glueRequestValidationTransfer->getIsValid());
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $glueRequestValidationTransfer->getStatus());
        $this->assertCount(1, $glueRequestValidationTransfer->getErrors());
        $error = $glueRequestValidationTransfer->getErrors()[0];
        $this->assertSame(GlueJsonApiConventionConfig::ERROR_CODE_INVALID_REQUEST_BODY_RESOURCE_TYPE, $error->getCode());
    }

    public function testValidateReturnsFalseWhenResourceTypeMismatchOnPatch(): void
    {
        //Arrange
        $content = json_encode(['data' => ['type' => 'incorrect', 'attributes' => ['userUuid' => 'some-uuid']]]);
        $glueRequestTransfer = (new GlueRequestTransfer())
            ->setMethod(Request::METHOD_PATCH)
            ->setContent($content);
        $resourceMock = $this->createResourceMock(static::RESOURCE_TYPE);

        //Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer, $resourceMock);

        //Assert
        $this->assertFalse($glueRequestValidationTransfer->getIsValid());
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $glueRequestValidationTransfer->getStatus());
    }

    protected function createValidator(): ResourceTypeRequestAfterRoutingValidator
    {
        $utilEncodingService = new GlueJsonApiConventionToUtilEncodingServiceBridge(
            $this->tester->getLocator()->utilEncoding()->service(),
        );

        return new ResourceTypeRequestAfterRoutingValidator(new JsonDecoder($utilEncodingService));
    }

    protected function createResourceMock(string $resourceType): ResourceInterface
    {
        $resourceMock = $this->createMock(ResourceInterface::class);
        $resourceMock->method('getType')->willReturn($resourceType);

        return $resourceMock;
    }
}
