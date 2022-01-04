<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Request;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceInterface;

class AttributesRequestBuilder implements RequestBuilderInterface
{
    protected GlueJsonApiConventionToUtilEncodingServiceInterface $utilEncodingService;

    /**
     * @param \Spryker\Glue\GlueJsonApiConvention\Dependency\Service\GlueJsonApiConventionToUtilEncodingServiceInterface $utilEncodingService
     */
    public function __construct(GlueJsonApiConventionToUtilEncodingServiceInterface $utilEncodingService)
    {
        $this->utilEncodingService = $utilEncodingService;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @return \Generated\Shared\Transfer\GlueRequestTransfer
     */
    public function extract(GlueRequestTransfer $glueRequestTransfer): GlueRequestTransfer
    {
        if (!$glueRequestTransfer->getContent()) {
            return $glueRequestTransfer;
        }

        $decodedContent = $this->utilEncodingService->decodeJson($glueRequestTransfer->getContent(), true);
        if (!$decodedContent || !isset($decodedContent['data']) || !isset($decodedContent['data']['attributes'])) {
            return $glueRequestTransfer;
        }

        $glueRequestTransfer->setAttributes($decodedContent['data']['attributes']);

        return $glueRequestTransfer;
    }
}
