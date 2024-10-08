<?php

namespace common\models\template;

use common\components\FormComponent;
use common\helpers\ArrayHelper;
use common\models\interfaces\BuildComplaintInterface;
use common\models\SubjectActivity;

/**
 * Class Template14
 * @package common\models\template
 */
class Template14 extends Template implements BuildComplaintInterface, TaxInterface
{
    const TYPE = 'template_14';

    const TEMPLATE_NAME = 'Позовна заява (оскарження ступеня ризику).docx';

    const TEMPLATE_DICTIONARY_NAME = 'Позовна заява (оскарження ступеня ризику)';

    public $subject_type = null;
    protected $_subjectActivityMultiple;
    public $date_inspection = null;
    public $planning_period = null;
    public $date_start = null;
    public $date_message = null;
    public $number_layer_appeal = null;
    public $date_layer_appeal = null;
    public $chapter = null;
    public $sub_chapter = null;
    public $criteria = null;
    public $additional = null;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['subject_type', 'subjectActivityMultiple', 'date_inspection',
                'planning_period', 'date_start', 'date_message', 'number_layer_appeal', 'date_layer_appeal',
                'chapter', 'sub_chapter', 'criteria', 'additional'
            ], 'required'],

            ['planning_period', 'compare', 'compareValue' => 2017, 'operator' => '>='],
            ['planning_period', 'compare', 'compareValue' => (+(new \DateTime())->format('Y') + 1), 'operator' => '<=']
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'subject_type' => 'Тип суб\'єкта',
            'subjectActivityMultiple' => "Види діяльності позивача",
            'date_inspection' => 'Дата коли стало відомо про перевірку',
            'planning_period' => 'Плановий період',
            'date_start' => 'Дата початку періоду, за який проводилася перевірка',
            'date_message' => 'Дата листа',
            'number_layer_appeal' => 'Номер адвокатського звернення',
            'date_layer_appeal' => 'Дата адвокатського звернення',
            'chapter' => 'Розділ плану-графіка',
            'sub_chapter' => 'Підрозділ плану-графіка',
            'criteria' => 'Критерії, за якими було включено до плану графіку',
            'additional' => 'Додатки'
        ]);
    }


    /**
     * @return array
     */
    public function getSubjectActivityMultiple()
    {
        return empty($this->_subjectActivityMultiple) ? [] : $this->_subjectActivityMultiple;
    }

    /**
     * @param $subjectActivityMultiple
     */
    public function setSubjectActivityMultiple($subjectActivityMultiple)
    {
        $this->_subjectActivityMultiple = (array)$subjectActivityMultiple;
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
            'subject_type' => [],
            'subjectActivityMultiple' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => ArrayHelper::map(SubjectActivity::find()->all(), 'id', 'name'),
                'options' => [
                    'multiple' => true
                ],
                'get_value' => function () {

                    /* @var $subject_activities SubjectActivity[] */
                    $subject_activities = SubjectActivity::find()->where(['id' => $this->_subjectActivityMultiple])->all();

                    $list = ArrayHelper::getColumn($subject_activities, 'name');

                    return implode('; ', $list);
                }
            ],
            'date_inspection' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_inspection);
                }
            ],
            'planning_period' => [],
            'date_start' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_start);
                }
            ],
            'date_message' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_message);
                }
            ],
            'number_layer_appeal' => [],
            'date_layer_appeal' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_layer_appeal);
                }
            ],
            'chapter' => [],
            'sub_chapter' => [],
            'criteria' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'additional' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ]
        ];
    }
}