<?php

namespace common\models\template;
use common\components\FormComponent;
use common\models\interfaces\BuildComplaintInterface;
use common\models\Model;

/**
 * Class Template5
 * @package common\models\template
 */
class Template5 extends Template implements BuildComplaintInterface
{
    const TYPE = 'template_5';

    const TEMPLATE_NAME = 'Перегляд визначеного ступения ризику.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Перегляд визначеного ступения ризику';

    public $sphere_id;
    public $doc_number;
    public $doc_date;
    public $justification;
    public $risk;
    public $additional;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['doc_number', 'doc_date', 'justification', 'risk'], 'required'],
            $this->getSphereRule()
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'sphere_id' => 'Сфера державного нагляду (контролю)',
            'doc_number' => 'Номер наказу',
            'doc_date' => 'Дата наказу',
            'justification' => 'Обгрунтування звернення',
            'risk' => 'Ступінь ризику',
            'additional' => 'Додатки (перелік документів, які додаються до звернення)'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return [
            'sphere_id' => $this->getSphereBuildProperty(),
            'doc_number' => [
                'form_type' => FormComponent::FORM_INPUT_TEXT
            ],
            'doc_date' => [
                'form_type' => FormComponent::FORM_DATE,
                'get_value' => function() {
                    return $this->doc_date ? (new \DateTime($this->doc_date))->format('m.d.Y') : '';
                }
            ],
            'justification' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'risk' => [
                'form_type' => FormComponent::FORM_SELECT,
                'data' => Model::getRiskList(),
                'get_value' => function() {
            if($this->risk) {
                return Model::getRiskList()[$this->risk];
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