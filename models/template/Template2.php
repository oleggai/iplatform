<?php

namespace common\models\template;
use common\components\FormComponent;
use common\components\PhpWordComponent;
use common\helpers\ArrayHelper;
use common\models\interfaces\BuildComplaintInterface;
use common\models\Regulator;

/**
 * Class Template2
 * @package common\models\template
 */
class Template2 extends Template implements BuildComplaintInterface
{
    const TYPE = 'template_2';

    const TEMPLATE_NAME = 'Звернення до ДРС про відмову від КЗ.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Звернення до ДРС про відмову від КЗ';

    public $event_year;
    protected $_regulatorMultiple;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['event_year', 'regulatorMultiple'], 'required']
        ]);
    }

    /**
     * @return array
     */
    public function getYearList()
    {
        $currentYear = +(new \DateTime())->format('Y');
        $nextYear = $currentYear + 1;

        return [
            $currentYear => $currentYear,
            $nextYear => $nextYear
        ];
    }

    /**
     * @return array
     */
    public function getRegulatorMultiple()
    {
        return empty($this->_regulatorMultiple) ? [] : $this->_regulatorMultiple;
    }

    /**
     * @param $regulatorMultiple
     */
    public function setRegulatorMultiple($regulatorMultiple)
    {
        $this->_regulatorMultiple = (array)$regulatorMultiple;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'event_year' => 'Рік заходу',
            'regulatorMultiple' => 'Органи контролю'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return [
            'event_year' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => $this->getYearList(),
            ],
            'regulatorMultiple' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => ArrayHelper::map(Regulator::find()->all(), 'id', 'name'),
                'options' => [
                    'multiple' => true
                ],
                'get_value' => function() {

                    /* @var $regulators Regulator[]*/
                    $regulators = Regulator::find()->where(['id' => $this->_regulatorMultiple])->all();

                    $list = ArrayHelper::getColumn($regulators, 'name');

                    $newLine = PhpWordComponent::NEW_LINE.'- ';

                    return $newLine.implode(PhpWordComponent::NEW_LINE.'- ', $list);
                }
            ]
        ];
    }
}