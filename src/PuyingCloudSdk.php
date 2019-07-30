<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk;

use Hanson\Foundation\Foundation;
use Yan\PuyingCloudSdk\Core\AccessToken;
use Yan\PuyingCloudSdk\Kernel\PrinterManage;
use Yan\PuyingCloudSdk\Providers\AccessTokenServiceProvider;
use Yan\PuyingCloudSdk\Providers\PrinterServiceProvider;

/**
 * @property AccessToken   $access_token
 * @property PrinterManage $printer
 */
class PuyingCloudSdk extends Foundation
{
    protected $providers = [
        AccessTokenServiceProvider::class,
        PrinterServiceProvider::class,
    ];
}
