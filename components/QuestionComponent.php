<?php


use common\helpers\ArrayHelper;
use common\models\question\entities\PartQuestion;
use common\models\question\entities\QuestionItem;
use common\models\question\Question;
use common\models\question\UserSubjectSphereQuestion;
use backend\models\SubjectActivity;
use common\models\UserSubject;
use common\models\UserSubjectSphere;
use frontend\models\search\QuestionSearch;
use yii\base\Component;

/**
 * Class QuestionComponent
 * @package common\components
 */
class QuestionComponent extends Component
{
    /**
     * @param Question $partQuestion
     * @param UserSubject $userSubject
     * @return null|static
     */
    public function getUserSubjectSphereQuestion(Question $partQuestion, UserSubject $userSubject)
    {
        /* @var $userSubjectSphere UserSubjectSphere */
        $userSubjectSphere = UserSubjectSphere::find()
            ->where(['user_subject_id' => $userSubject->id, 'sphere_id' => $partQuestion->sphere_id])
            ->one();

        $userSubjectSphereQuestion = UserSubjectSphereQuestion::findOne(['user_subject_sphere_id' => $userSubjectSphere->id, 'question_id' => $partQuestion->id]);

        if(!$userSubjectSphereQuestion) {
            $userSubjectSphereQuestion = new UserSubjectSphereQuestion();
            $userSubjectSphereQuestion->user_subject_sphere_id = $userSubjectSphere->id;
            $userSubjectSphereQuestion->question_id = $partQuestion->id;

            $userSubjectSphereQuestion->save(false);

            $userSubjectSphereQuestion->refresh();
        }

        return $userSubjectSphereQuestion;
    }

    /**
     * @param SubjectActivity $subjectActivity
     * @return array
     */
    public static function getPartQuestionList(SubjectActivity $subjectActivity)
    {
        $query = PartQuestion::find();

        if(!$subjectActivity->isNewRecord) {
            if($subjectActivity->sphereMultiple) {
                $query->where(['sphere_id' => $subjectActivity->sphereMultiple]);
            }
        }

        return ArrayHelper::map($query->all(), 'id', 'name');
    }

    /**
     * @param $part_question_ids
     * @throws \yii\db\Exception
     */
    public function recalculateProgress($part_question_ids)
    {
        $rootQuestions = [];

        $partQuestions = PartQuestion::findAll(['id' => $part_question_ids]);

        /* @var $partQuestion PartQuestion */
        foreach ($partQuestions as $partQuestion) {
            $rootQuestions[] = $partQuestion->parents(1)->one();
        }

        $rootQuestions = array_unique($rootQuestions);

        foreach (UserSubjectSphere::find()->each(1000) as $userSubjectSphere) {
            foreach ($rootQuestions as $rootQuestion) {
                $this->populateProgress($rootQuestion, $userSubjectSphere);
            }
        }
    }

    /**
     * @param Question $question
     * @param UserSubjectSphere $userSubjectSphere
     * @throws \yii\db\Exception
     */
    public function populateProgress(Question $question, UserSubjectSphere $userSubjectSphere)
    {
        $type = QuestionItem::TYPE;

        $children = $question->children()->andWhere(['type' => QuestionItem::TYPE])->all();
        $countItems = count($children);

        $queryCountAnsweredItems = UserSubjectSphereQuestion::find()
            ->where([
                'question_id' => ArrayHelper::getColumn($children, 'id'),
                'user_subject_sphere_id' => $userSubjectSphere->id
            ]);

        if($question->isRoot()) {

            $dataProvider = (new QuestionSearch())->searchPartQuestions($question, [], $userSubjectSphere);

            $partQuestions = $dataProvider->getModels();

            $deletePartQuestionIds = array_diff(
                ArrayHelper::getColumn($question->children()->where(['type' => PartQuestion::TYPE])->all(), 'id'),
                ArrayHelper::getColumn($partQuestions, 'id')
            );

            $deletePartQuestions = PartQuestion::findAll(['id' => $deletePartQuestionIds]);
            $deleteIds = [];

            /* @var $deletePartQuestion PartQuestion */
            foreach ($deletePartQuestions as $deletePartQuestion) {
                $deleteIds[] = $deletePartQuestion->id;
                $deleteIds = array_merge($deleteIds, ArrayHelper::getColumn($deletePartQuestion->children()->all(), 'id'));
            }

            if($deleteIds) {
                UserSubjectSphereQuestion::deleteAll(['user_subject_sphere_id' => $userSubjectSphere->id, 'question_id' => $deleteIds]);
            }

            $except = UserSubjectSphereQuestion::find()
                ->where([
                    'question_id' => ArrayHelper::getColumn($children, 'id'),
                    'user_subject_sphere_id' => $userSubjectSphere->id,
                    'apply' => false
                ])
                ->all();

            $except = ArrayHelper::getColumn($except, 'question_id');

            $countItems = 0;
            /* @var $partQuestion PartQuestion */
            foreach ($partQuestions as $partQuestion) {
                $countItems += $partQuestion
                    ->children()
                    ->andWhere(['type' => $type])
                    ->andWhere(['not', ['id' => $except]])
                    ->count();
            }

            $countAnsweredItems = $queryCountAnsweredItems
                ->andWhere(['answer' => [Question::ANSWER_YES, Question::ANSWER_NR]])
                ->andWhere(['apply' => true])
                ->count();

            if($countItems) {
                $max = 90;
                $oneValue = $max/$countItems;
                $event_risk = $max - ($countAnsweredItems * $oneValue);

            } else {
                $event_risk = 10;
            }
            \Yii::$app->db->createCommand()
                ->update(UserSubjectSphereQuestion::tableName(), ['event_risk' => $event_risk], ['user_subject_sphere_id' => $userSubjectSphere->id, 'question_id' => $question->id])
                ->execute();

        } elseif ($question instanceof PartQuestion) {

            $countAnsweredItems = $queryCountAnsweredItems
                ->andWhere(['not', ['answer' => null]])
                ->count();

            $oneValue = 100/$countItems;
            $fill_status = $oneValue * $countAnsweredItems;

            \Yii::$app->db->createCommand()
                ->update(UserSubjectSphereQuestion::tableName(), ['fill_status' => $fill_status], ['user_subject_sphere_id' => $userSubjectSphere->id, 'question_id' => $question->id])
                ->execute();
        }
    }
}