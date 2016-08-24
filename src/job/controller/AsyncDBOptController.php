<?php
/**
 * @Author shaowei
 * @Date   2015-12-02
 */

namespace src\job\controller;

use \src\common\Nosql;
use \src\common\WxSDK;
use \src\common\Log;
use \src\job\model\AsyncModel;
use \src\admin\model\DnsModel;
use \src\admin\model\DomainPoolModel;

class AsyncDBOptController extends JobController
{
    const ASYNC_DB_OPT_QUEUE_SIZE = 2;

    public function doit()
    {
        $this->spawnTask(self::ASYNC_DB_OPT_QUEUE_SIZE);
    }

    protected function run($idx)
    {
        $nk = Nosql::NK_ASYNC_DB_OPT_QUEUE;
        $beginTime = time();

        do {
            do {
                $rawMsg = Nosql::lPop($nk);
                if ($rawMsg === false
                    || !isset($rawMsg[0])) {
                    break;
                }
                $data = json_decode($rawMsg, true);
                $this->doOpt($data);
            } while (true);

            if (time() - $beginTime > 30) { // 30秒脚本重新执行一次
                break;
            }
            usleep(200000);
        } while (true);
    }

    private function doOpt($data)
    {
        switch ($data['opt']) {
        case 'insert_domain_pool':
            DomainPoolModel::newOne(
                $data['data']['domain'],
                $data['data']['type'],
                $data['data']['state']
            );
            break;
        case 'add_dns':
            DnsModel::newOne(
                $data['data']['domain'],
                $data['data']['rr'],
                'A',
                $data['data']['value'],
                $data['data']['ok']
            );
            break;
        default:
            Log::error(__FILE__ . ' unknow event');
        }
    }
}

