<?php

namespace common\models\interfaces;

/**
 * Interface DisplayInterface
 */
interface DisplayInterface
{
    public static function getPublicationType();
    public static function getTitle();
    public static function getDescription();
    public static function getImage();
}