<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk\Core;

use GuzzleHttp\Middleware;
use Hanson\Foundation\AbstractAPI;
use Yan\PuyingCloudSdk\PuyingCloudSdk;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;
use Yan\PuyingCloudSdk\Exceptions\ApiException;
use Yan\PuyingCloudSdk\Exceptions\AccessTokenExpireException;
use Yan\PuyingCloudSdk\Exceptions\InvalidResponseException;

class Api extends AbstractAPI
{
    const API_URL = 'http://puyingcloud.cn/v2/printer/open/index.html';

    const MAX_RETRIES = 1;

    /** @var PuyingCloudSdk */
    protected $app;

    /** @var AccessToken */
    protected $accessToken;

    protected $action;

    public function __construct(PuyingCloudSdk $app)
    {
        $this->app = $app;

        $this->setAccessToken($app['access_token']);
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

    public function setUser($phone, $password)
    {
        $this->setAccessToken(
            $this->app->access_token->setAccessTokenWithUser($phone, $password)
        );

        return $this;
    }

    public function middlewares()
    {
        if (empty($this->action)) {
            $this->action($this->accessToken->getAction());
        }

        $this->http->addMiddleware($this->addRequestHeader('Content-Type', 'application/json'));
        $this->http->addMiddleware($this->addRequestHeader('Action', $this->action()));
        $this->http->addMiddleware($this->accessTokenMiddleware());
        $this->http->addMiddleware($this->retryMiddleware());
    }

    public function addRequestHeader($key, $value)
    {
        return Middleware::mapRequest(function (RequestInterface $request) use ($key, $value) {
            return $request->withHeader($key, $value);
        });
    }

    public function accessTokenMiddleware()
    {
        return Middleware::mapRequest(function (RequestInterface $request) {
            if ($this->action() === $this->accessToken->getAction()) {
                $request = $request->withoutHeader('Access-Token');
            } else {
                $request = $request->withHeader('Access-Token', $this->accessToken->getToken());
            }

            return $request;
        });
    }

    public function retryMiddleware()
    {
        return Middleware::retry(function (
            $retries,
            RequestInterface $request,
            ResponseInterface $response = null,
            $value = null,
            RequestException $exception = null
        ) {
            $this->app['logger']->debug('action: '.$this->action(), $this->data());

            // 超过最大重试次数，不再重试
            if ($retries > static::MAX_RETRIES) {
                return false;
            }

            // 请求失败，继续重试
            if ($exception instanceof ConnectException) {
                return true;
            }

            if ($response) {

                // 如果请求有响应，但是状态码大于等于500，继续重试(这里根据自己的业务而定)
                if ($response->getStatusCode() >= 500) {
                    return true;
                }
            }

            return false;
        }, function ($numberOfRetries) {
            return 1000 * $numberOfRetries;
        });
    }

    public function action($action = '')
    {
        if (!empty($action)) {
            $this->action = $action;
        }

        return $this->action;
    }

    public function request($action, $data = [])
    {
        try {
            $this->action($action);
            $this->data($data);

            $response = $this->getHttp()->json(self::API_URL, $data);
        } catch (BadResponseException $e) { // 获取接口返回实际响应
            $response = $e->getResponse();
        }

        try {
            $response = $this->parseJSON($response);
        } catch (AccessTokenExpireException $e) { // token 过期
            $this->getAccessToken()->getToken(true);
            $response = $this->getHttp()->json(self::API_URL, $this->data());
            $response = $this->parseJSON($response);
        }

        return $response;
    }

    public function data($data = [])
    {
        if (!empty($data)) {
            $this->data = $data;
        }

        return $this->data;
    }

    public function parseJSON($response)
    {
        $result = $response;
        if (is_object($response)) {
            $result = json_decode(strval($response->getBody()), true);
        }

        $this->checkAndThrow($result);

        if ($this->getAccessToken()->getAction() === $this->action()) {
            return $result;
        }

        return $result['data'] ?? [];
    }

    public function checkAndThrow($result)
    {
        if (!$result) {
            throw new InvalidResponseException('invalid response.');
        }

        if (isset($result['status']) && 200 !== $result['status']) {
            if (AccessTokenExpireException::EXPIRE_CODE == $result['status']) { // token 过期，需要重新登录
                throw new AccessTokenExpireException($result['msg'], $result['status']);
            }

            $msg = $result['msg'];

            if (strstr($msg, 'content is ')) {
                dd($msg);
                $msg = \json_decode(str_replace('content is ', '', $exception->getMessage()), true) ?? $exception->getMessage();
            }

            throw new ApiException($msg, $result['status']);
        }
    }
}
