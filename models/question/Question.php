<?php

namespace common\models\question;

use common\db\ActiveRecord;
use common\models\document\entities\Document;
use common\models\Model;
use common\models\question\entities\GroupQuestion;
use common\models\question\entities\PartQuestion;
use common\models\question\entities\QuestionItem;
use common\models\question\entities\SubQuestion;
use common\models\question\meta\QuestionQuery;
use common\models\Sphere;
use creocoder\nestedsets\NestedSetsBehavior;

/**
 * Class Question
 * @package common\models\question
 * @mixin NestedSetsBehavior
 *
 * @property int $id
 * @property string $name
 * @property int $sphere_id
 * @property Sphere $sphere
 * @property int $document_id
 * @property Document $document
 * @property string $risk
 * @property UserSubjectSphereQuestion[] $userSubjectSphereQuestions
 */
class Question extends ActiveRecord
{
    const TYPE = 'question';

    const TAB_QUESTION = 'question';
    const TAB_NPA = 'npa';

    const ANSWER_YES = 1;
    const ANSWER_NO = 2;
    const ANSWER_NR = 3;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%question}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'document_id'], 'required'],
            [['risk'], 'string', 'max' => 5]
        ];
    }

    /**
     * @return array
     */
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::class,
                'treeAttribute' => 'tree',
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'depth',
            ],
        ];
    }

    /**
     * @return array
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public function init()
    {
        $this->type = static::TYPE;
        parent::init();
    }

    /**
     * @param array $row
     * @return GroupQuestion|PartQuestion|QuestionItem|SubQuestion|static
     * @throws \Exception
     */
    public static function instantiate($row)
    {
        $type = is_array($row) ? $row['type'] : $row;

        switch ($type) {
            case PartQuestion::TYPE:
                return new PartQuestion();
            case QuestionItem::TYPE:
                return new QuestionItem();
            case GroupQuestion::TYPE:
                return new GroupQuestion();
            case SubQuestion::TYPE:
                return new SubQuestion();
            case self::TYPE:
                return new self();
            default:
                throw new \Exception('Incorrect type');
        }
    }

    /**
     * @return array
     */
    public function getRisks()
    {
        $res = [];
        $riskList = Model::getRiskList();
        if($this->risk) {
            $ids = explode(',', $this->risk);
            foreach ($ids as $id) {
                $res[$id] = $riskList[$id];
            }

        } else {
            $res[Model::RISK_UNDEFINED] = $riskList[Model::RISK_UNDEFINED];
        }

        return $res;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::class, ['id' => 'document_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSphere()
    {
        return $this->hasOne(Sphere::class, ['id' => 'sphere_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSubjectSphereQuestions()
    {
        return $this->hasMany(UserSubjectSphereQuestion::class, ['question_id' => 'id']);
    }

    /**
     * @return QuestionQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        $backtrace = debug_backtrace();

        // need for compatibility single table inheritance and nested sets (creocoder)
        if(isset($backtrace[1]['class']) && ($backtrace[1]['class'] == 'creocoder\nestedsets\NestedSetsBehavior')) {
            return new QuestionQuery(get_called_class());
        }

        if (static::TYPE == self::TYPE) {
            return new QuestionQuery(get_called_class(), ['tableName' => self::tableName()]);
        } else {
            return new QuestionQuery(get_called_class(), ['type' => static::TYPE, 'tableName' => self::tableName()]);
        }
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->type = static::TYPE;

        if (!parent::beforeSave($insert)) {
            return false;
        }

        // here is code

        return true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }
}