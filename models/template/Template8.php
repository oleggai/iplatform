<?php

namespace common\models\template;

use common\components\FormComponent;
use common\components\PhpWordComponent;
use common\helpers\ArrayHelper;
use common\models\Dictionary;
use common\models\Inspection;
use common\models\interfaces\BuildComplaintInterface;
use common\models\Regulator;

/**
 * Class Template8
 * @package common\models\template
 */
class Template8 extends Template implements BuildComplaintInterface
{
    const TYPE = 'template_8';

    const TEMPLATE_NAME = 'Скарга до ДРС (оскарження процедури та строків здійснення контролю).docx';

    const TEMPLATE_DICTIONARY_NAME = 'Скарга до ДРС (оскарження процедури та строків здійснення контролю)';

    public $regulator_name;
    public $appeal_core;
    public $final_part_description;
    public $event_type;
    public $date_start;
    public $date_finish;

    protected $_basisMultiple;
    public $anotherBasisMultiple;

    protected $_petitionMultiple;
    public $anotherPetitionMultiple;

    protected $_violationRightMultiple;
    public $anotherViolationRightMultiple;

    public $additional;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['regulator_name', 'appeal_core', 'final_part_description', 'event_type',
                'date_start', 'date_finish', 'basisMultiple', 'petitionMultiple', 'violationRightMultiple'], 'required'],
            $this->getAnotherRule('anotherBasisMultiple', 'basisMultiple'),
            $this->getAnotherRule('anotherPetitionMultiple', 'petitionMultiple'),
            $this->getAnotherRule('anotherViolationRightMultiple', 'violationRightMultiple')
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'regulator_name' => 'Назва органу',
            'appeal_core' => 'Суть звернення',
            'final_part_description' => 'Описова заключна частина',
            'event_type' => 'Тип заходу',
            'date_start' => 'Початок перевірки',
            'date_finish' => 'Закінчення перевірки',
            'basisMultiple' => 'Підстави оскарження',
            'anotherBasisMultiple' => 'Інші підстави оскарження',
            'petitionMultiple' => 'Прохання щодо вжиття певних заходів',
            'anotherPetitionMultiple' => 'Інші заходи, що знаходяться в межах повноважень ДРС України',
            'violationRightMultiple' => 'Порушення законних прав',
            'anotherViolationRightMultiple' => 'Інші порушення законних прав',
            'additional' => 'Додатки'
        ]);
    }

    /**
     * @return array
     */
    public function getBasisMultiple()
    {
        return empty($this->_basisMultiple) ? [] : $this->_basisMultiple;
    }

    /**
     * @param $basisMultiple
     */
    public function setBasisMultiple($basisMultiple)
    {
        $this->_basisMultiple = (array)$basisMultiple;
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
    public function getViolationRightMultiple()
    {
        return empty($this->_violationRightMultiple) ? [] : $this->_violationRightMultiple;
    }

    /**
     * @param $violationRightMultiple
     */
    public function setViolationRightMultiple($violationRightMultiple)
    {
        $this->_violationRightMultiple = (array)$violationRightMultiple;
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return [
            'regulator_name' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => ArrayHelper::map(Regulator::find()->all(), 'id', 'name'),
                'get_value' => function() {
                    $regulator = Regulator::find()->where(['id' => $this->regulator_name])->one();
                    return $regulator ? $regulator->name : '';
                }
            ],
            'appeal_core' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'final_part_description' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'event_type' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => Inspection::getEventTypeList(),
                'get_value' => function() {
                    return Inspection::getEventTypeList()[$this->event_type];
                }
            ],
            'date_start' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function() {
                    return $this->date_start ? (new \DateTime($this->date_start))->format('m.d.Y') : '';
                }
            ],
            'date_finish' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function() {
                    return $this->date_finish ? (new \DateTime($this->date_finish))->format('m.d.Y') : '';
                }
            ],
            'basisMultiple' => [
                'form_type' => FormComponent::FORM_SELECT,
                'options' => ['multiple' => true, 'data-another-change' => 'basisMultiple'],
                'data' => Dictionary::BASIS['підстави оскарження'][self::TYPE],
                'get_value' => function() {
                    $list = [];
                    foreach ($this->_basisMultiple as $id) {

                        if($id == Dictionary::ANOTHER) {
                            $list[] = $this->anotherBasisMultiple;
                        } else {
                            $list[] = Dictionary::BASIS['підстави оскарження'][self::TYPE][$id];
                        }
                    }

                    $newLine = PhpWordComponent::NEW_LINE.'- ';

                    return $newLine.implode(PhpWordComponent::NEW_LINE.'- ', $list);
                }
            ],
            'anotherBasisMultiple' => [
                'form_type' => FormComponent::FORM_TEXTAREA,
                'options' => ['data-another-text' => 'basisMultiple']
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

            'violationRightMultiple' => [
                'form_type' => FormComponent::FORM_SELECT,
                'options' => ['multiple' => true, 'data-another-change' => 'violationRightMultiple'],
                'data' => Dictionary::VIOLATION_LEGAL_RIGHTS['порушення законних прав'],
                'get_value' => function() {
                    $list = [];
                    foreach ($this->_violationRightMultiple as $id) {

                        if($id == Dictionary::ANOTHER) {
                            $list[] = $this->anotherViolationRightMultiple;
                        } else {
                            $list[] = Dictionary::VIOLATION_LEGAL_RIGHTS['порушення законних прав'][$id];
                        }
                    }

                    $newLine = PhpWordComponent::NEW_LINE.'- ';

                    return $newLine.implode(PhpWordComponent::NEW_LINE.'- ', $list);
                }
            ],
            'anotherViolationRightMultiple' => [
                'form_type' => FormComponent::FORM_TEXTAREA,
                'options' => ['data-another-text' => 'violationRightMultiple']
            ],
            'additional' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ]
        ];
    }
}