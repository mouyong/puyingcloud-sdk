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
    public function list($offset = 0, $limit = 10, $query = null, $filter = [])
    {
        return $this->request('printer_list', [
            'offset' => intval($offset),
            'limit' => intval($limit),
            'query' => $query,
            'filter' => $filter,
        ]);
    }

    public function addOne(array $printer)
    {
        $printers = [
            $printer,
        ];

        return $this->add($printers);
    }

    public function add($printers = [])
    {
        $data = [];

        foreach ($printers as $index => &$printer) {
            if (empty($printer['sn']) || empty($printer['key'])) {
                throw new InvalidArgumentException("待添加打印机索引 {$index} 缺少必要参数 sn 或 key，请核实");
            }

            if (count($printer) < 3) {
                $name = sprintf('%s-%s', date('YmdHis'), substr(uniqid(), 0, 5));

                $printer['name'] = $name;
            }

            $data[] = sprintf('%s#%s#%s', $printer['sn'], $printer['key'], $printer['name']);
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

    public function remove($printersSns = [])
    {
        return $this->request('remove_printer', $printersSns);
    }

    public function createPrintTask($sn, $title, $content, $count = 1, $interval = 0)
    {
        return $this->request('add_task', [
            'count' => $count,
            'interval' => $interval,
            'title' => $title,
            'content' => strval($content),
            'sn' => $sn,
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

    public function cancelWaitPrintTask($printersSn)
    {
        return $this->request('remove_task', [
            'sn' => strval($printersSn),
        ]);
    }

    public function deviceStateStatistics()
    {
        return $this->request('device_state_statistics');
    }

    public function setPrintName($printerSn, $name)
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
