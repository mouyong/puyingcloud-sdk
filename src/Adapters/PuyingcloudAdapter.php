<?php

namespace Yan\PuyingCloudSdk\Adapters;

abstract class PuyingcloudAdapter extends Adapter
{
    protected $glue = '<BR>';

    /**
     * 添加换行标签.
     *
     * @param string $text
     * @param bool   $endOfLine 行尾
     *
     * @return string
     */
    public function br($text, $endOfLine = true)
    {
        if ($endOfLine) {
            return sprintf('%s<BR>', $text);
        }

        return sprintf('<BR>%s', $text);
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
     * @param string $text
     * @param int    $order     词机型打印机可烧录 4 个 logo 图 n 为 1~4
     * @param bool   $endOfLine
     *
     * @return string
     */
    public function logo($text, $order = 1, $endOfLine = true)
    {
        if ($endOfLine) {
            return sprintf('%s%s', $text, $this->c(sprintf('<LOGO%s>', $order)));
        }

        return sprintf('%s%s', $this->c('<LOGOn>'), $text);
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function cash(string $text)
    {
        return sprintf('<Cash>%s', $text);
    }

    /**
     * 居中放大.
     *
     * @param string $text
     *
     * @return string
     */
    public function cb(string $text)
    {
        return $this->warpBr('<CB>%s</CB>', $text);
    }

    /**
     * 放大一倍.
     *
     * @param string $text
     *
     * @return string
     */
    public function b(string $text)
    {
        return $this->warpBr('<B>%s</B>', $text);
    }

    /**
     * 居中.
     *
     * @param string $text
     *
     * @return string
     */
    public function c(string $text)
    {
        return $this->warpBr('<C>%s</C>', $text);
    }

    /**
     * 居中.
     *
     * @param string $text
     *
     * @return string
     */
    public function center(string $text)
    {
        return $this->c($text);
    }

    /**
     * 字体变高一倍.
     *
     * @param string $text
     *
     * @return string
     */
    public function l(string $text)
    {
        return $this->warpBr('<L>%s</L>', $text);
    }

    /**
     * 字体变宽一倍.
     *
     * @param string $text
     *
     * @return string
     */
    public function w(string $text)
    {
        return $this->warpBr('<W>%s</W>', $text);
    }

    /**
     * 居中二维码.
     *
     * @param string $text
     *
     * @return string
     */
    public function qrcode(string $text)
    {
        return sprintf('<C><QR>%s<BR></QR><BR></C>', $text);
    }

    /**
     * 右对齐.
     *
     * @param string $text
     *
     * @return string
     */
    public function right(string $text)
    {
        return $this->warpBr('<RIGHT>%s</RIGHT>', $text);
    }

    /**
     * 字体加粗.
     *
     * @param string $text
     *
     * @return string
     */
    public function bold(string $text)
    {
        return $this->warpBr('<BOLD>%s</BOLD>', $text);
    }

    /**
     * 结束内容排版.
     *
     * @return string
     */
    public function cut($text = '')
    {
        return sprintf('%s<CUT>', $text);
    }

    /**
     * 将其他排版指令设置为出厂状态.
     *
     * @param $text
     *
     * @return string
     */
    public function init(string $text)
    {
        return sprintf('<Init>%s', $this->br($this->br($text), false));
    }

    /**
     * 语音播报.
     *
     * @param string $text
     *
     * @return string
     */
    public function VO(string $text)
    {
        return "<VO>{$text}<BR></VO>";
    }

    public function textSmall(string $text)
    {
        return $this->section($text);
    }

    public function textMedium(string $text)
    {
        return $this->section("<B>{$text}<BR></B>");
    }

    public function textLarge(string $text)
    {
        return $this->push("<B>{$text}<BR></B>");
    }

    public function division($division = null)
    {
        $division = $division ?: times('·', $this->byteLength);

        return $this->section($division);
    }

    /**
     * 围绕文字.
     *
     * @param string $text
     * @param int    $times
     * @param string $around
     * @param string $size
     *
     * @return $this
     */
    public function around(string $text, $times = 1, $around = '·', $size = 'small')
    {
        $text = convert2utf8($text);
        $strlen = strlen($text);

        // 左、右需要重复的字符串
        // (小票宽度 - 字符串长度 - 2 字节空格) / 2
        $halfAround = times($around, ($this->getByteLength($size) - $strlen) / 2);

        $text = "{$halfAround} {$text} {$halfAround}";

        if (mb_strwidth($text) !== $this->getByteLength($size)) {
            $text = $text.$around;
        }

        return $this->section($text);
    }
}
