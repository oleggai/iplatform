<?php

namespace common\models\question;

use common\db\ActiveRecord;
use common\models\question\Question;
use common\models\UserSubjectSphere;

/**
 * This is the model class for table "user_subject_sphere_question".
 *
 * @property int $id
 * @property int $user_subject_sphere_id
 * @property int $question_id
 * @property int|null $answer
 * @property int|null $fill_status
 * @property int|null $event_risk
 * @property bool $apply
 *
 * @property Question $question
 * @property UserSubjectSphere $userSubjectSphere
 */
class UserSubjectSphereQuestion extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_subject_sphere_question}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_subject_sphere_id', 'question_id'], 'required'],
            [['user_subject_sphere_id', 'question_id'], 'unique', 'targetAttribute' => ['user_subject_sphere_id', 'question_id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_subject_sphere_id' => 'User Subject Sphere ID',
            'question_id' => 'Question ID',
            'answer' => 'Answer',
            'fill_status' => 'Fill Status',
        ];
    }

    /**
     * Gets query for [[Question]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * Gets query for [[UserSubjectSphere]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSubjectSphere()
    {
        return $this->hasOne(UserSubjectSphere::className(), ['id' => 'user_subject_sphere_id']);
    }
}
