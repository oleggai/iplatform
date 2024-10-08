<?php

namespace common\models\template;
use common\components\FormComponent;
use common\components\PhpWordComponent;
use common\helpers\ArrayHelper;
use common\models\behaviors\ChooseDocumentTypeBehavior;
use common\models\behaviors\DocumentMultipleBehavior;
use common\models\Dictionary;
use common\models\interfaces\BuildComplaintInterface;
use common\models\Regulator;

/**
 * Class Template3
 * @package common\models\template\entities
 *
 * @mixin DocumentMultipleBehavior
 */
class Template3 extends Template implements BuildComplaintInterface
{
    const TYPE = 'template_3';

    const TEMPLATE_NAME = 'Скарги до органу державного нагляду.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Скарги до органу державного нагляду';

    public $appeal_core;
    public $final_part_description;
    public $additional;

    protected $_basisMultiple;
    public $anotherBasisMultiple;

    protected $_petitionMultiple;
    public $anotherPetitionMultiple;

    protected $_violationRightMultiple;
    public $anotherViolationRightMultiple;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['appeal_core', 'final_part_description', 'basisMultiple', 'petitionMultiple', 'violationRightMultiple'], 'required'],
            $this->getAnotherRule('anotherBasisMultiple', 'basisMultiple'),
            $this->getAnotherRule('anotherPetitionMultiple', 'petitionMultiple'),
            $this->getAnotherRule('anotherViolationRightMultiple', 'violationRightMultiple')
        ]);
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            DocumentMultipleBehavior::class
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
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), $this->getDocumentLabels(), [
            'appeal_core' => 'Суть звернення',
            'final_part_description' => 'Описова заключна частина',
            'basisMultiple' => 'Підстави оскарження',
            'anotherBasisMultiple' => 'Інші підстави оскарження',
            'petitionMultiple' => 'Прохання щодо вжиття певних заходів',
            'anotherPetitionMultiple' => 'Інші заходи, що знаходяться в межах повноважень органу або посадової особи',
            'violationRightMultiple' => 'Порушення законних прав',
            'anotherViolationRightMultiple' => 'Інші порушені права та/або законні інтереси суб’єкта господарювання',
            'additional' => 'Додатки'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return array_merge($this->getDocumentProperties(), [
            'appeal_core' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'final_part_description' => [
                'form_type' => FormComponent::FORM_TEXTAREA
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
        ]);
    }
}