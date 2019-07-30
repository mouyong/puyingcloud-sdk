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
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Yan\PuyingCloudSdk\Kernel\PrinterManage;

class PrinterServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['printer'] = function (Foundation $pimple) {
            return new PrinterManage($pimple['access_token']);
        };
    }
}
