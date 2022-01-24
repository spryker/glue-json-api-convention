<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention;

use Codeception\Test\Unit;
use Spryker\Glue\GlueJsonApiConvention\Plugin\GlueJsonApiConvention\AttributesRequestBuilderPlugin;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group GlueJsonApiConvention
 * @group Plugin
 * @group GlueJsonApiConvention
 * @group AttributesRequestBuilderPluginTest
 * Add your own group annotations below this line
 */
class AttributesRequestBuilderPluginTest extends Unit
{
    /**
     * @var \SprykerTest\Glue\GlueJsonApiConvention\GlueJsonApiConventionTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testJsonApiResponseFormatterPlugin(): void
    {
        //Arrange
        $glueRequestTransfer = $this->tester->createGlueRequestTransfer();

        //Act
        $attributesRequestBuilderPlugin = new AttributesRequestBuilderPlugin();
        $attributesRequestBuilderPlugin->build($glueRequestTransfer);
    }
}
