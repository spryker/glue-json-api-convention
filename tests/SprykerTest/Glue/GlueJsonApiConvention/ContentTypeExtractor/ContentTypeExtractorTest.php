<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Request;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\ContentTypeExtractor\ContentTypeExtractor;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Request
 * @group ContentTypeExtractorTest
 * Add your own group annotations below this line
 */
class ContentTypeExtractorTest extends Unit
{
    /**
     * @var string
     */
    protected const CONTENT_TYPE = 'application/vnd.api+json; version=1.0';

    /**
     * @return void
     */
    public function testExtractContentType(): void
    {
        $expectedResourceType = 'json';

        $glueRequestTransfer = (new GlueRequestTransfer())->setRequestedFormat(static::CONTENT_TYPE);

        $contentTypeExtractor = new ContentTypeExtractor();
        $glueRequestTransfer = $contentTypeExtractor->extractContentTypeFormat($glueRequestTransfer);

        $this->assertSame($expectedResourceType, $glueRequestTransfer->getRequestedFormat());
    }
}
