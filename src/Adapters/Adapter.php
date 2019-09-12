<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk\Adapters;

use Yan\PuyingCloudSdk\Exceptions\AdapterException;
use Yan\PuyingCloudSdk\Exceptions\RenderException;

abstract class Adapter
{
    protected $content = [];

    protected $glue = "\n";

    protected $escapes = [];

    protected $byteLength = 48;

    public function clear()
    {
        $this->content = [];
    }

    public function getByteLength($size = 'small'): int
    {
        // size 不为 small 时，内容被放大一倍，能放下的数据减少一半，只能放 24 字节
        if ('small' != $size) {
            return $this->byteLength / 2;
        }

        return $this->byteLength;
    }

    /**
     * 小号字体.
     *
     * @param string $text
     *
     * @return mixed
     */
    abstract public function textSmall(string $text);

    /**
     * 中号字体.
     *
     * @param string $text
     *
     * @return mixed
     */
    abstract public function textMedium(string $text);

    /**
     * 大号字体.
     *
     * @param string $text
     *
     * @return mixed
     */
    abstract public function textLarge(string $text);

    /**
     * 表格
     *
     * @param array  $items
     * @param string $size
     *
     * @return mixed
     */
    public function table($items, $size = 'small')
    {
        $method = ['small' => 'textSmall', 'medium' => 'textMedium', 'large' => 'textLarge'][$size];

        foreach ($items as $key => $value) {
            $this->{$method}($this->calcTableSpace($value, $size));
        }

        return $this;
    }

    /**
     * 表格空白补全.
     *
     * @param array  $row
     * @param string $size
     *
     * @return mixed
     */
    public function calcTableSpace(array $row, $size)
    {
        if (count($row) < 3) {
            throw new RenderException('暂时只支持 3 列表格');
        }

        foreach ($row as &$cell) {
            $cell = convert2utf8($cell);
        }
        list($first, $secend, $third) = $row;

        $halfWidth = $this->getByteLength($size) / 2;

        $third = space($halfWidth - mb_strwidth($secend) - mb_strwidth($third)).$third;

        if (mb_strwidth($first) > $halfWidth) {
            $processedFirst = $first.'<BR>';
            $processedSecend = space($halfWidth).$secend;
            $processedThird = space($halfWidth - mb_strwidth($secend) - mb_strwidth($third)).$third;
        } else {
            $processedFirst = $first.space($halfWidth - mb_strwidth($first));
            $processedSecend = $secend;
            $processedThird = space($halfWidth - mb_strwidth($secend) - mb_strwidth($third)).$third;
        }

        $result = [$processedFirst, $processedSecend, $processedThird];
        $resultStr = implode('', $result);

        return $resultStr;
    }

    /**
     * 小号表格
     *
     * @param array $items
     *
     * @return mixed
     */
    public function tableSmall($items)
    {
        return $this->table($items, 'small');
    }

    /**
     * 中号表格
     *
     * @param array $items
     *
     * @return mixed
     */
    public function tableMedium($items)
    {
        return $this->table($items, 'medium');
    }

    /**
     * 大号表格
     *
     * @return mixed
     */
    public function tableLarge($items)
    {
        return $this->table($items, 'large');
    }

    /**
     * 行分割线
     *
     * @return mixed
     */
    abstract public function division($division = null);

    /**
     * 两边围绕.
     *
     * @param string $text
     * @param int    $times
     * @param string $around
     * @param string $size
     *
     * @return mixed
     */
    abstract public function around(string $text, $times = 1, $around = '·', $size = 'small');

    /**
     * 转义.
     *
     * @param string $text
     *
     * @return mixed
     */
    public function section(string $text)
    {
        foreach ($this->escapes as $escape) {
            $text = str_replace($escape, '\\'.$escape, $text);
        }

        return $this->push($text);
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function push(string $text)
    {
        array_push($this->content, $text);

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function render()
    {
        return implode($this->glue, $this->content);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strval($this->render());
    }

    /**
     * 自动静态调用.
     *
     * @param string $method
     * @param $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = new static();

        if (!method_exists($instance, $method)) {
            throw new AdapterException(sprintf('not found method %s::%s', __CLASS__, $method));
        }

        return $instance->{$method}(...$args);
    }
}
