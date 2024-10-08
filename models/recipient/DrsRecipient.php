<?php

namespace common\models\recipient;

use common\components\FormComponent;
use common\models\interfaces\BuildComplaintInterface;

/**
 * Class DrsRecipient
 * @package common\models\recipient
 */
class DrsRecipient extends Recipient implements BuildComplaintInterface
{
    const TYPE = 'drs';

    const NAME = 'ДРС';

    const LONG_NAME = 'Державна регуляторна служба України';
    const ADDRESS = '01011, Київ, вул. Арсенальна, 9/11';

    public $drs_name;
    public $drs_location;

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'drs_name' => 'Назва',
            'drs_location' => 'Адреса'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return [
            'drs_name' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT,
                'options' => [
                    'readonly' => true,
                    'value' => self::LONG_NAME
                ],
            ],
            'drs_location' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT,
                'options' => [
                    'readonly' => true,
                    'value' => self::ADDRESS
                ],
            ]
        ];
    }
}