<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk\Core;

use Hanson\Foundation\AbstractAccessToken;
use Yan\PuyingCloudSdk\Exceptions\ApiException;
use Yan\PuyingCloudSdk\PuyingCloudSdk;

class AccessToken extends AbstractAccessToken
{
    protected $app;

    protected $prefix = 'printer.token.';

    protected $action = 'login';

    protected $tokenJsonKey = 'access_token';

    protected $expiresJsonKey = 'expire_in';

    protected $result;

    public function __construct($phone, $password, PuyingCloudSdk $app)
    {
        $this->appId = $phone;
        $this->secret = $password;
        $this->app = $app;
    }

    /**
     * Throw exception if token is invalid.
     *
     * @param $result
     *
     * @return mixed
     */
    public function checkTokenResponse($result)
    {
        if (empty($result[$this->tokenJsonKey])) {
            throw new ApiException('获取 token 失败');
        }

        $this->setResult($result);

        return $result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getResult($key = '', $default = null)
    {
        // 还未获取过 token
        if (is_null($this->result)) {
            $this->getToken(true);
        }

        if ($key) {
            return $this->result[$key] ?? $default;
        }

        return $this->result;
    }

    public function getToken($forceRefresh = false)
    {
        $cached = $this->getCache()->fetch($this->getCacheKey()) ?: $this->token;

        if ($forceRefresh || empty($cached)) {

            $result = $this->getTokenFromServer();

            $this->checkTokenResponse($result);

            $this->setToken(
                $token = $result[$this->tokenJsonKey],
                5600
            );

            return $token;
        }

        return $cached;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getTokenFromServer()
    {
        $api = new Api($this->app);

        $response = $api->request($this->getAction(), [
            'phone' => $this->getAppId(),
            'password' => $this->getSecret(),
        ]);

        return $api->parseJSON($response);
    }

    public function setAccessTokenWithUser($phone, $password)
    {
        return new static($phone, $password, $this->app);
    }
}
