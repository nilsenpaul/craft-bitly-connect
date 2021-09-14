<?php

namespace nilsenpaul\bitlyconnect\models;

use craft\base\Model;

class Settings extends Model
{
    public $accessToken;
    public $domain;
    public $group;

    public function rules()
    {
        return [
        ];
    }
}
