<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk\Providers;

use Hanson\Foundation\Foundation;
use InvalidArgumentException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Yan\PuyingCloudSdk\Core\AccessToken;

class AccessTokenServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['access_token'] = function (Foundation $pimple) {
            $config = $pimple->getConfig();

            if (empty($config['phone']) || empty($config['password'])) {
                throw new InvalidArgumentException('请检查配置文件中是否存在 phone 与 password');
            }

            return new AccessToken($pimple->getConfig('phone'), $pimple->getConfig('password'));
        };
    }
}
