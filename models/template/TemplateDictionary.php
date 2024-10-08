<?php

namespace common\models\template;

use common\db\ActiveRecord;

/**
 * Class TemplateDictionary
 * @package common\models\template
 *
 * @property int $id
 * @property string $name
 */
class TemplateDictionary extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%template_dictionary}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required']
        ];
    }
}