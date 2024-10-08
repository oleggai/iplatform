<?php

namespace common\models\template;

use common\components\FormComponent;
use common\models\behaviors\ChooseDocumentTypeBehavior;
use common\models\behaviors\DocumentMultipleBehavior;
use common\models\interfaces\BuildComplaintInterface;

/**
 * Class Template7
 * @package common\models\template
 *
 * @mixin DocumentMultipleBehavior
 */
class Template7 extends Template implements BuildComplaintInterface
{
    const TYPE = 'template_7';

    const TEMPLATE_NAME = 'Звернення щодо продовження строку виконання розпорядчого документа.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Звернення щодо продовження строку виконання розпорядчого документа';

    public $done_points;
    public $request_to_extend;
    public $additional;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['done_points', 'request_to_extend'], 'required']
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
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), $this->getDocumentLabels(), [
            'done_points' => 'Перелік виконаних пунктів розпорядчого документу',
            'request_to_extend' => 'Прохання продовжити строк виконання та обґрунтування необхідності продовження',
            'additional' => 'Додатки (перелік документів, які додаються до звернення)'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return array_merge($this->getDocumentProperties(), [
            'done_points' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'request_to_extend' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ],
            'additional' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ]
        ]);
    }
}