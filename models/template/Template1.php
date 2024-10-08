<?php

namespace common\models\template;
use common\components\FormComponent;
use common\components\PhpWordComponent;
use common\helpers\ArrayHelper;
use common\models\Dictionary;
use common\models\Inspection;
use common\models\interfaces\BuildComplaintInterface;
use common\models\Sphere;

/**
 * Class Template1
 * @package common\models\template\entities
 */
class Template1 extends Template implements BuildComplaintInterface
{
    const TYPE = 'template_1';

    const TEMPLATE_NAME = 'Недопуск до перевірки.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Недопуск до перевірки';

    public $sphere_id;
    public $event_type;
    public $event_date;
    public $additional;
    protected $_basisMultiple;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['event_type', 'event_date', 'basisMultiple'], 'required'],
            $this->getSphereRule()
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'sphere_id' => 'Сфера державного нагляду (контролю)',
            'event_type' => 'Тип заходу',
            'event_date' => 'Дата заходу',
            'additional' => 'Додатки (перелік документів, які додаються до звернення)',
            'basisMultiple' => 'Підстави недопуску'
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
    public function getBuildProperties()
    {
        return [
            'sphere_id' => $this->getSphereBuildProperty(),
            'event_type' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => Inspection::getEventTypeList(),
                'get_value' => function() {
            return Inspection::getEventTypeList()[$this->event_type];
                }
            ],
            'event_date' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function() {
                    return $this->event_date ? (new \DateTime($this->event_date))->format('m.d.Y') : '';
                }
            ],
            'basisMultiple' => [
                'form_type' => FormComponent::FORM_SELECT,
                'options' => ['multiple' => true],
                'data' => Dictionary::BASIS['підстави недопуску'],
                'get_value' => function() {
                    $list = [];
                    foreach ($this->_basisMultiple as $id) {
                        $list[] = Dictionary::BASIS['підстави недопуску'][$id];
                    }

                    $newLine = PhpWordComponent::NEW_LINE.'- ';

                    return $newLine.implode(PhpWordComponent::NEW_LINE.'- ', $list);
                }
            ],
            'additional' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ]
        ];
    }
}