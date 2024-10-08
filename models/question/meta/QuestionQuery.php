<?php

namespace common\models\question\meta;

use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\ActiveQuery;

/**
 * Class QuestionQuery
 * @package common\models\question\meta
 *
 * @mixin NestedSetsQueryBehavior
 */
class QuestionQuery extends ActiveQuery
{
    public $type;

    public $tableName;

    /**
     * @param $builder
     * @return $this|\yii\db\Query
     */
    public function prepare($builder)
    {
        if ($this->type !== null) {
            $this->andWhere(["$this->tableName.type" => $this->type]);
        }
        return parent::prepare($builder);
    }

    /**
     * @return array
     */
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::class,
        ];
    }
}