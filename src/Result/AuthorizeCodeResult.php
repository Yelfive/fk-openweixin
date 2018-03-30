<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 18-3-29
 */

namespace fk\openweixin\Result;

class AuthorizeCodeResult extends AbstractResult
{
    public $code;
    public $state;
}
