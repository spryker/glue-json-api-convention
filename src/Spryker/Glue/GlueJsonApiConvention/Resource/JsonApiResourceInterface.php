<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

use Spryker\Glue\GlueApplication\Resource\ResourceInterface;

interface JsonApiResourceInterface extends ResourceInterface
{
    public function getName(): string;

    public function getResourceAttributesClassName(): string;
}
