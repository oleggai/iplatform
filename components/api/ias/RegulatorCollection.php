<?php

namespace common\components\api\ias;

use common\components\api\ApiCollectionInterface;

/**
 * Class RegulatorCollection
 * @package common\components\api\ias
 */
class RegulatorCollection extends ApiIas implements ApiCollectionInterface
{
    /**
     * @return string
     */
    public function getAction()
    {
        return 'regulators/list';
    }
}