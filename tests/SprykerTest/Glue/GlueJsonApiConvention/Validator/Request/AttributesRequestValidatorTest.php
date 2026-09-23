<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\GlueJsonApiConvention\Validator\Request;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\Decoder\JsonDecoder;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceBridge;
use Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig;
use Spryker\Glue\GlueJsonApiConvention\Validator\Request\AttributesRequestValidator;
use Spryker\Service\UtilEncoding\UtilEncodingService;
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
 * @group AttributesRequestValidatorTest
 * Add your own group annotations below this line
 */
class AttributesRequestValidatorTest extends Unit
{
    protected const string CONTENT_ATTRIBUTES_STRING = '{"data":{"type":"items","attributes":"oops"}}';

    protected const string CONTENT_ATTRIBUTES_OBJECT = '{"data":{"type":"items","attributes":{"name":"Item"}}}';

    protected const string CONTENT_WITHOUT_ATTRIBUTES = '{"data":{"type":"items"}}';

    protected const string CONTENT_DATA_NOT_AN_OBJECT = '{"data":"items"}';

    public function testGivenPostWithStringAttributesWhenValidatingThenTheRequestIsRejectedWithBadRequest(): void
    {
        // Arrange
        $glueRequestTransfer = $this->createRequest(Request::METHOD_POST, static::CONTENT_ATTRIBUTES_STRING);

        // Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer);

        // Assert
        $this->assertFalse($glueRequestValidationTransfer->getIsValid());
        $this->assertSame(Response::HTTP_BAD_REQUEST, $glueRequestValidationTransfer->getStatus());
        $glueErrorTransfer = $glueRequestValidationTransfer->getErrors()->getIterator()->current();
        $this->assertSame(GlueJsonApiConventionConfig::ERROR_CODE_INVALID_REQUEST_BODY_ATTRIBUTES, $glueErrorTransfer->getCode());
        $this->assertSame(GlueJsonApiConventionConfig::ERROR_MESSAGE_INVALID_REQUEST_BODY_ATTRIBUTES, $glueErrorTransfer->getMessage());
    }

    public function testGivenPatchWithStringAttributesWhenValidatingThenTheRequestIsRejected(): void
    {
        // Arrange
        $glueRequestTransfer = $this->createRequest(Request::METHOD_PATCH, static::CONTENT_ATTRIBUTES_STRING);

        // Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer);

        // Assert
        $this->assertFalse($glueRequestValidationTransfer->getIsValid());
    }

    public function testGivenPostWithObjectAttributesWhenValidatingThenTheRequestIsValid(): void
    {
        // Arrange
        $glueRequestTransfer = $this->createRequest(Request::METHOD_POST, static::CONTENT_ATTRIBUTES_OBJECT);

        // Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer);

        // Assert
        $this->assertTrue($glueRequestValidationTransfer->getIsValid());
    }

    public function testGivenBodyWithoutAttributesOrWithoutDataObjectWhenValidatingThenTheRequestIsValid(): void
    {
        // Arrange
        $validator = $this->createValidator();

        // Act & Assert
        $this->assertTrue($validator->validate($this->createRequest(Request::METHOD_POST, static::CONTENT_WITHOUT_ATTRIBUTES))->getIsValid());
        $this->assertTrue($validator->validate($this->createRequest(Request::METHOD_POST, static::CONTENT_DATA_NOT_AN_OBJECT))->getIsValid());
        $this->assertTrue($validator->validate($this->createRequest(Request::METHOD_POST, ''))->getIsValid());
    }

    public function testGivenReadRequestWithStringAttributesWhenValidatingThenTheBodyIsNotJudged(): void
    {
        // Arrange
        $glueRequestTransfer = $this->createRequest(Request::METHOD_GET, static::CONTENT_ATTRIBUTES_STRING);

        // Act
        $glueRequestValidationTransfer = $this->createValidator()->validate($glueRequestTransfer);

        // Assert
        $this->assertTrue($glueRequestValidationTransfer->getIsValid());
    }

    protected function createRequest(string $method, string $content): GlueRequestTransfer
    {
        return (new GlueRequestTransfer())->setMethod($method)->setContent($content);
    }

    protected function createValidator(): AttributesRequestValidator
    {
        return new AttributesRequestValidator(
            new JsonDecoder(new GlueJsonApiConventionToUtilEncodingServiceBridge(new UtilEncodingService())),
        );
    }
}
