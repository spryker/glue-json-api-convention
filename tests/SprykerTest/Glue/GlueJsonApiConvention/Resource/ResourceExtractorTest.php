<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Resource;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface;
use Spryker\Glue\GlueJsonApiConvention\Decoder\JsonDecoder;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceBridge;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceInterface;
use Spryker\Glue\GlueJsonApiConvention\Exception\MissingRequestDataException;
use Spryker\Glue\GlueJsonApiConvention\Resource\ResourceExtractor;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Resource
 * @group ResourceExtractorTest
 *
 * Add your own group annotations below this line
 */
class ResourceExtractorTest extends Unit
{
    /**
     * @var \SprykerTest\Glue\GlueJsonApiConvention\GlueJsonApiConventionTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testResourceExtractor(): void
    {
        $resourceExtractor = new ResourceExtractor($this->createJsonDecoder());
        $parsedRequestBody = $resourceExtractor->extract((new GlueRequestTransfer())->setContent($this->getTestContentData()));

        $this->assertNotNull($parsedRequestBody);
        $this->assertIsArray($parsedRequestBody);
        $this->assertArrayHasKey('attributes', $parsedRequestBody);
        $this->assertArrayHasKey('title', $parsedRequestBody['attributes']);
        $this->assertSame('Test title', $parsedRequestBody['attributes']['title']);
    }

    /**
     * @return void
     */
    public function testMissingRequestDataExceptionForRequestContent(): void
    {
        $resourceExtractor = new ResourceExtractor($this->createJsonDecoder());

        $this->expectException(MissingRequestDataException::class);
        $this->expectExceptionMessage('Wrong request content data.');

        $parsedRequestBody = $resourceExtractor->extract((new GlueRequestTransfer())->setContent('test string'));
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
                'relationships' => [
                    'author' => [
                        'links' => [
                            'self' => '/articles/1/relationships/author',
                            'related' => '/articles/1/author',
                        ],
                        'data' => [
                            'type' => 'people',
                            'id' => '9',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
