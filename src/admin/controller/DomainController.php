<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Log;
use \src\admin\model\DomainPoolModel;

require_once ROOT_PATH . '/aliyun-openapi-php-sdk/aliyun-php-sdk-core/Config.php';
use Domain\Request\V20160511 as Domain;

class DomainController extends AdminController
{
    const ONE_PAGE_SIZE = 20;

    public function checkPage()
    {
        $data = array(
            'title' => '检查域名是否注册',
            'action' => '/admin/Domain/check',
        );
        $this->display('domain_check', $data);
    }
    public function check()
    {
        $domain = trim($this->postParam('domain', ''));
        if (empty($domain)) {
            echo '请输入域名';
            return ;
        }
        $iClientProfile = \DefaultProfile::getProfile(
            'cn-hangzhou',
            ACCESS_KEY_ID,
            ACCESS_KEY_SECRET
        );
        $client = new \DefaultAcsClient($iClientProfile);
        $checkResult = array();
        $domainFix = array('.com', '.cn', '.com.cn', '.net', '.net.cn', '.org', '.org.cn', '.xin', '.xyz', '.win',
            '.wang', '.top', '.cc', '.site', '.ren', '.link', '.mobi', '.info',
        );
        foreach ($domainFix as $val) {
            $request = new Domain\CheckDomainRequest();
            $request->setDomainName($domain . $val);
            try {
                $response = $client->getAcsResponse($request);
                $checkResult[] = array('domain' => $domain . $val, 'result' => ($response->Avail == 1 ? '可注册' : '已注册'), 'canOrder' => $response->Avail);
            } catch (\ClientException $e) {
                $checkResult[] = array('domain' => $domain . $val, 'result' => $e->getMessage(), 'canOrder' => 0);
            }
        }
        $data = array(
            'title' => '检查结果',
            'dataList' => $checkResult,
        );
        $this->display('domain_check_result', $data);
    }
    public function orderPage()
    {
        $data = array(
            'action' => '/admin/Domain/order',
        );
        $this->display('domain_order', $data);
    }
    public function order()
    {
        $domain = trim($this->postParam('domain', ''));
        if (empty($domain)) {
            echo '请输入域名';
            return ;
        }
        $iClientProfile = \DefaultProfile::getProfile(
            'cn-hangzhou',
            ACCESS_KEY_ID,
            ACCESS_KEY_SECRET
        );
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new Domain\CreateOrderRequest();
        $request->setSubOrderParam(
            "SubOrderParam.1.Action=activate&SubOrderParam.1.RelatedName={$domain}&SubOrderParam.1.Period=12&SubOrderParam.1.DomainTemplateID=0000000"
        );

        try {
            $response = $client->getAcsResponse($request);
            if ($this->isAjax())
                $this->ajaxReturn(0, '下单成功，订单号：' . $response->OrderID, '');
            else
                echo '下单成功，订单号：' . $response->OrderID;
        } catch (\ClientException $e) {
            if ($this->isAjax())
                $this->ajaxReturn(ERR_PARAMS_ERROR, $e->getMessage(), '');
            else
                echo $e->getMessage();
        }
    }
    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = DomainPoolModel::fetchCount([], [], []);
        $dataList = DomainPoolModel::fetchSome([], [], [], $page, self::ONE_PAGE_SIZE);
        $dataList = $this->fillDataList($dataList);

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Domain/listPage',
            $searchParams
        );
        $data = array(
            'dataList' => $dataList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("domain_list", $data);
    }
    public function search()
    {
        $dataList = array();
        $totalNum = 0;
        $error = '';
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $searchDomain = trim($this->getParam('searchDomain', ''));
            if (empty($searchDomain)) {
                header('Location: /admin/Domain/listPage');
                return ;
            }
            if (!empty($searchDomain)) {
                $searchParams['searchDomain'] = $searchDomain;
                $ret = DomainPoolModel::searchDomain($searchDomain, $page, self::ONE_PAGE_SIZE);
                if (!empty($ret)) {
                    $dataList = $ret;
                    $totalNum = DomainPoolModel::searchDomainCount($searchDomain);
                }
            }
        } while(false);

        $dataList = $this->fillDataList($dataList);

        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Domain/search',
            $searchParams
        );
        $data = array(
            'dataList' => $dataList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display('domain_list', $data);
    }
    public function addPage()
    {
        $data = array(
            'title' => '域名绑定',
            'coupon' => array(),
            'action' => '/admin/Dns/add',
        );
        $this->display('dns_add', $data);
    }
    public function importDomainPage()
    {
        $data = array(
            'title' => '域名倒入',
            'coupon' => array(),
            'action' => '/admin/Domain/importDomain',
        );
        $this->display('domain_import', $data);
    }
    public function importDomain()
    {
        $domainListP = trim($this->postParam('domainList', ''));
        if (strpos(',', $domainListP) !== false) {
            $domainList = explode(',', $domainListP);
        } else {
            $domainList = explode("\n", $domainListP);
        }
        if (count($domainList) > 1000) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '域名不能超过1000个');
            return ;
        }
        if (empty($domainList)) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '域名不能为空');
            return ;
        }
        $domainType = trim($this->postParam('domainType', ''));
        if ($domainType != 'A' && $domainType != 'B') {
            $this->ajaxReturn(ERR_PARAMS_ERROR, '域名类型不对');
            return ;
        }
        foreach ($domainList as $domain) {
            DomainPoolModel::newOne(
                $domain,
                $domainType,
                'ok'
            );
        }
        $this->ajaxReturn(0, '域名导入成功');
    }
    public function add()
    {
        $error = '';
        $params = array();
        $ret = $this->fetchFormParams($params, $error);
        if ($ret === false) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $error, '');
            return ;
        }
        $iClientProfile = \DefaultProfile::getProfile(
            'cn-hangzhou',
            ACCESS_KEY_ID,
            ACCESS_KEY_SECRET
        );
        $client = new \DefaultAcsClient($iClientProfile);
        $domainList = array();
        foreach ($params['domainList'] as $idx => $domain) {
            if (empty($domain))
                continue;
            $ret = DnsModel::newOne(
                $domain,
                $params['rr'],
                'A',
                $params['value']
            );
            if ($ret !== false) {
                $domainList[] = array('Index' => $idx + 1, 'Domain' => $domain);
                $request = new AliDns\AddDomainRecordRequest();
                $request->setMethod("GET");
                $request->setDomainName($domain);
                $request->setRR($params['rr']);
                $request->setType('A');
                $request->setValue($params['value']);
                try {
                    $response = $client->getAcsResponse($request);
                } catch (\ClientException $e) {
                    DnsModel::update(
                        $domain,
                        $params['rr'],
                        array(
                            'state' => 'add error',
                            'remark' => $e->getMessage(),
                        )
                    );
                    continue;
                }
            }
        }
        
        /*
        $record = array(
            'RR' => $params['rr'],
            'Type' => 'A',
            'Value' => $params['value'],
            'Domains' => $domainList,
        );
        $request = new AliDns\AddBatchDomainRecordsRequest();
        $request->setMethod("POST");
        $request->setRecords(json_encode($record));
        */
        $this->ajaxReturn(0, '提交任务成功!', '/admin/Dns/listPage');
    }
    public function del()
    {
        $id = $this->getParam('id', 0);
        if ($id == 0) {
            header('Location: /admin/Domain/listPage');
            return ;
        }
        DomainPoolModel::del($id);
        header('Location: /admin/Domain/listPage');
    }
    private function fetchFormParams(&$params, &$error)
    {
        $params['domainList'] = trim($this->postParam('domainList', ''));
        $params['domainList'] = explode(',', $params['domainList']);
        if (count($params['domainList']) > 5000) {
            $error = '解析记录不能超过5000个';
            return false;
        }
        if (empty($params['domainList'])) {
            $error = '域名不能为空';
            return false;
        }
        $params['value'] = trim($this->postParam('value', ''));
        if (empty($params['value'])) {
            $error = 'IP不能为空';
            return false;
        }
        if (empty($params['rr'])) {
            $params['rr'] = '@';
        }
        return true;
    }
    private function fillDataList($dataList)
    {
        foreach ($dataList as &$one) {
            $one['ctime'] = date('Y-m-d H:i:s', $one['ctime']);
            $one['mtime'] = date('Y-m-d H:i:s', $one['mtime']);
        }
        return $dataList;
    }
}
