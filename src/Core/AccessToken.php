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

class AccessToken extends AbstractAccessToken
{
    protected $prefix = 'printer.token.';

    protected $action = 'login';

    protected $tokenJsonKey = 'access_token';

    protected $expiresJsonKey = 'expire_in';

    public function __construct($phone, $password)
    {
        $this->appId = $phone;
        $this->secret = $password;
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
        return $result;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getTokenFromServer()
    {
        $response = (new Api($this))->json([
            'phone' => $this->getAppId(),
            'password' => $this->getSecret(),
        ]);

        return $response;
    }
}
