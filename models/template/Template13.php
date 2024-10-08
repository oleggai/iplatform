<?php

namespace common\models\template;

use common\components\FormComponent;
use common\helpers\ArrayHelper;
use common\models\Dictionary;
use common\models\interfaces\BuildComplaintInterface;
use common\models\SubjectActivity;

/**
 * Class Template13
 * @package common\models\template
 */
class Template13 extends Template implements BuildComplaintInterface, TaxInterface
{
    const TYPE = 'template_13';

    const TEMPLATE_NAME = 'Позовна заява щодо оскарження рішення.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Позовна заява щодо оскарження рішення';

    public $subject_type = null;
    protected $_subjectActivityMultiple;
    public $date_inspection = null;
    public $count_inspection = 1;
    public $inspection_type = null;
    public $inspection_ask = null;
    public $date_start = null;
    public $date_end = null;
    public $number_act = null;
    public $date_act = null;
    public $count_act = null;
    public $complaint_number = null;
    public $complaint_date = null;
    public $decision_number = null;
    public $decision_date = null;
    public $number_tax_decision = null;
    public $date_tax_decision = null;
    public $decision_description = null;
    public $circumstance_description = null;
    public $content_claim = null;
    public $argumentation_illegal_decision = null;
    public $link_to_violation_points = null;
    public $additional = null;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['subject_type', 'inspection_type', 'subjectActivityMultiple', 'date_inspection', 'count_inspection', 'inspection_ask',
                'date_start', 'date_end', 'number_act', 'date_act', 'complaint_number', 'complaint_date', 'decision_number', 'decision_date',
                'number_tax_decision', 'date_tax_decision', 'decision_description', 'circumstance_description', 'content_claim', 'additional',
                'argumentation_illegal_decision', 'link_to_violation_points'
            ], 'required'],
            [['date_end'], 'compare', 'compareAttribute' => 'date_start', 'operator' => '>=', 'type' => 'date'],
            ['count_inspection', 'compare', 'compareValue' => 1, 'operator' => '>=']
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'subject_type' => 'Тип суб\'єкта',
            'subjectActivityMultiple' => "Види діяльності суб'єкта",
            'date_inspection' => 'Дата перевірки',
            'count_inspection' => 'Кількість перевірок',
            'inspection_type' => 'Вид перевірки',
            'inspection_ask' => 'Питання проведення перевірки',
            'date_start' => 'Дата початку періоду, за який проводилася перевірка',
            'date_end' => 'Дата закінчення періоду, за який проводилася перевірка',
            'number_act' => 'Номер акту результатів перевірки',
            'date_act' => 'Дата акту результатів перевірки',
            'complaint_number' => 'Номер скарги',
            'complaint_date' => 'Дата скарги',
            'decision_number' => 'Номер рішення',
            'decision_date' => 'Дата рішення',
            'number_tax_decision' => 'Номер податкового повідомлення-рішення',
            'date_tax_decision' => 'Дата податкового повідомлення-рішення',
            'decision_description' => 'Опис рішення',
            'circumstance_description' => 'Опис обставин, якими Позивач обґрунтовує позовні вимоги',
            'content_claim' => 'Зміст позовних вимог',
            'additional' => 'Додатки',
            'argumentation_illegal_decision' => 'Аргументація незаконності та необґрунтованості оскаржуваних рішень',
            'link_to_violation_points' => 'Посилання на підпункти, пункти, статті порушення яких допустили контролюючі органи'
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
            'count_inspection' => [],
            'inspection_type' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => Dictionary::INSPECTION_TYPE,
                'get_value' => function () {
                    return $this->inspection_type ? Dictionary::INSPECTION_TYPE[$this->inspection_type] : '';
                }
            ],
            'inspection_ask' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'date_start' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_start);
                }
            ],
            'date_end' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_end);
                }
            ],
            'number_act' => [],
            'date_act' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_act);
                }
            ],
            'count_act' => [
                'hide' => true,
                'get_value' => function () {

                    $collection = [];

                    for ($i = 0; $i < $this->count_inspection; ++$i) {
                        $collection[] = 'Акта перевірки-' . ($i + 1);
                    }

                    return implode(', ', $collection);
                }
            ],
            'complaint_number' => [],
            'complaint_date' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->complaint_date);
                }
            ],
            'decision_number' => [],
            'decision_date' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->decision_date);
                }
            ],
            'number_tax_decision' => [],
            'date_tax_decision' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function () {
                    return $this->getFormattedDate($this->date_tax_decision);
                }
            ],
            'decision_description' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'circumstance_description' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'content_claim' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'argumentation_illegal_decision' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'link_to_violation_points' => [],
            'additional' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ]
        ];
    }
}