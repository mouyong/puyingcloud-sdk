<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

return [
    'debug' => env('PUYINGCLOUD_DEBUG', false), // 必须有，不然 foundation 72 行会报 Notice 未定义索引 debug 错误
    'phone' => env('PUYINGCLOUD_PHONE', 'your-phone-number'),
    'password' => env('PUYINGCLOUD_PASSWORD', 'your-password'),

    'log' => [
        'file' => storage_path('logs/puyingcloud.log'),
        'level' => 'debug',
        'permission' => 0777,
    ],

    'cache' => new \Doctrine\Common\Cache\FilesystemCache(storage_path('app/framework/cache')),
];
