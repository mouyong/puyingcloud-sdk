<?php

namespace Yan\PuyingCloudSdk\Adapters;

abstract class PuyingcloudAdapter extends Adapter
{
    protected $glue = "<BR>";

    /**
     * 添加换行标签.
     *
     * @param string $text
     * @param bool $endOfLine 行尾
     *
     * @return string
     */
    public function br($text, $endOfLine = true)
    {
        if ($endOfLine) {
            $text = "{$text}<BR>";
        } else {
            $text = "<BR>{$text}";
        }

        return $this->push($text);
    }    /**
 * 打印烧录到打印机中的 logo.
 *
 * @param string $text
 * @param int $order 词机型打印机可烧录 4 个 logo 图 n 为 1~4
 * @param bool $endOfLine
 * @return $this
 */
    public function logo($text, $order = 1, $endOfLine = true)
    {
        $logon = "<LOGO{$order}>";

        if ($endOfLine) {
            $text = "{$text}{$logon}";
        } else {
            $text = "{$logon}{$text}";
        }

        return $this->push($text);
    }

    /**
     * @param string $text
     * @return $this
     */
    public function cash(string $text)
    {
        return $this->push("<Cash>{$text}");
    }

    /**
     * 居中放大.
     *
     * @param string $text
     * @return $this
     */
    public function cb(string $text)
    {
        return $this->push("<CB>{$text}<BR></CB>");
    }

    /**
     * 放大一倍.
     *
     * @param string $text
     * @return $this
     */
    public function b(string $text)
    {
        return $this->push("<B>{$text}<BR></B>");
    }

    /**
     * 居中.
     *
     * @param string $text
     * @return $this
     */
    public function c(string $text)
    {
        return $this->push("<C>{$text}<BR></C>");
    }

    /**
     * 居中.
     *
     * @param string $text
     * @return $this
     */
    public function center(string $text)
    {
        return $this->c($text);
    }

    /**
     * 字体变高一倍.
     *
     * @param string $text
     * @return $this
     */
    public function l(string $text)
    {
        return $this->push("<L>{$text}<BR></L>");
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
        return $this->push("<W>{$text}<BR></W>");
    }

    /**
     * 居中二维码.
     *
     * @param string $text
     * @return $this
     */
    public function qrcode(string $text)
    {
        return $this->push("<C><QR>{$text}<BR></QR><BR></C>");
    }

    /**
     * 右对齐.
     *
     * @param string $text
     *
     * @return $this
     */
    public function right(string $text)
    {
        return $this->push("<RIGHT>{$text}<BR></RIGHT>");
    }

    /**
     * 字体加粗.
     *
     * @param string $text
     *
     * @return $this
     */
    public function bold(string $text)
    {
        return $this->push("<BOLD>{$text}<BR></BOLD>");
    }

    /**
     * 结束内容排版.
     *
     * @return $this
     */
    public function cut()
    {
        return $this->push('<CUT>');
    }

    /**
     * 将其他排版指令设置为出厂状态.
     *
     * @param $text
     *
     * @return $this
     */
    public function init(string $text)
    {
        return $this->push("<Init>{$text}");
    }

    /**
     * 语音播报
     *
     * @param string $text
     * @return $this
     */
    public function VO(string $text)
    {
        return $this->push("<VO>{$text}<BR></VO>");
    }

    public function textSmall(string $text): Adapter
    {
        return $this->section($text);
    }

    public function textMedium(string $text): Adapter
    {
        return $this->section("<B>{$text}<BR></B>");
    }

    public function textLarge(string $text): Adapter
    {
        return $this->push("<B>{$text}<BR></B>");
    }

    public function division($division = null): Adapter
    {
        $division = $division ?: times('·', $this->byteLength);

        return $this->section($division);
    }

    /**
     * 围绕文字.
     *
     * @param string $text
     * @param int $times
     * @param string $around
     * @param string $size
     * @return $this
     */
    public function around($text, $times = 1, $around = '·', $size = 'small'): Adapter
    {
        $text = convert2utf8($text);
        $strlen = strlen($text);

        // 左、右需要重复的字符串
        // (小票宽度 - 字符串长度 - 2 字节空格) / 2
        $halfAround = times($around, ($this->getByteLength($size) - $strlen - 2) / 2);

        $text = "{$halfAround} {$text} {$halfAround}";

        if (mb_strwidth($text) !== $this->getByteLength($size)) {
            $text = $text.$around;
        }
        return $this->section($text);
    }
}