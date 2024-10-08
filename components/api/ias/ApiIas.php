<?php

namespace common\components\api\ias;

use common\components\api\ApiInterface;
use yii\base\BaseObject;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\Response;

/**
 * Class ApiComponent
 * @package common\components
 *
 * @property InspectionCollection $inspection
 * @property RegulatorCollection $regulator
 */
class ApiIas extends BaseObject implements ApiInterface
{
    /**
     * @var array
     */
    public $collections = [];

    /**
     * @var array
     */
    public $params = [];

    /**
     * @var null
     */
    protected $log = null;

    /**
     * @var null|Client
     */
    protected $_client = null;

    const LOG_CATEGORY = 'api_log_category';

    /**
     * @return mixed
     */
    public function getKey()
    {
        return \Yii::$app->params['iasApiKey'];
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return \Yii::$app->params['iasApiDomain'];
    }

    /**
     * @return null|Client
     */
    public function getClient()
    {
        if(!$this->_client) {
            $this->_client = new Client([
                'baseUrl' => $this->getDomain()
            ]);
        }

        return $this->_client;
    }

    /**
     * @return Request
     * @throws \yii\base\InvalidConfigException
     */
    public function createRequest()
    {
        return $this->getClient()
            ->createRequest()
            ->addData(['apiKey' => $this->getKey()]);
    }

    /**
     * @param array $params
     * @return Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function get(array $params = [])
    {
        $response = $this->createRequest()
            ->setUrl($this->getAction())
            ->addData($params)
            ->send();

        return $response;
    }

    /**
     * @param $params
     * @return string
     */
    public function getUrl($params)
    {
        $params['apiKey'] = $this->getKey();

        $url = $this->getDomain().$this->getAction().'?'.http_build_query($params);

        return $url;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UnknownPropertyException
     */
    public function __get($name)
    {
        if(array_key_exists($name, $this->collections)) {
            return $this->getCollection($name);
        }

        return parent::__get($name);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function getCollection($id)
    {
        if(!isset($this->collections[$id])){
            throw new \InvalidArgumentException("Unknown collection '{$id}'.");
        }

        if (!is_object($this->collections[$id])) {
            $this->collections[$id] = $this->createCollection($id, $this->collections[$id]);
        }

        return $this->collections[$id];
    }

    /**
     * @param $id
     * @param $config
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    protected function createCollection($id, $config)
    {
        return \Yii::createObject($config);
    }

    /**
     * @param array $messages
     */
    protected function log(array $messages)
    {
        if($this->log) {
            foreach ($messages as $message) {
                \Yii::debug($message, self::LOG_CATEGORY);
            }
        }
    }
}