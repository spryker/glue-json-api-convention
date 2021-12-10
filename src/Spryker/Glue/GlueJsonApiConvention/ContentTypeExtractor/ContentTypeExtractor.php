<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\ContentTypeExtractor;

use Generated\Shared\Transfer\GlueRequestTransfer;

class ContentTypeExtractor implements ContentTypeExtractorInterface
{
    /**
     * @var string
     */
    protected const CONTENT_TYPE_REGULAR_EXPRESSION = '/application\/vnd.api\+([a-z]+)/';

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueRequestTransfer
     */
    public function extractContentTypeFormat(GlueRequestTransfer $glueRequestTransfer): GlueRequestTransfer
    {
        $headerParts = $this->matchContentType($glueRequestTransfer->getRequestedFormat());

        if (count($headerParts) >= 2) {
            $glueRequestTransfer->setRequestedFormat($headerParts[1]);
        }

        return $glueRequestTransfer;
    }

    /**
     * @param string|null $contentType
     *
     * @return array<string>
     */
    protected function matchContentType(?string $contentType): array
    {
        $contentTypeParts = [];
        if ($contentType) {
            preg_match(static::CONTENT_TYPE_REGULAR_EXPRESSION, $contentType, $contentTypeParts);
        }

        return $contentTypeParts;
    }
}
