<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Resource;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface;
use Spryker\Glue\GlueJsonApiConvention\Exception\MissingRequestDataException;

class ResourceExtractor implements ResourceExtractorInterface
{
    /**
     * @var string
     */
    protected const RESOURCE_DATA = 'data';

    /**
     * @var string
     */
    public const RESOURCE_TYPE = 'type';

    /**
     * @var string
     */
    public const RESOURCE_ID = 'id';

    /**
     * @var string
     */
    public const RESOURCE_ATTRIBUTES = 'attributes';

    /**
     * @var \Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @param \Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface $jsonDecoder
     */
    public function __construct(DecoderInterface $jsonDecoder)
    {
        $this->jsonDecoder = $jsonDecoder;
    }

    /**
     * @param \Generated\Shared\Transfer\GlueRequestTransfer $glueRequestTransfer
     *
     * @throws \Spryker\Glue\GlueJsonApiConvention\Exception\MissingRequestDataException
     *
     * @return array<mixed>
     */
    public function extract(GlueRequestTransfer $glueRequestTransfer): array
    {
        $requestData = $this->jsonDecoder->decode((string)$glueRequestTransfer->getContent());

        if (
            !isset($requestData[static::RESOURCE_DATA]) ||
            !isset($requestData[static::RESOURCE_DATA][static::RESOURCE_TYPE]) ||
            !isset($requestData[static::RESOURCE_DATA][static::RESOURCE_ATTRIBUTES])
        ) {
            throw new MissingRequestDataException('Wrong request content data.');
        }

        return $requestData[static::RESOURCE_DATA];
    }
}
