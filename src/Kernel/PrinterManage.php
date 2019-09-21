<?php

/*
 * This file is part of the mouyong/puyingcloud-sdk.
 *
 * (c) 牟勇 <my24251325@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yan\PuyingCloudSdk\Kernel;

use InvalidArgumentException;
use Yan\PuyingCloudSdk\Core\Api;

class PrinterManage extends Api
{
    public function getPrinterlist($offset = 0, $limit = 10, $query = null, $filter = [])
    {
        return $this->request('printer_list', [
            'offset' => intval($offset),
            'limit' => intval($limit),
            'query' => $query,
            'filter' => $filter,
        ]);
    }

    /**
     * 添加单台打印机.
     *
     * @param array $printer = ['sn' => $sn, 'key' => $key, 'alias' => $alias]
     *
     * @return array|mixed|null|\Psr\Http\Message\ResponseInterface
     */
    public function addOne(array $printer)
    {
        $printers = [
            $printer,
        ];

        return $this->add($printers);
    }

    /**
     * 批量添加打印机.
     *
     * @param array $printers = [
     *                        ['sn' => $sn, 'key' => $key, 'alias' => $alias],
     *                        ['sn' => $sn, 'key' => $key, 'alias' => $alias],
     *                        ...
     *                        ]
     *
     * @return array|mixed|null|\Psr\Http\Message\ResponseInterface
     */
    public function add($printers = [])
    {
        $data = [];

        foreach ($printers as $index => &$printer) {
            if (empty($printer['sn']) || empty($printer['key'])) {
                throw new InvalidArgumentException("待添加打印机索引 {$index} 缺少必要参数 sn 或 key，请核实");
            }

            if (count($printer) < 3 || empty($prefix['alias'])) {
                $printer['alias'] = $printer['sn'];
            }

            $data[] = sprintf('%s#%s#%s', $printer['sn'], $printer['key'], $printer['alias']);
        }

        return $this->request('add_printer', [
            'printers' => $data,
        ]);
    }

    public function removeOne($printerSn)
    {
        return $this->remove([
            $printerSn,
        ]);
    }

    public function remove($printerSns = [])
    {
        return $this->request('remove_printer', $printerSns);
    }

    public function createPrinterTask($printerSn, $content, $title = '', $count = 1, $interval = 0)
    {
        return $this->request('add_task', [
            'count' => $count,
            'interval' => $interval,
            'title' => $title,
            'content' => strval($content),
            'sn' => $printerSn,
        ]);
    }

    public function getPrinterTaskList($offset = 0, $limit = 10, $query = null, $filter = [])
    {
        return $this->request('task_list', [
            'offset' => intval($offset),
            'limit' => intval($limit),
            'query' => $query,
            'filter' => $filter,
        ]);
    }

    public function getPrinterTaskBySn($sn)
    {
        return $this->request('get_task', [
            'sn' => strval($sn),
        ]);
    }

    public function cancelWaitPrintTaskBySn($printerSn)
    {
        return $this->request('remove_task', [
            'sn' => strval($printerSn),
        ]);
    }

    public function deviceStateStatistics()
    {
        return $this->request('device_state_statistics');
    }

    public function setPrinterNameBySn($printerSn, $name)
    {
        return $this->request('update_printer', [
            'sn' => strval($printerSn),
            'name' => $name,
        ]);
    }

    public function printAmountStatistics($type = 'today')
    {
        return $this->request('print_amount_statistics', [
            'type' => $type,
        ]);
    }
}
