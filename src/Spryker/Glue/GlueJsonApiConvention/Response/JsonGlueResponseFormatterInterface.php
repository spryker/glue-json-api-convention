<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Response;

use ArrayObject;
use Generated\Shared\Transfer\GlueRequestTransfer;

interface JsonGlueResponseFormatterInterface
{
    /**
     * @param array<\Generated\Shared\Transfer\GlueResourceTransfer> $glueResources
     * @param array<string, mixed> $sparseFields
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    public function formatResponseData(
        array $glueResources,
        array $sparseFields,
        GlueRequestTransfer $glueRequestTransfer
    ): string;

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    public function formatResponseWithEmptyResource(GlueRequestTransfer $glueRequestTransfer): string;

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\RestErrorMessageTransfer> $restErrorMessageTransfers
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return string
     */
    public function formatErrorResponse(ArrayObject $restErrorMessageTransfers, GlueRequestTransfer $glueRequestTransfer): string;
}
