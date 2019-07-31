<?php

namespace Yan\PuyinCloudSdk\Test;

use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    public function testTitleFormat()
    {
        $title = "测试标题";

        $formatter = new TestFormatter($title);

        $this->assertEquals("<CB>$title<BR></CB><BR>", strval($formatter));
    }
}