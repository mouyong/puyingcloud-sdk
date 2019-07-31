<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Yan\PuyingCloudSdk\Core\AccessToken;
use Yan\PuyingCloudSdk\Exceptions\InvalidConfigException;
use Yan\PuyingCloudSdk\PuyingCloudSdk;

class AccessTokenServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['access_token'] = function (PuyingCloudSdk $pimple) {
            $config = $pimple->getConfig();

            if (empty($config['phone']) || empty($config['password'])) {
                throw new InvalidConfigException('请检查配置文件中是否存在 phone 与 password');
            }

            return new AccessToken($pimple->getConfig('phone'), $pimple->getConfig('password'), $pimple);
        };
    }
}
