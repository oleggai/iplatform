<?php

namespace common\models\template;

use common\components\FormComponent;
use common\models\Dictionary;
use common\models\interfaces\BuildComplaintInterface;

/**
 * Class Template11
 * @package common\models\template
 */
class Template11 extends Template implements BuildComplaintInterface, TaxInterface
{
    const TYPE = 'template_11';

    const TEMPLATE_NAME = 'Заперечення на акт перевірки.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Заперечення на акт перевірки';

    public $number_act = null;
    public $date_act = null;
    public $date_start = null;
    public $date_end = null;
    public $ask_description = null;
    public $violation_factory = null;
    public $violation_current_legislation = null;
    public $illegal_description = null;
    public $date_received_act = null;
    public $inspection_type = null;
    public $additional = null;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['number_act', 'date_act', 'date_start', 'date_end', 'ask_description',
                'violation_factory', 'illegal_description', 'date_received_act', 'violation_current_legislation',
                'inspection_type', 'additional'
            ],
                'required'],
            [['date_end'], 'compare', 'compareAttribute' => 'date_start', 'operator' => '>=', 'type' => 'date'],
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'number_act' => 'Номер акту результатів перевірки',
            'date_act' => 'Дата акту результатів перевірки',
            'date_start' => 'Дата початку періоду, за який проводилася перевірка питань',
            'date_end' => 'Дата закінчення періоду, за який проводилася перевірка питань',
            'ask_description' => 'Питання, які перевірялись',
            'violation_factory' => 'Порушення підприємства',
            'violation_current_legislation' => 'Порушення норм чинного законодавства',
            'illegal_description' => 'Обґрунтування Підприємства неправомірності та незаконності висновків',
            'date_received_act' => 'Дата отримання акту перевірки',
            'inspection_type' => 'Вид перевірки',
            'additional' => 'Дадатки'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return [
            'created_at' => [
                'hide' => true,
                'get_value' => function () {
                    return (new \DateTime())->format('m.d.Y');
                }
            ],
            'number_act' => [

            ],
            'date_act' => [
                'form_type' => FormComponent::FORM_DATE
            ],
            'date_start' => [
                'form_type' => FormComponent::FORM_DATE
            ],
            'date_end' => [
                'form_type' => FormComponent::FORM_DATE
            ],
            'ask_description' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'violation_factory' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'violation_current_legislation' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'illegal_description' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'date_received_act' => [
                'form_type' => FormComponent::FORM_DATE
            ],
            'inspection_type' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => Dictionary::INSPECTION_TYPE,
                'get_value' => function() {
                    return $this->inspection_type ? Dictionary::INSPECTION_TYPE[$this->inspection_type] : '';
                }
            ],
            'additional' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ]
        ];
    }
}