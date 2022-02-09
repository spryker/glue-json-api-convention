<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Response;

interface ResponseSparseFieldFormatterInterface
{
    /**
     * @param array<string, mixed> $sparseFields
     * @param array<string, mixed> $responseData
     *
     * @return array<string, mixed>
     */
    public function format(array $sparseFields, array $responseData): array;
}
