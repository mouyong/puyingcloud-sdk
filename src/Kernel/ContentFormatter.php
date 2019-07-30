<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk\Kernel;

use Yan\PuyingCloudSdk\Exceptions\FormatException;

class ContentFormatter
{
    protected $content;

    /**
     * 添加换行标签.
     *
     * @param string $content
     * @param bool   $endOfLine 行尾
     *
     * @return string
     */
    protected function br($content, $endOfLine = true)
    {
        if ($endOfLine) {
            return sprintf('%s<BR>', $content);
        }

        return sprintf('<BR>%s', $content);
    }

    /**
     * 将内容嵌套在指定格式的 format 中，并在内容尾部和行尾加换行符.
     *
     * @param string $format
     * @param string $content
     *
     * @return string
     */
    protected function warpBr($format, $content)
    {
        return $this->br(
            sprintf($format, $this->br($content))
        );
    }

    /**
     * 打印烧录到打印机中的 logo.
     *
     * @param string $content
     *
     * @return string
     */
    protected function logo($content, $endOfLine = true)
    {
        if ($endOfLine) {
            return sprintf('%s%s', $content, $this->c('<LOGOn>'));
        }

        return sprintf('%s%s', $this->c('<LOGOn>'), $content);
    }

    protected function cash($content)
    {
        return sprintf('<Cash>%s', $content);
    }

    /**
     * 居中放大.
     *
     * @param string $content
     *
     * @return string
     */
    protected function cb($content)
    {
        return $this->warpBr('<CB>%s</CB>', $content);
    }

    /**
     * 放大一倍.
     *
     * @param string $content
     *
     * @return string
     */
    protected function b($content)
    {
        return $this->warpBr('<B>%s</B>', $content);
    }

    /**
     * 居中.
     *
     * @param string $content
     *
     * @return string
     */
    protected function c($content)
    {
        return $this->warpBr('<C>%s</C>', $content);
    }

    /**
     * 字体变高一倍.
     *
     * @param string $content
     *
     * @return string
     */
    protected function l($content)
    {
        return $this->warpBr('<L>%s</L>', $content);
    }

    /**
     * 字体变宽一倍.
     *
     * @param string $content
     *
     * @return string
     */
    protected function w($content)
    {
        return $this->warpBr('<W>%s</W>', $content);
    }

    /**
     * 居中二维码
     *
     * @param string $content
     *
     * @return string
     */
    protected function qrcode($content)
    {
        return sprintf('<C><QR>%s</QR></C>', $content);
    }

    /**
     * 右对齐.
     *
     * @param string $content
     *
     * @return string
     */
    protected function right($content)
    {
        return $this->warpBr('<RIGHT>%s</RIGHT>', $content);
    }

    /**
     * 字体加粗.
     *
     * @param string $content
     *
     * @return string
     */
    protected function bold($content)
    {
        return $this->warpBr('<BOLD>%s</BOLD>', $content);
    }

    /**
     * 结束内容排版.
     *
     * @param string $content
     *
     * @return string
     */
    protected function end($content)
    {
        return sprintf('%s<CUT>', $this->br($content));
    }

    /**
     * 将其他排版指令设置为出厂状态
     *
     * @param $content
     * @return string
     */
    protected function init($content)
    {
        return sprintf('<Init>%s', $this->br($this->br($content), false));
    }

    /**
     * 标题
     *
     * @param string $title
     * @return string
     */
    protected function title($title)
    {
        return $this->cb($title);
    }

    /**
     * 静态调用
     *
     * @param string $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = new static();

        if (!method_exists($instance, $method)) {
            throw new FormatException('not found method %s::%s', __CLASS__, $method);
        }

        return $instance->{$method}(...$args);
    }
}
