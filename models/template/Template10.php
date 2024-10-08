<?php

namespace common\models\template;

use common\components\FormComponent;
use common\components\PhpWordComponent;
use common\helpers\ArrayHelper;
use common\models\Dictionary;
use common\models\interfaces\BuildComplaintInterface;
use common\models\Regulator;

/**
 * Class Template10
 * @package common\models\template
 */
class Template10 extends Template implements BuildComplaintInterface
{
    const TYPE = 'template_10';

    const TEMPLATE_NAME = 'Скарга до спеціально уповноваженого органу з питань ліцензування.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Скарга до спеціально уповноваженого органу з питань ліцензування';

    const ADMINISTRATIVE_VIOLATION_LIST = [1 => 'відсутнє', 2 => "наявне"];

    public $representative_name;
    public $regulator_name;
    public $core_decision;
    public $number_decision;
    public $date_decision;
    public $core_request;

    public $_petitionMultiple;
    public $anotherPetitionMultiple;

    public $administrative_violation;
    public $additional;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['regulator_name', 'core_decision', 'number_decision', 'date_decision',
                'core_request', 'petitionMultiple', 'administrative_violation'], 'required'],
            $this->getAnotherRule('anotherPetitionMultiple', 'petitionMultiple'),
        ]);
    }

    /**
     * @return array
     */
    public function getPetitionMultiple()
    {
        return empty($this->_petitionMultiple) ? [] : $this->_petitionMultiple;
    }

    /**
     * @param $petitionMultiple
     */
    public function setPetitionMultiple($petitionMultiple)
    {
        $this->_petitionMultiple = (array)$petitionMultiple;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'representative_name' => 'ПІБ/Назва представника скаржника',
            'regulator_name' => 'Назва органу',
            'core_decision' => 'Зміст рішення',
            'number_decision' => 'Номер рішення',
            'date_decision' => 'Дата рішення',
            'core_request' => 'Суть звернення',
            'petitionMultiple' => 'Прохання, щодо вжиття певних заходів',
            'anotherPetitionMultiple' => 'Інші заходи, що знаходяться в межах повноважень ДРС України',
            'administrative_violation' => 'Адміністративне правопорушення',
            'additional' => 'Додатки (матеріали, що аргументовано підтверджують оскарження)'
        ]);
    }

    public function getBuildProperties()
    {
        return [
            'representative_name' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT
            ],
            'regulator_name' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => ArrayHelper::map(Regulator::find()->all(), 'id', 'name'),
                'get_value' => function() {
                    $regulator = Regulator::find()->where(['id' => $this->regulator_name])->one();
                    return $regulator ? $regulator->name : '';
                }
            ],
            'core_decision' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'number_decision' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT
            ],
            'date_decision' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function() {
            return $this->date_decision ? (new \DateTime($this->date_decision))->format('d.m.Y') : '';
                }
            ],
            'core_request' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'petitionMultiple' => [
                'form_type' => FormComponent::FORM_SELECT,
                'options' => ['multiple' => true, 'data-another-change' => 'petitionMultiple'],
                'data' => Dictionary::PETITION['прохання'][self::TYPE],
                'get_value' => function() {
                    $list = [];
                    foreach ($this->_petitionMultiple as $id) {

                        if($id == Dictionary::ANOTHER) {
                            $list[] = $this->anotherPetitionMultiple;
                        } else {
                            $list[] = Dictionary::PETITION['прохання'][self::TYPE][$id];
                        }
                    }

                    $newLine = PhpWordComponent::NEW_LINE.'- ';

                    return $newLine.implode(PhpWordComponent::NEW_LINE.'- ', $list);
                }
            ],
            'anotherPetitionMultiple' => [
                'form_type' => FormComponent::FORM_TEXTAREA,
                'options' => ['data-another-text' => 'petitionMultiple']
            ],
            'administrative_violation' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => self::ADMINISTRATIVE_VIOLATION_LIST,
                'get_value' => function() {
            if($this->administrative_violation) {
                return self::ADMINISTRATIVE_VIOLATION_LIST[$this->administrative_violation];
            }
            return '';
                }
            ],
            'additional' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ]
        ];
    }
}