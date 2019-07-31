<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

return [
    'debug' => true, // 必须有，不然 foundation 72 行会报 Notice 未定义索引 debug 错误
    'phone' => 'your-phone-number',
    'password' => 'your-password',

    'log' => [
        'file' => __DIR__.'/runtime.log',
        'level' => 'debug',
        'permission' => 0777,
    ],

    'cache' => new \Doctrine\Common\Cache\FilesystemCache(__DIR__.'../cache/'),
];
