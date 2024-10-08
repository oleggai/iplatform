<?php

namespace common\models\template;

use common\components\FormComponent;
use common\models\Dictionary;
use common\models\interfaces\BuildComplaintInterface;

/**
 * Class Template12
 * @package common\models\template
 */
class Template12 extends Template implements BuildComplaintInterface, TaxInterface
{
    const TYPE = 'template_12';

    const TEMPLATE_NAME = 'Скарга на рішення КО.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Скарга на рішення КО';

    public $date_tax = null;
    public $number_tax = null;
    public $date_inspection = null;
    public $inspection_type = null;
    public $inspection_reason = null;
    public $date_act = null;
    public $number_act = null;
    public $article_part = null;
    public $article_number = null;
    public $violation_description = null;
    public $date_remark = null;
    public $number_remark = null;
    public $claim_description = null;
    public $date_decision = null;
    public $number_decision = null;
    public $reason_sanction_description = null;
    public $description_factory = null;
    public $date_received_decision = null;
    public $date_message_claim = null;
    public $inspection_number = null;
    public $address_decision = null;
    public $additional = null;
    public $date_start = null;
    public $date_finish = null;
    public $activity_type = null;
    public $regulator_result = null;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['date_tax', 'number_tax', 'date_inspection', 'inspection_type', 'inspection_reason',
                'date_act', 'number_act', 'article_part', 'article_number', 'violation_description', 'date_remark',
                'number_remark', 'claim_description', 'date_decision', 'number_decision', 'reason_sanction_description',
                'description_factory', 'date_received_decision', 'date_message_claim', 'inspection_number', 'address_decision',
                'date_start', 'date_finish', 'activity_type', 'regulator_result'
            ], 'required']
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'date_tax' => 'Дата податкового повідомлення-рішення',
            'number_tax' => 'Номер податкового повідомлення-рішення',
            'date_inspection' => 'Дата проведення перевірки',
            'inspection_type' => 'Вид перевірки',
            'inspection_reason' => 'Підстава проведення перевірки',
            'date_act' => 'Дата акту перевірки',
            'number_act' => 'Номер акту перевірки',
            'article_part' => 'Пункт статті',
            'article_number' => 'Номер статті',
            'violation_description' => 'Опис порушення',
            'date_remark' => 'Дата листа заперечення',
            'number_remark' => 'Номер листа заперечення',
            'claim_description' => 'Короткий опис претензії',
            'date_decision' => 'Дата повідомлення-рішення',
            'number_decision' => 'Номер повідомлення-рішення',
            'reason_sanction_description' => 'Опис причини застосування санкції',
            'description_factory' => 'Обґрунтування Підприємства неправомірності та незаконності висновків контролюючого органу про порушення Підприємством податкового чи іншого законодавства, з посиланням на нормативно-правові акти, що підтверджують позицію Підприємства',
            'date_received_decision' => 'Дата отримання повідомлення-рішення',
            'date_message_claim' => 'Дата листа з повідомленням про оскарження',
            'inspection_number' => 'Номер перевірки',
            'address_decision' => 'Адреса для надсилання рішення',
            'additional' => 'Додатки',
            'date_start' => 'Дата початку',
            'date_finish' => 'Дата закінчення',
            'activity_type' => 'Предмет перевірки',
            'regulator_result' => 'Висновки КО щодо порушення законодавства'
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
            'date_start' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_start);
                }
            ],
            'date_finish' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_finish);
                }
            ],
            'activity_type' => [],
            'date_tax' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_tax);
                }
            ],
            'number_tax' => [],
            'date_inspection' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_inspection);
                }
            ],
            'regulator_result' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'inspection_number' => [],
            'inspection_type' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => Dictionary::INSPECTION_TYPE,
                'get_value' => function () {
                    return $this->inspection_type ? Dictionary::INSPECTION_TYPE[$this->inspection_type] : '';
                }
            ],
            'inspection_reason' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'date_act' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_act);
                }
            ],
            'number_act' => [],
            'article_part' => [],
            'article_number' => [],
            'violation_description' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'date_remark' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_remark);
                }
            ],
            'number_remark' => [],
            'claim_description' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'date_decision' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_decision);
                }
            ],
            'number_decision' => [],
            'reason_sanction_description' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'description_factory' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'date_received_decision' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_received_decision);
                }
            ],
            'date_message_claim' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_message_claim);
                }
            ],
            'address_decision' => [],
            'additional' => []
        ];
    }
}