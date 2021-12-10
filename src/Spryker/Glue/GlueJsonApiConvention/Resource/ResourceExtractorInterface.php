<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

use Generated\Shared\Transfer\GlueRequestTransfer;

interface ResourceExtractorInterface
{
    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return array<mixed>
     */
    public function extract(GlueRequestTransfer $glueRequestTransfer): array;
}
