<?php

namespace models;

use common\components\FormComponent;
use common\db\ActiveRecord;
use common\models\Inspection;
use common\models\interfaces\BuildComplaintInterface;
use common\models\Reason;
use common\models\recipient\CourtRecipient;
use common\models\recipient\DrsRecipient;
use common\models\recipient\Recipient;
use common\models\recipient\RegulatorRecipient;
use common\models\Sphere;
use common\models\StepCategory;
use common\models\template;
use common\models\UserSubject;
use Yii;
use yii\behaviors\AttributeBehavior;

/**
 * This is the model class for table "complaint".
 *
 * @property int $id
 * @property int|null $step_category_id
 * @property int|null $reason_id
 * @property string $reason_name
 * @property string|null $recipient
 * @property string|null $recipient_name
 * @property int|null $sphere_id
 * @property int|null $inspection_id
 * @property string|null $generated_file
 * @property string|null $step
 * @property int $user_subject_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Inspection $inspection
 * @property Reason $reason
 * @property Sphere $sphere
 * @property StepCategory $stepCategory
 * @property UserSubject $userSubject
 */
class Complaint extends ActiveRecord implements BuildComplaintInterface
{
    const STEP_INSPECTION_STAGE = 'inspection_stage';
    const STEP_REASON = 'reason';
    const STEP_RECIPIENT = 'recipient';
    const STEP_COMPLAINT_CREATION = 'complaint_creation';

    const FOR_ANOTHER = 'another';
    const FOR_TAX = 'tax';

    public $clicked_btn = null;
    const CLICKED_BTN = 'generate_btn';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%complaint}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_subject_id'], 'required'],
            [['reason_name', 'clicked_btn', 'step_category_id', 'reason_id', 'sphere_id', 'inspection_id'], 'safe'],
            [['recipient'], 'string', 'max' => 40],
            [['recipient_name'], 'string', 'max' => 1000],
            [['generated_file', 'step'], 'string', 'max' => 50],
            ['step', 'default', 'value' => self::STEP_INSPECTION_STAGE]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $attributes = array_keys($this->getBuildProperties());

        $scenarios[self::SCENARIO_DEFAULT] = array_merge($scenarios[self::SCENARIO_DEFAULT], $attributes);

        return $scenarios;
    }

    /**
     * @return array
     */
    public static function getSteps()
    {
        return [
            Complaint::STEP_INSPECTION_STAGE => 'Етап первірки',
            Complaint::STEP_REASON => 'Причина подання скарги',
            Complaint::STEP_RECIPIENT => 'Адресат скарги',
            Complaint::STEP_COMPLAINT_CREATION => 'Створення скарги'
        ];
    }

    /**
     * @param $step
     * @return false|int|mixed|string
     */
    public static function getNextStep($step)
    {
        $steps = array_keys(self::getSteps());
        $key = array_search($step, $steps);

        return isset($steps[$key + 1]) ? $steps[$key + 1] : $key;
    }

    /**
     * @return null|string
     */
    public function detectFor()
    {
        return $this->reason ? $this->reason->for : null;
    }

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->detectFor();
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
                ],
                'value' => function ($event) {
                    return (new \DateTime())->format('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'step_category_id' => 'Step Category ID',
            'reason_id' => 'Reason ID',
            'reason_name' => 'Підстава звернення',
            'recipient' => 'Recipient',
            'recipient_name' => 'Recipient Name',
            'sphere_id' => 'Sphere ID',
            'inspection_id' => 'Inspection ID',
            'generated_file' => 'Generated File',
            'step' => 'Step',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return array
     */
    public function getBuildProperties()
    {
        return [

        ];
    }

    /**
     * @return template\Template|mixed|null
     */
    public function getTemplate()
    {
        if($this->isNewRecord || !$this->recipient) {
            return null;
        }

        $templates = $this->reason ? $this->reason->templates : [];

        foreach ($templates as $template) {
            if($template->recipient == $this->recipient) {
                return $template;
            }
        }

        return null;
    }

    /**
     * @return CourtRecipient|DrsRecipient|RegulatorRecipient|null
     * @throws \Exception
     */
    public function getRecipient()
    {
        if($this->isNewRecord || !$this->recipient) {
            return null;
        }

        return Recipient::instantiate($this->recipient);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSubject()
    {
        return $this->hasOne(UserSubject::class, ['id' => 'user_subject_id']);
    }

    /**
     * Gets query for [[Inspection]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInspection()
    {
        return $this->hasOne(Inspection::className(), ['id' => 'inspection_id']);
    }

    /**
     * Gets query for [[Reason]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReason()
    {
        return $this->hasOne(Reason::className(), ['id' => 'reason_id']);
    }

    /**
     * Gets query for [[Sphere]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSphere()
    {
        return $this->hasOne(Sphere::className(), ['id' => 'sphere_id']);
    }

    /**
     * Gets query for [[StepCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStepCategory()
    {
        return $this->hasOne(StepCategory::className(), ['id' => 'step_category_id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if($this->step_category_id) {
            $this->step = self::STEP_REASON;
        }

        if($this->recipient) {
            $this->step = self::STEP_RECIPIENT;
        }

        if($this->generated_file) {
            $this->step = self::STEP_COMPLAINT_CREATION;
        }

        if($this->reason) {
            $this->reason_name = $this->reason->name;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        if ($this->generated_file && is_file(\Yii::getAlias('@complaint_files') . DIRECTORY_SEPARATOR . $this->generated_file)) {
            unlink(\Yii::getAlias('@complaint_files') . DIRECTORY_SEPARATOR . $this->generated_file);
        }

        return parent::beforeDelete();
    }

}
