<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Log;
use \src\admin\model\CdnDomainModel;

require_once ROOT_PATH . '/aliyun-openapi-php-sdk/aliyun-php-sdk-core/Config.php';
use Slb\Request\V20140515 as Slb;
use Cdn\Request\V20141111 as Cdn;

class SlbController extends AdminController
{
    const ONE_PAGE_SIZE = 20;

    public function regions()
    {
        $iClientProfile = \DefaultProfile::getProfile(
            'cn-hangzhou',
            ACCESS_KEY_ID,
            ACCESS_KEY_SECRET
        );
        $client = new \DefaultAcsClient($iClientProfile);
        $request = new Slb\DescribeRegionsRequest();

        try {
            $response = $client->getAcsResponse($request);
            foreach ($response->Regions->Region as $one) {
                echo $one->RegionId . '   ' . $one->LocalName . '</br>';
            }
        } catch (\ClientException $e) {
            echo $e->getMessage();
        }
    }
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
            'title' => '添加SLB实例',
            'action' => '/admin/Slb/add',
        );
        $this->display('slb_add', $data);
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
        $request = new Slb\CreateLoadBalancerRequest();
        $request->setRegionId($params['regionId']);
        $request->setAddressType('internet');
        $request->setLoadBalancerName($params['slbName']);

        try {
            $response = $client->getAcsResponse($request);
            $this->ajaxReturn(0, '提交任务成功!', '');
        } catch (\ClientException $e) {
            $this->ajaxReturn(ERR_PARAMS_ERROR, $e->getMessage(), '');
        }
        return ;

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
        $params['regionId'] = trim($this->postParam('regionId', ''));
        if (empty($params['regionId'])) {
            $error = '区域编号不能为空';
            return false;
        }
        $params['slbName'] = trim($this->postParam('slbName', ''));
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
