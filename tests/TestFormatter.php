<?php

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
