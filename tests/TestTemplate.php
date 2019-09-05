<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyinCloudSdk\Test;

use Yan\PuyingCloudSdk\Contracts\Template;
use Yan\PuyingCloudSdk\Adapters\Adapter;

class TestTemplate extends Adapter implements Template
{
    public function render()
    {
        $title = $this->title($this->content);

        $this->result = $title;
    }
}
