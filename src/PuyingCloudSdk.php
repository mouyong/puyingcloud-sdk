<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk;

use Monolog\Logger;
use Hanson\Foundation\Log;
use Hanson\Foundation\Foundation;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\HandlerInterface;
use Yan\PuyingCloudSdk\Core\AccessToken;
use Yan\PuyingCloudSdk\Kernel\PrinterManage;
use Yan\PuyingCloudSdk\Providers\AccessTokenServiceProvider;
use Yan\PuyingCloudSdk\Providers\PrinterServiceProvider;
use Yan\PuyingCloudSdk\Providers\LoggerServiceProvider;

/**
 * @property AccessToken   $access_token
 * @property PrinterManage $printer
 */
class PuyingCloudSdk extends Foundation
{
    protected $providers = [
        LoggerServiceProvider::class,
        AccessTokenServiceProvider::class,
        PrinterServiceProvider::class,
    ];

    public function __construct($config)
    {
        parent::__construct($config);

        // 主动重新初始化日志
        $this->initializeLogger();
    }

    /**
     * foundation ^2.0 无此函数.
     *
     * @param null $key
     *
     * @return mixed
     */
    public function getConfig($key = null)
    {
        return $key ? $this->config[$key] : $this->config;
    }

    protected function initializeLogger()
    {
        if ($this->foundationVersion() >= 3) {
            return;
        }

        // 当 foundation 小于 3 的时候，无法正常读取 config 的配置，需要主动重新获取
        // 以下进行 logger 的重新初始化

        $logger = new Logger($this['config']['log']['name'] ?? 'puyingcloud');

        if (!($this['config']['debug'] ?? false) || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif (($this['config']['log']['handler'] ?? null) instanceof HandlerInterface) {
            $logger->pushHandler($this['config']['log']['handler']);
        } elseif ($logFile = ($this['config']['log']['file'] ?? null)) {
            $logger->pushHandler(new StreamHandler(
                    $logFile,
                    $this['config']['log']['level'] ?? Logger::WARNING,
                    true,
                    $this['config']['log']['permission'] ?? null
            ));
        }

        Log::setLogger($logger);
    }

    public function foundationVersion()
    {
        if (method_exists(parent::class, 'getConfig')) {
            return 3;
        }

        return 2;
    }
}
