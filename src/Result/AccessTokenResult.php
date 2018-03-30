<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 18-3-30
 */

namespace fk\openweixin\Result;

class AccessTokenResult extends AbstractResult
{
    public $access_token;
    public $expires_in = 7200;
    public $refresh_token;
    public $openid;
    public $scope;
    public $errcode = 0;
    public $errmsg = null;
    public $state;
}