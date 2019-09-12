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

class TitleTemplate extends Adapter implements Template
{
    public function __construct(string $title)
    {
        $this->result = $title;
    }

    public function render()
    {
        $this->textSmall($this->result);

        return strval($this->result);
    }

    public function textSmall(string $text)
    {
        $this->push($text);
    }

    public function textMedium(string $text)
    {
        // TODO: Implement textMedium() method.
    }

    public function textLarge(string $text)
    {
        // TODO: Implement textLarge() method.
    }

    public function division($division = null)
    {
        // TODO: Implement division() method.
    }

    public function around($text, $times = 1, $around = '·', $size = 'small')
    {
        // TODO: Implement around() method.
    }
}
