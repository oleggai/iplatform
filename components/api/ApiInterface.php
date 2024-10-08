<?php

namespace common\components\api;

/**
 * Interface ApiInterface
 * @package common\components\api
 */
interface ApiInterface
{
    public function getKey();
    public function getDomain();
    public function getClient();
    public function createRequest();
    public function get(array $params = []);
}