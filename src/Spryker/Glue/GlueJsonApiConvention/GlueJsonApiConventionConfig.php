<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApiConvention;

use Spryker\Glue\Kernel\AbstractBundleConfig;
use Spryker\Shared\GlueJsonApiConvention\GlueJsonApiConventionConstants;

class GlueJsonApiConventionConfig extends AbstractBundleConfig
{
    public const CONVENTION_JSON_API = 'json_api';

    /**
     * Specification:
     *  - Domain name of glue application to build API links.
     *
     * @api
     *
     * @return string
     */
    public function getGlueDomain(): string
    {
        return $this->get(GlueJsonApiConventionConstants::GLUE_DOMAIN);
    }
}
