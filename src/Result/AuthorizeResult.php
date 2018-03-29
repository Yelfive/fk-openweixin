<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 18-3-29
 */

class AuthorizeResult
{
    public $access_token;
    public $expires_in;
    public $refresh_token;
    public $openid;
    public $scope;
    public $errcode = 0;
    public $errmsg = null;
}