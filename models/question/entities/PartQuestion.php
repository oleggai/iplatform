<?php

namespace common\models\question\entities;

use common\models\question\Question;

/**
 * Class PartQuestion
 * @package common\models\question\entities
 *
 * @property string $question_num
 */
class PartQuestion extends Question
{
    const TYPE = 'part_question';
}