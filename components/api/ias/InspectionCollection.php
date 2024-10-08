<?php

namespace common\components\api\ias;

use common\components\api\ApiCollectionInterface;

/**
 * Class InspectionCollection
 * @package common\components\api\ias
 */
class InspectionCollection extends ApiIas implements ApiCollectionInterface
{
    /**
     * @return string
     */
    public function getAction()
    {
        return 'v1_1/inspection';
    }

    /**
     * @param $code
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function findForChat($code)
    {
        $params = [
            'code' => $code,
            'year' => (new \DateTime())->format('Y'),
            'is_planned' => 1
        ];

        $response = $this->get($params);
        $data = $response->getData();

        $result = '';
        if (array_key_exists('items', $data)) {

            foreach ($data['items'] as $key => $item) {

                $activity_type = $item['data']['activity_type'] . PHP_EOL;
                $name = $item['data']['name'] . PHP_EOL;
                $risk = '' . $item['data']['risk'] . PHP_EOL;
                $date_start = $item['data']['date_start'] . PHP_EOL;
                $regulator = $item['regulator'] . PHP_EOL;
                $status = $item['data']['status'] . PHP_EOL;
                $link = $item['data']['link'] . PHP_EOL . PHP_EOL;

                $result .= <<<TEMPLATE
Предмет перевірки: $activity_type
Назва СГ: $name
Ступінь ризику: $risk
Дата початку: $date_start
Контролюючий орган: $regulator
Статус: $status
Детальніше: $link
TEMPLATE;

            }
        } else {
            $result = "Перевірок не знайдено.";
        }

        return $result;
    }
}