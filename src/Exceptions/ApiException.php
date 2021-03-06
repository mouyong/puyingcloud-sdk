<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk\Exceptions;

use RuntimeException;
use Throwable;

class ApiException extends RuntimeException
{
    const REQUEST_BODY_ERROR = 40013003;

    const PRINTER_ALREADY_SUBSCRIBED = 40013007;

    const PRINTER_UBSCRIBED = 403133001;

    const PRINTER_UBSCRIBED_WHEN_REMOVE = 40013004;

    const SN_UNEXISTS = 40013002;

    const EXCEPTION_MESSAGE_MAP = [
        self::REQUEST_BODY_ERROR => '请检查输入信息是否正确',
        self::PRINTER_ALREADY_SUBSCRIBED => '小票机已绑定，请先解绑',
        self::PRINTER_UBSCRIBED => '小票机未绑定',
        self::PRINTER_UBSCRIBED_WHEN_REMOVE => '小票机未绑定，打印机未绑定到当前账号下',
        self::SN_UNEXISTS => '小票机不存在',
    ];

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $message = $this->resolveMessage($code, $message);

        parent::__construct($message, $code, $previous);
    }

    public function resolveMessage($code, $message = '')
    {
        if (array_key_exists($code, self::EXCEPTION_MESSAGE_MAP)) {
            return self::EXCEPTION_MESSAGE_MAP[$code];
        }

        return $message;
    }
}
