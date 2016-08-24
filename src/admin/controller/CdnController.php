<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Log;
use \src\admin\model\CdnDomainModel;

require_once ROOT_PATH . '/aliyun-openapi-php-sdk/aliyun-php-sdk-core/Config.php';
use Cdn\Request\V20141111 as Cdn;

class CdnController extends AdminController
{
    const ONE_PAGE_SIZE = 20;

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = CdnDomainModel::fetchCount([], [], []);
        $dataList = CdnDomainModel::fetchSome([], [], [], $page, self::ONE_PAGE_SIZE);
        $dataList = $this->fillDataList($dataList);

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Cdn/listPage',
            $searchParams
        );
        $data = array(
            'dataList' => $dataList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("cdn_list", $data);
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
                header('Location: /admin/Cdn/listPage');
                return ;
            }
            if (!empty($searchDomain)) {
                $searchParams['searchDomain'] = $searchDomain;
                $ret = CdnDomainModel::searchDomain($searchDomain, $page, self::ONE_PAGE_SIZE);
                if (!empty($ret)) {
                    $dataList = $ret;
                    $totalNum = CdnDomainModel::searchDomainCount($searchDomain);
                }
            }
        } while(false);

        $dataList = $this->fillDataList($dataList);

        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Cdn/search',
            $searchParams
        );
        $data = array(
            'dataList' => $dataList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display('cdn_list', $data);
    }
    public function addPage()
    {
        $data = array(
            'title' => '添加CDN域名',
            'action' => '/admin/Cdn/add',
        );
        $this->display('cdn_add', $data);
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
        $ret = CdnDomainModel::findDomain($params['domainList'][0]);
        if (!empty($ret)) {
            $this->ajaxReturn(0, '提交任务成功!', '');
            return ;
        }
        $ret = CdnDomainModel::newOne(
            $params['domainList'][0],
            $params['source'],
            'ipaddr',
            80,
            'web'
        );
        if ($ret === false) {
            $this->ajaxReturn(0, '保存数据失败', '');
            return ;
        }
        $iClientProfile = \DefaultProfile::getProfile(
            'cn-hangzhou',
            ACCESS_KEY_ID,
            ACCESS_KEY_SECRET
        );
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Cdn\AddCdnDomainRequest();
        $request->setMethod('GET');
        $request->setDomainName($params['domainList'][0]);
        $request->setCdnType('web');
        $request->setSourceType('ipaddr');
        $request->setSources($params['source']);
        try {
            $response = $client->getAcsResponse($request);
        } catch (\ClientException $e) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $e->getMessage(), '');
            CdnDomainModel::update(
                $params['domainList'][0],
                array(
                    'state' => 'add error',
                    'remark' => $e->getMessage(),
                )
            );
            return ;
        }
        $this->ajaxReturn(0, '提交任务成功!', '/admin/Cdn/listPage');
    }
    public function modifySource()
    {
        $domain = $this->postParam('domain', '');
        $source = $this->postParam('source', '');
        CdnDomainModel::update(
            $domain,
            array('source' => $source, 'mtime' => CURRENT_TIME)
        );
        $this->ajaxReturn(0, '', '/admin/Cdn/listPage');
    }
    public function del()
    {
        $id = $this->getParam('id', 0);
        if ($id == 0) {
            header('Location: /admin/Cdn/listPage');
            return ;
        }
        CdnDomainModel::del($id);
        header('Location: /admin/Cdn/listPage');
    }
    private function fetchFormParams(&$params, &$error)
    {
        $params['domainList'] = trim($this->postParam('domainList', ''));
        $params['domainList'] = explode(',', $params['domainList']);
        if (count($params['domainList']) > 500) {
            $error = '解析记录不能超过500个';
            return false;
        }
        if (empty($params['domainList'])) {
            $error = '域名不能为空';
            return false;
        }
        $params['source'] = trim($this->postParam('source', ''));
        if (empty($params['source'])) {
            $error = '回源IP不能为空';
            return false;
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
