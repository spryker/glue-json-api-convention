<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\GlueJsonApiConvention\Validator\Request;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestValidationTransfer;
use Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface;
use Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AttributesRequestValidator implements RequestValidatorInterface
{
    protected const string KEY_DATA = 'data';

    protected const string KEY_ATTRIBUTES = 'attributes';

    public function __construct(protected DecoderInterface $decoder)
    {
    }

    public function validate(GlueRequestTransfer $glueRequestTransfer): GlueRequestValidationTransfer
    {
        if (!$this->isRequestMethodWithBody($glueRequestTransfer) || !$glueRequestTransfer->getContent()) {
            return (new GlueRequestValidationTransfer())->setIsValid(true);
        }

        $data = $this->decoder->decode($glueRequestTransfer->getContent())[static::KEY_DATA] ?? null;
        $attributes = is_array($data) ? ($data[static::KEY_ATTRIBUTES] ?? null) : null;

        if ($attributes === null || is_array($attributes)) {
            return (new GlueRequestValidationTransfer())->setIsValid(true);
        }

        return (new GlueRequestValidationTransfer())
            ->setIsValid(false)
            ->setStatus(Response::HTTP_BAD_REQUEST)
            ->addError(
                (new GlueErrorTransfer())
                    ->setStatus(Response::HTTP_BAD_REQUEST)
                    ->setCode(GlueJsonApiConventionConfig::ERROR_CODE_INVALID_REQUEST_BODY_ATTRIBUTES)
                    ->setMessage(GlueJsonApiConventionConfig::ERROR_MESSAGE_INVALID_REQUEST_BODY_ATTRIBUTES),
            );
    }

    protected function isRequestMethodWithBody(GlueRequestTransfer $glueRequestTransfer): bool
    {
        return in_array($glueRequestTransfer->getMethod(), [Request::METHOD_POST, Request::METHOD_PATCH], true);
    }
}
