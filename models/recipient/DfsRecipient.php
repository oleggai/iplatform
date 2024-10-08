<?php

namespace common\models\recipient;

use common\components\FormComponent;
use common\models\interfaces\BuildComplaintInterface;

/**
 * Class DfsRecipient
 * @package common\models\recipient
 */
class DfsRecipient extends Recipient implements BuildComplaintInterface
{
    const TYPE = 'dfs';

    const NAME = 'ДФС';

    const LONG_NAME = 'Державна фіскальна служба України';
    const ADDRESS = '04053, місто Київ, Львівська площа, 8';

    public $dfs_name;
    public $dfs_location;

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'dfs_name' => 'Назва',
            'dfs_location' => 'Адреса'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return [
            'dfs_name' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT,
                'options' => [
                    'readonly' => true,
                    'value' => self::LONG_NAME
                ],
            ],
            'dfs_location' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT,
                'options' => [
                    'readonly' => true,
                    'value' => self::ADDRESS
                ],
            ]
        ];
    }
}