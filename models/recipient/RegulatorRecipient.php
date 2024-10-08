<?php

namespace common\models\recipient;

use common\components\FormComponent;
use common\helpers\ArrayHelper;
use common\models\interfaces\BuildComplaintInterface;
use common\models\Regulator;
use common\models\template\Template;
use common\models\template\Template4;
use common\models\template\Template5;

/**
 * Class RegulatorRecipient
 * @package common\models\recipient
 */
class RegulatorRecipient extends Recipient implements BuildComplaintInterface
{
    const TYPE = 'regulator';

    const NAME = 'Контролюючий орган';

    public $regulator_name;
    public $regulator_position;
    public $regulator_pib;

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'regulator_name' => 'Назва державного органу',
            'regulator_position' => 'Посада особи органу ДНК',
            'regulator_pib' => 'ПІБ особи органу ДНК'
        ]);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['regulator_name'], 'required'],
    /*        [['regulator_position'], 'required', 'on' => [Template1::TYPE, Template3::TYPE, Template6::TYPE, Template7::TYPE]],
            [['regulator_pib'], 'required', 'on' => [Template1::TYPE, Template3::TYPE, Template4::TYPE, Template6::TYPE, Template7::TYPE]]*/
        ]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getBuildProperties()
    {
        $regulatorQuery = Regulator::find();

        if($this->_template && array_key_exists($this->_template::TYPE, Template::getTaxTemplateList())) {
            $regulators = $regulatorQuery
                ->where(['is_tax' => true])
                ->all();
        } else {
            $regulators = $regulatorQuery->all();
        }

        return [
            'regulator_name' => [
                'form_type' => FormComponent::FORM_SELECT,
                'options' => [
                    // add this attribute if form has dependent spheres from regulator
                    'data-regulator-select' => ''
                ],
                'data' => ArrayHelper::map($regulators, 'id', 'name'),
                'get_value' => function() {
            $regulator = Regulator::findOne(['id' => $this->regulator_name]);
            return $regulator ? $regulator->name : '';
                }
            ],
            'regulator_position' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT,
                'hide' => in_array($this->scenario, [Template4::TYPE, Template5::TYPE])
            ],
            'regulator_pib' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT,
                'hide' => in_array($this->scenario, [Template5::TYPE])
            ]
        ];
    }
}