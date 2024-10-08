<?php

namespace common\models\template;

use common\components\FormComponent;
use common\models\behaviors\ChooseDocumentTypeBehavior;
use common\models\behaviors\DocumentMultipleBehavior;
use common\models\interfaces\BuildComplaintInterface;

/**
 * Class Template6
 * @package common\models\template
 *
 * @mixin DocumentMultipleBehavior
 */
class Template6 extends Template implements BuildComplaintInterface
{
    const TYPE = 'template_6';

    const TEMPLATE_NAME = 'Повідомлення про виконання розпорядчого документа.docx';

    const TEMPLATE_DICTIONARY_NAME = 'Повідомлення про виконання розпорядчого документа';

    public $additional;

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
            'additional' => 'Додатки (перелік документів, які додаються до повідомлення)'
        ]);
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return array_merge($this->getDocumentProperties(), [
            'additional' => [
                'form_type' => FormComponent::FORM_TEXTAREA
            ]
        ]);
    }
}