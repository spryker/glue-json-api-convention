<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Response;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceBridge;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceInterface;
use Spryker\Glue\GlueJsonApiConvention\Encoder\EncoderInterface;
use Spryker\Glue\GlueJsonApiConvention\Encoder\JsonEncoder;
use Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseFormatter;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Response
 * @group JsonGlueResponseFormatterTest
 *
 * Add your own group annotations below this line
 */
class JsonGlueResponseFormatterTest extends Unit
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
        $jsonGlueResponseFormatter = new JsonGlueResponseFormatter($this->createJsonEncoder());
        $formatedResponseData = $jsonGlueResponseFormatter->formatResponseData(
            $this->getMainResourceTestData(),
            $this->getSparseFieldsTestData(),
            (new GlueRequestTransfer())->setExcludeRelationships(false),
        );

        $this->assertNotNull($formatedResponseData);
        $this->assertIsString($formatedResponseData);
        $this->assertStringContainsString('Test title', $formatedResponseData);
        $this->assertStringContainsString('articles', $formatedResponseData);
        $this->assertStringContainsString('test-type', $formatedResponseData);
        $this->assertStringContainsString('relationships', $formatedResponseData);
        $decodedData = json_decode($formatedResponseData, true);
        $this->assertArrayHasKey('relationships', $decodedData['data']);
        $this->assertArrayHasKey('included', $decodedData);
        $this->assertSame('9', $decodedData['included']['author']['id']);
        $this->assertArrayHasKey('attributes', $decodedData['included']['author']);
        $this->assertArrayNotHasKey('attributes', $decodedData['included']['test-type']);
    }

    /**
     * @return void
     */
    public function testFormatResponseDataWithExcludeRelationships(): void
    {
        $jsonGlueResponseFormatter = new JsonGlueResponseFormatter($this->createJsonEncoder());
        $formatedResponseData = $jsonGlueResponseFormatter->formatResponseData(
            $this->getMainResourceTestData(),
            $this->getSparseFieldsTestData(),
            (new GlueRequestTransfer())->setExcludeRelationships(true),
        );

        $this->assertNotNull($formatedResponseData);
        $this->assertIsString($formatedResponseData);
        $decodedData = json_decode($formatedResponseData, true);
        $this->assertArrayNotHasKey('relationships', $decodedData['data']);
        $this->assertArrayNotHasKey('included', $decodedData);
        $this->assertArrayHasKey('attributes', $decodedData['data']);
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
     * @return array
     */
    protected function getMainResourceTestData(): array
    {
        return [
                'type' => 'articles',
                'id' => '1',
                'attributes' => [
                    'title' => 'Test title',
                ],
                'links' => [
                    'self' => 'http://example.com/articles/1',
                ],
                'relationships' => [
                    'author' => [
                        'type' => 'author',
                        'id' => '9',
                        'links' => [
                            'self' => '/articles/1/relationships/author',
                            'related' => '/articles/1/author',
                        ],
                        'attributes' => [
                            'title' => 'Test title',
                        ],
                    ],
                    'test-type' => [
                        'type' => 'test-type',
                        'id' => '2',
                        'links' => [
                            'self' => '/test/4/relationships/author',
                            'related' => '/test/4/author',
                        ],
                        'attributes' => [
                            'title' => 'Test title',
                        ],
                    ],
                ],
        ];
    }

    /**
     * @return array
     */
    protected function getSparseFieldsTestData(): array
    {
        return [
            'test-type' => ['id', 'type'],
        ];
    }
}
