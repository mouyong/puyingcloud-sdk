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

        return $result;
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

    public function getAccessTokenByUser($phone, $password)
    {
        return new static($phone, $password, $this->app);
    }
}
