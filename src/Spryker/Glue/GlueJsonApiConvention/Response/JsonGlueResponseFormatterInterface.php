<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Response;

use Generated\Shared\Transfer\GlueRequestTransfer;

interface JsonGlueResponseFormatterInterface
{
    /**
     * @param array<string, mixed> $mainResource
     * @param array<string, mixed> $sparseFields
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    public function formatResponseData(
        array $mainResource,
        array $sparseFields,
        GlueRequestTransfer $glueRequestTransfer
    ): string;

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    public function formatResponseWithEmptyResource(GlueRequestTransfer $glueRequestTransfer): string;
}
