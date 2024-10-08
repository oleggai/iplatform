<?php


use common\models\DownloadDocStatistic;
use common\models\Subject;
use common\models\SubjectActivity;
use common\models\user\entities\EmptyUser;
use common\models\UserSubject;
use thamtech\uuid\helpers\UuidHelper;
use yii\base\Component;
use yii\web\Cookie;

/**
 * Class UserComponent
 * @package common\components
 */
class UserComponent extends Component
{
    public $cookieName = 'empty_user_hash';

    /**
     * Час існування empty_user_hash куки для порожнього юзера
     * @var int секунди
     */
    const EMPTY_USER_EXPIRE = 3600*24*7; // один тиждень

    protected $_currentUser = null;

    /**
     * @return null|\common\models\user\entities\User
     */
    public function getCurrentUser()
    {
        if($this->_currentUser !== null) {
            return $this->_currentUser;
        }

        if(!\Yii::$app->user->isGuest) {
            $this->_currentUser = \common\models\user\entities\User::find()
                ->where(['id' => \Yii::$app->user->id])
                ->one();
            $this->_currentUser = $this->_currentUser ? : '';
        }

        return $this->_currentUser;
    }

    /**
     * Removes cookie and empty user
     */
    public function removeEmptyUser()
    {
        $cookieValue = \Yii::$app->request->cookies->get($this->cookieName);

        if($cookieValue) {
            // remove cookie and empty user
            \Yii::$app->response->cookies->remove($this->cookieName);
            EmptyUser::deleteAll(['empty_user_hash' => $cookieValue]);
        }
    }

    /**
     * @param EmptyUser $emptyUser
     * @return bool
     */
    public function isEmptyUserExpired(EmptyUser $emptyUser)
    {
        return $emptyUser->expire < time();
    }

    /**
     * @param $empty_user_hash
     * @param $expire
     * @return EmptyUser
     * @throws \Exception
     */
    public function createEmptyUser($empty_user_hash, $expire)
    {
        $emptyUser = new EmptyUser();
        $emptyUser->empty_user_hash = $empty_user_hash ? : UuidHelper::uuid();
        $emptyUser->expire = $expire;

        $emptyUser->save(false);

        $authManager = \Yii::$app->authManager;

        $roleEmptyUser = $authManager->getRole(\common\models\user\entities\User::ROLE_EMPTY_USER);
        $authManager->assign($roleEmptyUser, $emptyUser->id);

        return $emptyUser;
    }

    /**
     * @return EmptyUser|null
     * @throws \Exception
     */
    public function getEmptyUser()
    {
        $cookieName = $this->cookieName;
        $empty_user_hash = \Yii::$app->request->cookies->getValue($cookieName);

        if(!$empty_user_hash) {
            return null;
        } else {
            /* @var $emptyUser EmptyUser */
            $emptyUser = EmptyUser::find()
                ->where(['empty_user_hash' => $empty_user_hash])
                ->one();

            return $emptyUser;
        }
    }

    /**
     * @return UserSubject|null|static
     */
    public function processEmptyUser()
    {
        $cookieName = $this->cookieName;

        $expire = time() + self::EMPTY_USER_EXPIRE;

        $setCookie = function(EmptyUser $emptyUser) use($cookieName, $expire) {
            \Yii::$app->response->cookies->add(new Cookie([
                'name' => $cookieName,
                'value' => $emptyUser->empty_user_hash,
                'expire' => $expire
            ]));
        };

        $empty_user_hash = \Yii::$app->request->cookies->getValue($cookieName);

        if($empty_user_hash) {
            /* @var $emptyUser EmptyUser */
            $emptyUser = EmptyUser::find()
                ->where(['empty_user_hash' => $empty_user_hash])
                ->one();

            $emptyUser = $emptyUser ? : $this->createEmptyUser($empty_user_hash, $expire);

        } else {
            // createEmptyUser
            $emptyUser = $this->createEmptyUser($empty_user_hash, $expire);
            // setCookie
            $setCookie($emptyUser);
        }

        /* update expire field (продливаем время жизни временного юзера) */
        $emptyUser->expire = $expire;
        $emptyUser->updateAttributes(['expire']);

        $emptySubject = Subject::find()->where(['type' => Subject::TYPE_EMPTY_SUBJECT])->one();

        $userSubject = UserSubject::findOne(['user_id' => $emptyUser->id, 'subject_id' => $emptySubject->id]);

        if(!$userSubject) {
            $userSubject = new UserSubject();
            $userSubject->user_id = $emptyUser->id;
            $userSubject->subject_id = $emptySubject->id;

            $userSubject->save(false);
        }

        return $userSubject;
    }

    /**
     * @param DownloadDocStatistic $model
     */
    public function setDownloadDocStatisticCookie(DownloadDocStatistic $model)
    {
        $cookies = \Yii::$app->response->cookies;
        $expire = time() + 3600*24*7;

        $cookies->add(new Cookie([
            'name' => 'email',
            'value' => $model->email,
            'expire' => $expire
        ]));

        $cookies->add(new Cookie([
            'name' => 'phone',
            'value' => $model->phone,
            'expire' => $expire
        ]));

        $cookies->add(new Cookie([
            'name' => 'company_name',
            'value' => $model->company_name,
            'expire' => $expire
        ]));

        $cookies->add(new Cookie([
            'name' => 'position',
            'value' => $model->position,
            'expire' => $expire
        ]));
    }
}