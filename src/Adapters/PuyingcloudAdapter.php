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
     * @param bool   $needBr
     *
     * @return string
     */
    public function br($text, $endOfLine = true, $needBr = true)
    {
        if (!$needBr) {
            return $text;
        }

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
     * @param bool   $needBr
     *
     * @return string
     */
    protected function warpBr($format, $content, $needBr = true)
    {
        return sprintf($format, $this->br($content, $needBr));
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

        return sprintf('%s%s', $this->c('<LOGO1>'), $text);
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
     * @param bool   $needBr
     *
     * @return string
     */
    public function cb(string $text, $needBr = true)
    {
        return $this->warpBr('<CB>%s</CB>', $text, $needBr);
    }

    /**
     * 放大一倍.
     *
     * @param string $text
     * @param bool   $needBr
     *
     * @return string
     */
    public function b(string $text, $needBr = true)
    {
        return $this->warpBr('<B>%s</B>', $text, $needBr);
    }

    /**
     * 居中.
     *
     * @param string $text
     * @param bool   $needBr
     *
     * @return string
     */
    public function c(string $text, $needBr = true)
    {
        return $this->warpBr('<C>%s</C>', $text, $needBr);
    }

    /**
     * 居中.
     *
     * @param string $text
     * @param bool   $needBr
     *
     * @return string
     */
    public function center(string $text, $needBr = true)
    {
        return $this->c($text, $needBr);
    }

    /**
     * 字体变高一倍.
     *
     * @param string $text
     * @param bool   $needBr
     *
     * @return string
     */
    public function l(string $text, $needBr = true)
    {
        return $this->warpBr('<L>%s</L>', $text, $needBr);
    }

    /**
     * 字体变宽一倍.
     *
     * @param string $text
     * @param bool   $needBr
     *
     * @return string
     */
    public function w(string $text, $needBr = true)
    {
        return $this->warpBr('<W>%s</W>', $text, $needBr);
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
        return sprintf('<C><QR>%s<BR></QR></C>', $text);
    }

    /**
     * 右对齐.
     *
     * @param string $text
     * @param bool   $needBr
     *
     * @return string
     */
    public function right(string $text, $needBr = true)
    {
        return $this->warpBr('<RIGHT>%s</RIGHT>', $text, $needBr);
    }

    /**
     * 字体加粗.
     *
     * @param string $text
     * @param bool   $needBr
     *
     * @return string
     */
    public function bold(string $text, $needBr = true)
    {
        return $this->warpBr('<BOLD>%s</BOLD>', $text, $needBr);
    }

    /**
     * 结束内容排版.
     *
     * @param string $text
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

    public function textSmall(string $text, $bold = false)
    {
        return $this->section($text);
    }

    public function textMedium(string $text, $needBr = true, $bold = false)
    {
        $br = '';
        if ($needBr) {
            $br = '<BR>';
        }

        if ($bold) {
            return $this->push("<B>{$text}$br</B>");
        }

        return $this->push("<L>{$text}$br</L>");
    }

    public function textLarge(string $text, $needBr = true, $bold = false)
    {
        $br = '';
        if ($needBr) {
            $br = '<BR>';
        }

        if ($bold) {
            return $this->push("<B>{$text}$br</B>");
        }

        return $this->push("<L>{$text}$br</L>");
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
     * @param bool   $needBr
     * @param bool   $bold
     *
     * @return $this
     */
    public function around(string $text, $times = 1, $around = '·', $size = 'small', $needBr = true, $bold = true)
    {
        $text = convert2utf8($text);
        $strWidth = mb_strwidth($text);

        // 左、右需要重复的字符串
        // (小票宽度 - (字符串长度 + 2 字节空格)) / 2
        $textWidth = $strWidth + 2;
        $avaliableWidth = $this->getByteLength($size, $bold) - $textWidth;

        $halfAround = times($around, $avaliableWidth / 2);

        $newText = "{$halfAround} {$text} {$halfAround}";

        // 将组合的字宽 = text 字宽 + 2 个空格 + 一半剩余 * 2 的长度
        $newTextWidth = mb_strwidth($text) + 2 + strlen($halfAround) * 2;
        if ($newTextWidth !== $this->getByteLength($size, $bold)) {
            $newTextWidth = $newText.$around;
        }

        $method = 'text'.ucfirst($size);

        return $this->{$method}($newText, $needBr, $bold);
    }
}
