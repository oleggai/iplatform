<?php

namespace common\components\validator;

use common\helpers\StringHelper;
use yii\validators\Validator;

/**
 * Class TrimValidator
 * @package common\components\validators
 */
class TrimValidator extends Validator
{
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $model->{$attribute} = trim($model->{$attribute});

        if($model->{$attribute}) {
            $model->{$attribute} = StringHelper::trim($model->{$attribute});
        } else {
            $model->{$attribute} = null;
        }
    }
}