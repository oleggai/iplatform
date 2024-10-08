<?php

namespace common\models\template;

use common\components\FormComponent;
use common\components\PhpWordComponent;
use common\helpers\ArrayHelper;
use common\models\Dictionary;
use common\models\interfaces\BuildComplaintInterface;
use common\models\Regulator;

/**
 * Class Template9
 * @package common\models\template
 */
class Template9 extends Template implements BuildComplaintInterface
{
    const TYPE = 'template_9';

    const TEMPLATE_NAME = 'Адміністративний позов до Суду.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Адміністративний позов до Суду';

    public $claim_date;
    public $regulator_position;
    public $regulator_pib;
    public $regulator_name;
    public $condition_event;

    public $evidence_count;
    public $doc_count;

    protected $_violationRightMultiple;
    public $anotherViolationRightMultiple;

    protected $_contentClaimMultiple;
    public $anotherContentClaimMultiple;

    protected $_petitionMultiple;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['claim_date', 'regulator_position', 'regulator_pib', 'regulator_name', 'condition_event',
                'violationRightMultiple', 'contentClaimMultiple', 'petitionMultiple'], 'required'],
            [['evidence_count', 'doc_count'], 'number', 'min' => 1],
            $this->getAnotherRule('anotherPetitionMultiple', 'petitionMultiple'),
            $this->getAnotherRule('anotherContentClaimMultiple', 'contentClaimMultiple'),
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
    public function getContentClaimMultiple()
    {
        return empty($this->_contentClaimMultiple) ? [] : $this->_contentClaimMultiple;
    }

    /**
     * @param $contentClaimMultiple
     */
    public function setContentClaimMultiple($contentClaimMultiple)
    {
        $this->_contentClaimMultiple = (array)$contentClaimMultiple;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'claim_date' => 'Дата позову',
            'regulator_position' => 'Посада',
            'regulator_pib' => 'ПІБ посадової особи',
            'regulator_name' => 'Назва органу',
            'condition_event' => 'Обставини заходу',
            'violationRightMultiple' => 'Порушені законні права',
            'anotherViolationRightMultiple' => 'Інші порушені права та/або законні інтереси суб’єкта господарювання',
            'contentClaimMultiple' => 'Зміст позовних вимог',
            'anotherContentClaimMultiple' => 'Інші вимоги',
            'petitionMultiple' => 'Заявлені клопотання',
            'evidence_count' => 'Докази, що підтверджують позовні вимоги (кількість аркушів)',
            'doc_count' => 'Копії позовної заяви та всіх документів, що приєднуються до неї (кількість)'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return [
            'claim_date' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function() {
            return $this->claim_date ? (new \DateTime($this->claim_date))->format('d.m.Y') : '';
                }
            ],
            'regulator_position' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT
            ],
            'regulator_pib' => [
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
            'condition_event' => [
                'form_type' => FormComponent::FORM_TEXTAREA
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

            'contentClaimMultiple' => [
                'form_type' => FormComponent::FORM_SELECT,
                'options' => ['multiple' => true, 'data-another-change' => 'contentClaimMultiple'],
                'data' => Dictionary::CONTENT_CLAIM['зміст позовних вимог'],
                'get_value' => function() {
                    $list = [];
                    foreach ($this->_contentClaimMultiple as $id) {

                        if($id == Dictionary::ANOTHER) {
                            $list[] = $this->anotherContentClaimMultiple;
                        } else {
                            $list[] = Dictionary::CONTENT_CLAIM['зміст позовних вимог'][$id];
                        }
                    }

                    $newLine = PhpWordComponent::NEW_LINE.'- ';

                    return $newLine.implode(PhpWordComponent::NEW_LINE.'- ', $list);
                }
            ],
            'anotherContentClaimMultiple' => [
                'form_type' => FormComponent::FORM_TEXTAREA,
                'options' => ['data-another-text' => 'contentClaimMultiple']
            ],

            'petitionMultiple' => [
                'form_type' => FormComponent::FORM_SELECT,
                'options' => ['multiple' => true],
                'data' => Dictionary::PETITION['клопотання'],
                'get_value' => function() {
                    $list = [];
                    foreach ($this->_petitionMultiple as $id) {
                        $list[] = Dictionary::PETITION['клопотання'][$id];
                    }

                    $newLine = PhpWordComponent::NEW_LINE.'- ';

                    return $newLine.implode(PhpWordComponent::NEW_LINE.'- ', $list);
                }
            ],
            'evidence_count' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT
            ],
            'doc_count' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT
            ]
        ];
    }
}