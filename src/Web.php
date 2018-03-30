<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 18-3-28
 */

namespace fk\openweixin;

use fk\openweixin\Result\AccessTokenResult;
use fk\openweixin\Result\AuthorizeResult;

class Web
{

    public const ACCESS_TOKEN_TTL = 7200;

    public $appId;

    public $appSecret;

    protected $debug = true;

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(array $config)
    {
        foreach ($config as $k => $v) {
            if (property_exists($this, $k)) $this->$k = $v;
        }
        $this->cache = new Cache($config['runtime_directory'] ?? null);
    }

    protected function getAccessToken()
    {
        $key = 'access_token';
        if ($token = $this->cache->retrieve($key)) return $token;

        $api = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
        $content = file_get_contents($api);
        $data = json_decode($content, true);
        $token = $data['access_token'];
        $ttl = $data['expires_in'] ?? self::ACCESS_TOKEN_TTL;
        $this->cache->store('access_token', $token, $ttl);
        return $token;
    }


    public function getOpenid()
    {
        $result = $this->authorizeWithSnsapi_base();
        return $result->openid;
    }

    /**
     * @param string $scope
     * @param string $returnURI
     * @param string $state
     * @return AccessTokenResult|null
     * @throws \Exception
     */
    protected function getAuthorizeInfo($scope, $returnURI, $state = '')
    {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            return $this->getAuthorizeAccessTokenByCode($code);
        }

        $this->getAuthorizeCode($scope, $returnURI, $state);
    }

    protected function getAuthorizeAccessTokenByCode($code)
    {
        $api = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appId}&secret={$this->appSecret}&code={$code}&grant_type=authorization_code";
        $result = file_get_contents($api);
        return new AccessTokenResult($result);
    }

    /**
     * Redirect to Wechat authorize page, and finally returns to page:
     * ```
     * $redirectURI/?code=CODE&state=STATE
     * ```
     * @param string $scope
     * @param string $returnURI
     * @param string $state
     * @return
     * @throws \Exception
     */
    protected function getAuthorizeCode($scope, $returnURI, $state)
    {
        if ($_GET['code']) {
            return $_GET['code'];
        }
        if ($returnURI === null) {
            $schema = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $returnURI = "$schema://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        }
        $returnURI = urlencode($returnURI);
        if ($state) {
            if (preg_match('#[^0-9a-zA-Z]#', $state)) throw new \Exception('Parameter `state` should consist only of "0-9A-Za-z".');
            if (strlen($state) > 128) throw new \Exception('Parameter `state` should be no larger than 128 bytes.');
        }
        $api = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appId}&redirect_uri={$returnURI}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
        header("Location:$api");
        die(<<<HTML
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Document</title>
</head>
<body>
  <script>location.href="{$api}"</script>
</body>
</html>
HTML
        );
    }

    /**
     * @param string $state
     * @param null|string $returnURI
     * @return AccessTokenResult
     * @throws \Exception
     */
    public function authorizeWithSnsapi_base($state = '', $returnURI = null)
    {
        $result = $this->getAuthorizeInfo('snsapi_base', $returnURI, $state);
        return $result;
    }
}