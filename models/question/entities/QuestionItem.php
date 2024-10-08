<?php

namespace common\models\question\entities;

use common\components\NotificationComponent;
use common\components\QuestionComponent;
use common\models\notification\interfaces\NotifiableInterface;
use common\models\notification\QuestionNotification;
use common\models\question\Question;
use common\models\question\UserSubjectSphereQuestion;
use common\models\UserSubjectSphere;

/**
 * Class QuestionItem
 * @package common\models\question\entities
 *
 * @property int $api_id
 * @property string $question_num
 */
class QuestionItem extends Question implements NotifiableInterface
{
    const TYPE = 'question_item';

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function notify()
    {
        /* @var $notificationComponent NotificationComponent */
        $notificationComponent = \Yii::$app->notificationComponent;
        /* @var $questionComponent QuestionComponent */
        $questionComponent = \Yii::$app->questionComponent;

        $query = UserSubjectSphere::find()->with(['userSubject.user'])
            ->where(['sphere_id' => $this->sphere_id]);

        foreach ($query->all() as $userSubjectSphere) {

            $userSubjectSphereQuestion = UserSubjectSphereQuestion::find()
                ->where(['user_subject_sphere_id' => $userSubjectSphere->id, 'question_id' => $this->id])
                ->one();
            // Не слати нотіфікейшени по тим вимогам, які юзер проставив що вони не застосовуються
            if($userSubjectSphereQuestion && !$userSubjectSphereQuestion->apply) {
                continue;
            }

            if(!isset($notificationComponent->meta[QuestionNotification::TYPE][$userSubjectSphere->id])) {
                QuestionNotification::create('', ['user' => $userSubjectSphere->userSubject->user, 'questionItem' => $this, 'userSubjectSphere' => $userSubjectSphere])->send();

                // потрібно для того щоб не слати багато повідомлень що 10 питань добавилось чи видалилось,
                // а замість цього послати одне повідомлення що змінилися питання
                $notificationComponent->meta[QuestionNotification::TYPE][$userSubjectSphere->id] = $userSubjectSphere->id;
            }

            $partQuestion = $this->parents()->andWhere(['type' => PartQuestion::TYPE])->one();
            $rootQuestion = $partQuestion->parents(1)->one();

            $questionComponent->populateProgress($partQuestion, $userSubjectSphere);
            $questionComponent->populateProgress($rootQuestion, $userSubjectSphere);
        }
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function afterDelete()
    {
        $this->notify();

        parent::afterDelete();
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\base\InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes)
    {
        if($insert) {
            $this->notify();
        }

        parent::afterSave($insert, $changedAttributes);
    }
}