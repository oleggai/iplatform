<?php

namespace common\models\template;

use common\components\FormComponent;
use common\db\ActiveRecord;
use common\models\Dictionary;
use common\models\meta\SingleTableInheritanceQuery;
use common\models\recipient\Recipient;
use common\models\Sphere;
use yii\helpers\ArrayHelper;

/**
 * Class Template
 * @package common\models
 *
 * @property int $id
 * @property string $template_name Назва файлу-темплейту
 * @property string $recipient
 *
 * @property-read TemplateDictionary $templateDictionary
 */
class Template extends ActiveRecord
{
    const TYPE = 'template';

    const SCENARIO_CREATE = 'scenario_create';

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%template}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['recipient', 'type'], 'required'],
            ['recipient', 'unique', 'targetAttribute' => ['recipient', 'type'], 'message' => 'Шаблон з таким іменем та адресатом вже існує. Для створення шаблону змініть адресата або назву шаблону.']
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $attributes = [];

        try {
            $attributes = array_keys($this->getBuildProperties());

        } catch (\Exception $e) {

        }

        $scenarios[self::SCENARIO_DEFAULT] = array_merge($scenarios[self::SCENARIO_DEFAULT], $attributes);
        $scenarios[self::SCENARIO_CREATE] = ['recipient', 'type'];

        return $scenarios;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'template_name' => 'Назва файлу шаблону',
            'recipient' => 'Адресат'
        ];
    }

    /**
     * @param $attributeName
     * @param $relatedAttribute
     * @return array
     */
    public function getAnotherRule($attributeName, $relatedAttribute)
    {

        $whenClient = <<<JS

function(attribute, value) {
    
        var attributeName = $(attribute.input).attr('data-another-text');
    
        var elem = $('[data-another-change="'+ attributeName +'"]')
        
        return $.inArray("another", $(elem).val()) !== -1;
        
        }

JS;
        return [$attributeName, 'required', 'when' => function() use($relatedAttribute) {
            return in_array(Dictionary::ANOTHER, $this->{$relatedAttribute});
        }, 'whenClient' => $whenClient];
    }

    /**
     * @return array
     */
    public function getSphereRule()
    {
        return [['sphere_id'], 'required', 'when' => function() {
            return false;
        }, 'whenClient' => 'function() {
            
            var options = $("[data-depends-on-regulator] option"); 
            
            return $(options).length;
            
            }'];
    }

    /**
     * @return array
     */
    public function getSphereBuildProperty()
    {
        return [
            'form_type' => FormComponent::FORM_SELECT,
            'data' => ArrayHelper::map(Sphere::find()->all(), 'id', 'name'),
            'options' => [
                'data-depends-on-regulator' => ''
            ],
            'get_value' => function() {
                $sphere = Sphere::findOne(['id' => $this->sphere_id]);
                return $sphere ? $sphere->name : '';
            }
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getTemplateList()
    {
        $list = self::instantiate(false, true);
        $res = [];

        foreach ($list as $template) {
            $res[$template::TYPE] = $template::TEMPLATE_NAME;
        }

        return $res;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getTaxTemplateList()
    {
        $list = self::instantiate(false, true);
        $res = [];

        foreach ($list as $template) {
            if($template instanceof TaxInterface) {
                $res[$template::TYPE] = $template::TEMPLATE_NAME;
            }
        }

        return $res;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $s = DIRECTORY_SEPARATOR;

        return \Yii::getAlias('@complaint_templates') . $s . $this->template_name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateDictionary()
    {
        return $this->hasOne(TemplateDictionary::class, ['type' => 'type']);
    }

    /**
     * @return \common\models\recipient\CourtRecipient|\common\models\recipient\DrsRecipient|\common\models\recipient\RegulatorRecipient|string
     * @throws \Exception
     */
    public function getRecipient()
    {
        if($this->isNewRecord) {
            return '';
        }

        return Recipient::instantiate($this->recipient);
    }

    /**
     * @return string
     */
    public function getName()
    {
        if($this->isNewRecord) {
            $templateDictionary = TemplateDictionary::findOne(['type' => static::TYPE]);
        } else {
            $templateDictionary = $this->templateDictionary;
        }

        return $templateDictionary->name;
    }

    /**
     *
     */
    public function init()
    {
        $this->type = static::TYPE;
        parent::init();
    }

    /**
     * @return SingleTableInheritanceQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        if (static::TYPE == self::TYPE) {
            return new SingleTableInheritanceQuery(get_called_class(), ['tableName' => self::tableName()]);
        } else {
            return new SingleTableInheritanceQuery(get_called_class(), ['type' => static::TYPE, 'tableName' => self::tableName()]);
        }
    }


    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->type = static::TYPE;
        $this->template_name = static::TEMPLATE_NAME;

        if (!parent::beforeSave($insert)) {
            return false;
        }

        // here is code

        return true;
    }

    /**
     * @param array|bool $row
     * @param null $get_object_list
     * @return Template1|Template2|static
     * @throws \Exception
     */
    public static function instantiate($row, $get_object_list = null)
    {
        $type = is_array($row) ? $row['type'] : $row;

        $list[1] = new Template1();
        $list[2] = new Template2();
        $list[3] = new Template3();
        $list[4] = new Template4();
        $list[5] = new Template5();
        $list[6] = new Template6();
        $list[7] = new Template7();
        $list[8] = new Template8();
        $list[9] = new Template9();
        $list[10] = new Template10();
        $list[11] = new Template11();
        $list[12] = new Template12();
        $list[13] = new Template13();
        $list[14] = new Template14();
        $list[15] = new Template15();
        $list[16] = new Template16();
        $list[17] = new Template17();

        if($get_object_list) {
            return $list;
        }

        switch ($type) {
            case Template1::TYPE:
                return $list[1];
            case Template2::TYPE:
                return $list[2];
            case Template3::TYPE:
                return $list[3];
            case Template4::TYPE:
                return $list[4];
            case Template5::TYPE:
                return $list[5];
            case Template6::TYPE:
                return $list[6];
            case Template7::TYPE:
                return $list[7];
            case Template8::TYPE:
                return $list[8];
            case Template9::TYPE:
                return $list[9];
            case Template10::TYPE:
                return $list[10];
            case Template11::TYPE:
                return $list[11];
            case Template12::TYPE:
                return $list[12];
            case Template13::TYPE:
                return $list[13];
            case Template14::TYPE:
                return $list[14];
            case Template15::TYPE:
                return $list[15];
            case Template16::TYPE:
                return $list[16];
            case Template17::TYPE:
                return $list[17];
            default:
                throw new \Exception('Incorrect type');
        }
    }

    /**
     * @param $value
     * @param string $format
     * @return string
     */
    public function getFormattedDate($value, $format = 'd.m.Y')
    {
        return $value ? (new \DateTime($value))->format($format) : '';
    }
}