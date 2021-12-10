<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Controller;

use Spryker\Shared\Kernel\ClassResolver\Controller\AbstractControllerResolver;

class ControllerResolver extends AbstractControllerResolver
{
    /**
     * @var string
     */
    public const CLASS_NAME_PATTERN = '\\%s\\Glue\\%s%s\\Controller\\%s';

    /**
     * @return string
     */
    protected function getClassNamePattern(): string
    {
        return static::CLASS_NAME_PATTERN;
    }
}
