<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk\Core;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Hanson\Foundation\AbstractAPI;
use Yan\PuyingCloudSdk\Exceptions\AccessTokenExpireException;
use Yan\PuyingCloudSdk\Exceptions\ApiException;
use Yan\PuyingCloudSdk\Exceptions\InvalidCustomHeaderException;
use Yan\PuyingCloudSdk\Exceptions\InvalidResponseException;

class Api extends AbstractAPI
{
    const API_URL = 'http://puyingcloud.cn/v2/printer/open/index.html';

    /** @var AccessToken */
    protected $accessToken;

    protected $action;

    public function __construct(AccessToken $accessToken)
    {
        $this->setAccessToken($accessToken);
    }

    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function middlewares()
    {
        if (empty($this->action)) {
            $this->setAction($this->accessToken->getAction());
        }

        $this->http->addMiddleware($this->headerMiddleware([
            'Content-Type' => 'application/json',
            'Access-Token' => $this->getRequestToken(),
            'Action' => $this->getAction(),
        ]));
    }

    public function getRequestToken()
    {
        if ('login' == $this->getAction()) {
            return '';
        }

        return $this->accessToken->getToken();
    }

    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function getAction()
    {
        if (empty($this->action)) {
            throw new InvalidCustomHeaderException('unknown action');
        }

        return $this->action;
    }

    public function json($data = [])
    {
        try {
            $response = $this->getHttp()->json(self::API_URL, $data);
        } catch (ClientException $e) { // 获取接口返回实际响应
            $response = $e->getResponse();
        } catch (ServerException $e) {
            $response = $e->getResponse();
        }

        try {
            $response = $this->parseJSON($response);
        } catch (AccessTokenExpireException $e) { // token 过期
            $action = $this->getAction();
            $this->getAccessToken()->getToken(true);

            $response = $this->setAction($action)->getHttp()->json(self::API_URL, $data);
        }

        return $response;
    }

    public function parseJSON($response)
    {
        $result = json_decode(strval($response->getBody()), true);

        $this->checkAndThrow($result);

        return $result['data'] ?? [];
    }

    public function checkAndThrow($result)
    {
        if (!$result) {
            throw new InvalidResponseException('invalid response.');
        }

        if (isset($result['status']) && 200 !== $result['status']) {
            if (AccessTokenExpireException::EXPIRE_CODE == $result['status']) { // token 过期，需要重新登录
                throw new AccessTokenExpireException();
            }

            throw new ApiException($result['msg'], $result['status']);
        }
    }
}
