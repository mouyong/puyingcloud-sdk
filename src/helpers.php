<?php

if (!function_exists('times')) {
    /**
     * 重复
     *
     * @param string $text
     * @param int $multiplier
     *
     * @return string
     */
    function times($text, $multiplier = 1)
    {
        return str_repeat($text, $multiplier);
    }
}

if (!function_exists('space')) {
    /**
     * 空格
     *
     * @param int $multiplier
     * @param string $input
     *
     * @return string
     */
    function space($multiplier = 0, $input = ' ')
    {
        return times($input, $multiplier);
    }
}

if (!function_exists('convert2utf8')) {
    /**
     * 文本转换为 utf-8 编码
     *
     * @param int $multiplier
     * @param string $input
     *
     * @return string
     */
    function convert2utf8($text)
    {
        $encoding = mb_detect_encoding($text, mb_detect_order(), false);

        if ($encoding == "UTF-8") {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        $out = iconv(mb_detect_encoding($text, mb_detect_order(), false), "UTF-8//IGNORE", $text);

        return $out;
    }
}