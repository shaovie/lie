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

require_once ROOT_PATH . '/aliyun-openapi-php-sdk/aliyun-php-sdk-core/Config.php';
use Alidns\Request\V20150109 as Alidns;

class AliDnsController extends JobController
{
    const ASYNC_ALI_DNS_QUEUE_SIZE = 2;

    public function doit()
    {
        $this->spawnTask(self::ASYNC_ALI_DNS_QUEUE_SIZE);
    }

    protected function run($idx)
    {
        $nk = Nosql::NK_ASYNC_ALI_DNS_QUEUE;
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
        case 'add':
            $this->addRecord($data['data']);
            break;
        default:
            Log::error(__FILE__ . ' unknow event');
        }
    }

    private function addRecord($data)
    {
        $iClientProfile = \DefaultProfile::getProfile(
            'cn-hangzhou',
            ACCESS_KEY_ID,
            ACCESS_KEY_SECRET
        );
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new AliDns\AddDomainRecordRequest();
        $request->setMethod("GET");
        $request->setDomainName($data['domain']);
        $request->setRR($data['rr']);
        $request->setType('A');
        $request->setValue($data['value']);
        try {
            $response = $client->getAcsResponse($request);
            if (!empty($response->RecordId)) {
                DnsModel::newOne(
                    $response->RecordId,
                    $data['domain'],
                    $data['rr'],
                    'A',
                    $data['value'],
                    'ok'
                );
            }
        } catch (\ClientException $e) {
            Log::error($data['domain'] . ' add error '
                . ' rr = ' . $data['rr']
                . ' error = ' . $e->getMessage()
                );
        }
    }
}

