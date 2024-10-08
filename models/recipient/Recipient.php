<?php

namespace common\models\recipient;

use common\models\template\Template;
use yii\base\Model;

/**
 * Class Recipient
 * @package common\models\recipient
 *
 * @property-read string $name
 */
class Recipient extends Model
{
    /**
     * @var Template
     */
    public $_template = null;

    /**
     * @return array
     * @throws \Exception
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $attributes = array_keys($this->getBuildProperties());

        $scenarios[self::SCENARIO_DEFAULT] = array_merge($scenarios[self::SCENARIO_DEFAULT], $attributes);

        $templateScenarios = array_keys(Template::getTemplateList());

        foreach ($templateScenarios as $scenario) {
            $scenarios[$scenario] = $scenarios[self::SCENARIO_DEFAULT];
        }

        return $scenarios;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getRecipientList()
    {
        $list = self::instantiate(false, true);
        $res = [];

        foreach ($list as $recipient) {
            $res[$recipient::TYPE] = $recipient::NAME;
        }

        return $res;
    }

    /**
     * @param $type
     * @param null $get_object_list
     * @return CourtRecipient|DfsRecipient|DrsRecipient|OmbudsmanCouncilRecipient|RegulatorRecipient
     * @throws \Exception
     */
    public static function instantiate($type, $get_object_list = null)
    {
        $list[1] = new DrsRecipient();
        $list[2] = new CourtRecipient();
        $list[3] = new RegulatorRecipient();
        $list[4] = new DfsRecipient();
        $list[5] = new OmbudsmanCouncilRecipient();

        if($get_object_list) {
            return $list;
        }

        switch ($type) {
            case DrsRecipient::TYPE:
                return $list[1];
            case CourtRecipient::TYPE:
                return $list[2];
            case RegulatorRecipient::TYPE:
                return $list[3];
            case DfsRecipient::TYPE:
                return $list[4];
            case OmbudsmanCouncilRecipient::TYPE:
                return $list[5];

            default:
                throw new \Exception('incorrect type');
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }
}