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

    public function getByteLength($size = 'small', $bold = false): int
    {
        // bold 为 true 时，字体倍宽，能放下的数据减少一半
        if (! $bold) {
            return $this->byteLength;
        }

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
     * @param bool $bold
     * @return mixed
     */
    abstract public function textSmall(string $text, $bold = false);

    /**
     * 中号字体.
     *
     * @param string $text
     * @param bool $needBr
     * @param bool $bold
     * @return mixed
     */
    abstract public function textMedium(string $text, $needBr = true, $bold = false);

    /**
     * 大号字体.
     *
     * @param string $text
     *
     * @return mixed
     */
    abstract public function textLarge(string $text, $needBr = true, $bold = false);

    /**
     * 表格
     *
     * @param array $items
     * @param string $size
     * @param bool $bold
     * @return mixed
     */
    public function table($items, $size = 'small', $bold = false)
    {
        $method = ['small' => 'textSmall', 'medium' => 'textMedium', 'large' => 'textLarge'][$size];

        foreach ($items as $key => $value) {
            $this->{$method}($this->calcTableSpace($value, $size, $bold), $bold);
        }

        return $this;
    }

    /**
     * 表格空白补全.
     *
     * @param array $row
     * @param string $size
     * @param bool $bold
     * @return mixed
     */
    public function calcTableSpace(array $row, $size, $bold = false)
    {
        if (count($row) < 3) {
            throw new RenderException('暂时只支持 3 列表格');
        }

        foreach ($row as &$cell) {
            $cell = convert2utf8($cell);
        }
        list($first, $secend, $third) = $row;

        // 计算一半字宽长度是多少
        $halfWidth = $this->getByteLength($size, $bold) / 2;

        // 先处理第三列，第三列前补 2 个空格
        $processedThird = space(2).$third;

        // 计算第一、二、处理好的第三列占用去多长字宽
        $firstWidth = mb_strwidth($first);
        $secendWidth = mb_strwidth($secend);
        $thirdWidth = mb_strwidth($processedThird);

        // 1. 第一列超出一半宽度时，需要换行
        // 2. 第二列 + 第三列 字宽超过一半时，第一列需要换行
        if ($firstWidth >= $halfWidth || ($secendWidth + $thirdWidth) > $halfWidth) {
            $processedFirst = $first.'<BR>';
        } else {
            $processedFirst = $first.space($halfWidth - $firstWidth);
        }

        // 处理第三列

        // 第二列 + 第三列 字宽超过一半时，换行后，需要第一列换行并少占用第二行的空格
        // 此时第三列先占位
        if ($firstWidth >= $halfWidth || ($secendWidth + $thirdWidth) > $halfWidth) {
            // 处理第二列（第二行时，第一列占空数 = 整行字宽 - 处理好的第三列字宽 - 第二列字宽）
            $firstNeedSpaceNum = ($halfWidth * 2) - $thirdWidth - $secendWidth;
            $processedSecend = space($firstNeedSpaceNum).$secend;
        } else {
            // 处理第二列后面需要补充多少空格
            $secendNeedSpaceNum = $halfWidth - $thirdWidth - $secendWidth;
            $processedSecend = space($secendNeedSpaceNum).$secend;
        }

        // 返回结果
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
    public function tableSmall($items, $bold = false)
    {
        return $this->table($items, 'small', $bold);
    }

    /**
     * 中号表格
     *
     * @param array $items
     *
     * @return mixed
     */
    public function tableMedium($items, $bold = false)
    {
        return $this->table($items, 'medium', $bold);
    }

    /**
     * 大号表格
     *
     * @return mixed
     */
    public function tableLarge($items, $bold = false)
    {
        return $this->table($items, 'large', $bold);
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
