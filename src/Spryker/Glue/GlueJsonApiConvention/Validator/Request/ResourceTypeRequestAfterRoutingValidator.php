<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention\Validator\Request;

use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestValidationTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use Spryker\Glue\GlueJsonApiConvention\Decoder\DecoderInterface;
use Spryker\Glue\GlueJsonApiConvention\GlueJsonApiConventionConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceTypeRequestAfterRoutingValidator implements ResourceTypeRequestAfterRoutingValidatorInterface
{
    protected const string KEY_DATA = 'data';

    protected const string KEY_TYPE = 'type';

    public function __construct(protected DecoderInterface $decoder)
    {
    }

    public function validate(GlueRequestTransfer $glueRequestTransfer, ResourceInterface $resource): GlueRequestValidationTransfer
    {
        if (!$this->isRequestMethodWithBody($glueRequestTransfer)) {
            return (new GlueRequestValidationTransfer())->setIsValid(true);
        }

        $content = $glueRequestTransfer->getContent();
        if (!$content) {
            return (new GlueRequestValidationTransfer())->setIsValid(true);
        }

        $decodedContent = $this->decoder->decode($content);
        if (!isset($decodedContent[static::KEY_DATA][static::KEY_TYPE])) {
            return (new GlueRequestValidationTransfer())->setIsValid(true);
        }

        if ($decodedContent[static::KEY_DATA][static::KEY_TYPE] === $resource->getType()) {
            return (new GlueRequestValidationTransfer())->setIsValid(true);
        }

        return (new GlueRequestValidationTransfer())
            ->setIsValid(false)
            ->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->addError(
                (new GlueErrorTransfer())
                    ->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->setCode(GlueJsonApiConventionConfig::ERROR_CODE_INVALID_REQUEST_BODY_RESOURCE_TYPE)
                    ->setMessage(GlueJsonApiConventionConfig::ERROR_MESSAGE_INVALID_REQUEST_BODY_RESOURCE_TYPE),
            );
    }

    protected function isRequestMethodWithBody(GlueRequestTransfer $glueRequestTransfer): bool
    {
        return in_array(
            $glueRequestTransfer->getMethod(),
            [Request::METHOD_POST, Request::METHOD_PATCH],
            true,
        );
    }
}
