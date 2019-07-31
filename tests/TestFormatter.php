<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyinCloudSdk\Test;

use Yan\PuyingCloudSdk\Contracts\Formatter;
use Yan\PuyingCloudSdk\Kernel\ContentFormatter;

class TestFormatter extends ContentFormatter implements Formatter
{
    public function format()
    {
        $title = $this->title($this->content);

        $this->result = $title;
    }
}
