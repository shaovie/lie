<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Log;
use \src\job\model\AsyncModel;
use \src\admin\model\DnsModel;

require_once ROOT_PATH . '/aliyun-openapi-php-sdk/aliyun-php-sdk-core/Config.php';
use Alidns\Request\V20150109 as Alidns;

class DnsController extends AdminController
{
    const ONE_PAGE_SIZE = 20;

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = DnsModel::fetchCount([], [], []);
        $dataList = DnsModel::fetchSome([], [], [], $page, self::ONE_PAGE_SIZE);
        $dataList = $this->fillDataList($dataList);

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Dns/listPage',
            $searchParams
        );
        $data = array(
            'dataList' => $dataList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("dns_list", $data);
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
                header('Location: /admin/Dns/listPage');
                return ;
            }
            if (!empty($searchDomain)) {
                $searchParams['searchDomain'] = $searchDomain;
                $ret = DnsModel::searchDomain($searchDomain, $page, self::ONE_PAGE_SIZE);
                if (!empty($ret)) {
                    $dataList = $ret;
                    $totalNum = DnsModel::searchDomainCount($searchDomain);
                }
            }
        } while(false);

        $dataList = $this->fillDataList($dataList);

        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Dns/search',
            $searchParams
        );
        $data = array(
            'dataList' => $dataList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display('dns_list', $data);
    }
    public function addPage()
    {
        $data = array(
            'title' => '域名绑定',
            'action' => '/admin/Dns/add',
        );
        $this->display('dns_add', $data);
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
        $str = $params['rr'];
        $rrList = array();
        $leftK = strpos($str, '[');
        $rightK = strpos($str, ']');
        if ($leftK !== false && $leftK < $rightK) {
            $pattern = substr($str, $leftK + 1, $rightK - $leftK - 1);
            $leftS = substr($str, 0, $leftK);
            $rightS = substr($str, $rightK + 1);
            $pattern = explode('-', $pattern);
            if (is_numeric($pattern[0])) {
                $pattern[0] = intval($pattern[0]);
                $pattern[1] = intval($pattern[1]);
                for ($i = $pattern[0]; $i <= $pattern[1]; $i++) {
                    $rrList[] = $leftS . $i . $rightS;
                }
            } else {
                for ($i = ord($pattern[0]); $i <= ord($pattern[1]); $i++) {
                    $rrList[] = $leftS . chr($i) . $rightS;
                }
            }
        } else {
            $rrList = array($params['rr']);
        }

        foreach ($params['domainList'] as $idx => $domain) {
            if (empty($domain))
                continue;
            foreach ($rrList as $rr) {
                AsyncModel::asyncAliDnsOpt(
                    'add',
                    array('domain' => $domain,
                        'rr' => $rr,
                        'value' => $params['value']
                    )
                );
            }
        }
        
        $this->ajaxReturn(0, '提交任务成功!', '/admin/Dns/listPage');
    }
    public function modifyRecord()
    {
        $domain = $this->postParam('domain', '');
        $rr = $this->postParam('rr', '');
        $rtype = $this->postParam('rtype', '');
        $value = $this->postParam('value', '');
        DnsModel::update(
            $domain, $rr,
            array('record_type' => $rtype, 'value' => $value, 'mtime' => CURRENT_TIME)
        );
        $this->ajaxReturn(0, '', '/admin/Dns/listPage');
    }
    public function del()
    {
        $id = $this->getParam('id', 0);
        if ($id == 0) {
            header('Location: /admin/Dns/listPage');
            return ;
        }
        $ret = DnsModel::findDnsRecordById($id);
        if (!empty($ret)) {
            $iClientProfile = \DefaultProfile::getProfile(
                'cn-hangzhou',
                ACCESS_KEY_ID,
                ACCESS_KEY_SECRET
            );
            $client = new \DefaultAcsClient($iClientProfile);
            $request = new AliDns\DeleteDomainRecordRequest();
            $request->setMethod("GET");
            $request->setRecordId($ret['record_id']);
            try {
                $response = $client->getAcsResponse($request);
                if (!empty($response->RecordId)) {
                    DnsModel::del($id);
                }
            } catch (\ClientException $e) {
                Log::error($ret['domain'] . ' del error '
                    . ' rr = ' . $ret['rr']
                    . ' error = ' . $e->getMessage()
                );
            }
        }
        header('Location: /admin/Dns/listPage');
    }
    private function fetchFormParams(&$params, &$error)
    {
        $domainListP = trim($this->postParam('domainList', ''));
        if (strpos(',', $domainListP) !== false) {
            $params['domainList'] = explode(',', $domainListP);
        } else {
            $params['domainList'] = explode("\n", $domainListP);
        }
        if (count($params['domainList']) > 500) {
            $error = '解析记录不能超过500个';
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
        $params['rr'] = trim($this->postParam('rr', ''));
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
