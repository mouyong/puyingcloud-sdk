<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyinCloudSdk\Test;

use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    public function testTitleFormat()
    {
        $title = '测试标题';

        $formatter = new TestTemplate($title);

        $this->assertEquals("<CB>$title<BR></CB><BR>", strval($formatter));
    }
}
