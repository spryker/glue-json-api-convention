<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Response;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueResponseTransfer;
use Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig;

class JsonGlueResponseBuilder implements JsonGlueResponseBuilderInterface
{
    /**
     * @var \Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseFormatterInterface
     */
    protected $jsonGlueResponseFormatter;

    /**
     * @param \Spryker\Glue\GlueJsonApiConvention\Response\JsonGlueResponseFormatterInterface $jsonGlueResponseFormatter
     */
    public function __construct(JsonGlueResponseFormatterInterface $jsonGlueResponseFormatter)
    {
        $this->jsonGlueResponseFormatter = $jsonGlueResponseFormatter;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueResponseTransfer $glueResponseTransfer
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueResponseTransfer
     */
    public function buildResponse(
        GlueResponseTransfer $glueResponseTransfer,
        GlueRequestTransfer $glueRequestTransfer
    ): GlueResponseTransfer {
        if ($glueResponseTransfer->getErrors()->count()) {
            $content = $this->jsonGlueResponseFormatter->formatErrorResponse(
                $glueResponseTransfer->getErrors(),
                $glueRequestTransfer,
            );
            $glueResponseTransfer->setContent($content);

            return $glueResponseTransfer;
        }

        if (count($glueResponseTransfer->getResources()) === 0) {
            return $glueResponseTransfer->setContent($this->jsonGlueResponseFormatter->formatResponseWithEmptyResource($glueRequestTransfer));
        }

        $glueResponseTransfer->setFormat(GlueJsonApiConventionConfig::HEADER_CONTENT_TYPE);
        $sparseFields = $this->getSparseFields($glueRequestTransfer);

        return $glueResponseTransfer->setContent($this->jsonGlueResponseFormatter->formatResponseData(
            $glueResponseTransfer,
            $sparseFields,
            $glueRequestTransfer,
        ));
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return array<string, mixed>
     */
    protected function getSparseFields(GlueRequestTransfer $glueRequestTransfer): array
    {
        $sparseFields = [];

        foreach ($glueRequestTransfer->getSparseResources() as $sparseResource) {
            if (array_key_exists($sparseResource->getResourceTypeOrFail(), $sparseFields)) {
                $sparseFields[$sparseResource->getResourceType()] = [];
            }
            $sparseFields[$sparseResource->getResourceType()] = $sparseResource->getFields();
        }

        return $sparseFields;
    }
}
