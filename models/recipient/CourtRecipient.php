<?php

namespace common\models\recipient;

use common\components\FormComponent;
use common\helpers\ArrayHelper;
use common\models\court\Court;
use common\models\interfaces\BuildComplaintInterface;

/**
 * Class CourtRecipient
 * @package common\models\recipient
 */
class CourtRecipient extends Recipient implements BuildComplaintInterface
{
    const TYPE = 'court';

    const NAME = 'Суд';

    public $court_name;
    public $court_location;
    public $court_email;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['court_name', 'court_location'], 'required'],
            [['court_email'], 'email']
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'court_name' => 'Назва суду',
            'court_location' => 'Адреса суду',
            'court_email' => 'E-mail'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return [
            'court_name' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => ArrayHelper::map(Court::find()->all(), 'id', 'name'),
                'get_value' => function() {
                    $court = Court::find()->where(['id' => $this->court_name])->one();
                    return $court ? $court->name : '';
                }
            ],
            'court_location' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT
            ],
            'court_email' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT
            ]
        ];
    }
}