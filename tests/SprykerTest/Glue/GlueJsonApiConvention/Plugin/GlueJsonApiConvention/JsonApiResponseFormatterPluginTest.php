<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention;

use Codeception\Test\Unit;
use Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention\JsonApiResponseFormatterPlugin;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Plugin
 * @group GlueJsonApiConvention
 * @group JsonApiResponseFormatterPluginTest
 * Add your own group annotations below this line
 */
class JsonApiResponseFormatterPluginTest extends Unit
{
    /**
     * @var \SprykerTest\Glue\GlueJsonApiConvention\GlueJsonApiConventionTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testJsonApiResponseFormatterPluginTest(): void
    {
        //arrange
        $glueRequestTransfer = $this->tester->createGlueRequestTransfer();
        $glueResponseTransfer = $this->tester->createGlueResponseTransfer();

        //act
        $jsonApiResponseFormatterPlugin = new JsonApiResponseFormatterPlugin();
        $glueResponseTransfer = $jsonApiResponseFormatterPlugin->build($glueResponseTransfer, $glueRequestTransfer);
    }
}
