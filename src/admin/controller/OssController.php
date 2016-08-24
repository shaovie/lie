<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\Log;
use \src\admin\model\OssModel;

require_once ROOT_PATH . '/aliyun-openapi-php-sdk/aliyun-oss-php-sdk/autoload.php';
use OSS\OssClient;
use OSS\Core\OssException;

class OssController extends AdminController
{
    const ONE_PAGE_SIZE = 20;
    const REGION = 'oss-cn-beijing.aliyuncs.com';

    public function listPage()
    {
        $page = $this->getParam('page', 1);

        $totalNum = OssModel::fetchCount([], [], []);
        $dataList = OssModel::fetchSome([], [], [], $page, self::ONE_PAGE_SIZE);

        $dataList = $this->fillDataList($dataList);

        $searchParams = [];
        $error = '';
        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Oss/listPage',
            $searchParams
        );
        $data = array(
            'dataList' => $dataList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display("oss_list", $data);
    }
    public function search()
    {
        $dataList = array();
        $totalNum = 0;
        $error = '';
        $searchParams = array();
        do {
            $page = $this->getParam('page', 1);
            $searchName = trim($this->getParam('searchName', ''));
            if (empty($searchName)) {
                header('Location: /admin/Oss/listPage');
                return ;
            }
            if (!empty($searchName)) {
                $searchParams['searchName'] = $searchName;
                $ret = OssModel::searchName($searchName, $page, self::ONE_PAGE_SIZE);
                if (!empty($ret)) {
                    $dataList = $ret;
                    $totalNum = OssModel::searchNameCount($searchName);
                }
            }
        } while(false);

        $dataList = $this->fillDataList($dataList);

        $pageHtml = $this->pagination(
            $totalNum,
            $page,
            self::ONE_PAGE_SIZE,
            '/admin/Oss/search',
            $searchParams
        );
        $data = array(
            'dataList' => $dataList,
            'totalNum' => $totalNum,
            'pageHtml' => $pageHtml,
            'search' => $searchParams,
            'error' => $error
        );
        $this->display('oss_list', $data);
    }
    public function addPage()
    {
        $data = array(
            'title' => '添加Bucket',
            'action' => '/admin/Oss/add',
        );
        $this->display('oss_add', $data);
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
        for ($i = 1; $i <= $params['amount']; $i++) {
            $ret = OssModel::newOne(
                $params['name'] . $i,
                $params['name'] . $i . '.' . self::REGION
            );
            if ($ret === false) {
                $this->ajaxReturn(0, '保存数据失败', '');
                return ;
            }
            $ossClient = new OssClient(ACCESS_KEY_ID, ACCESS_KEY_SECRET, self::REGION);
            try {
                $ossClient->createBucket($params['name'] . $i, OssClient::OSS_ACL_TYPE_PUBLIC_READ);
                OssModel::update(
                    $params['name'] . $i,
                    array(
                        'state' => 'ok',
                        'remark' => 'ok',
                    )
                );
            } catch (OssException $e) {
                OssModel::update(
                    $params['name'] . $i,
                    array(
                        'state' => 'error',
                        'remark' => $e->getMessage(),
                    )
                );
            }
        }
        $this->ajaxReturn(0, '提交任务成功!', '/admin/Oss/listPage');
    }
    public function del()
    {
        $name = $this->getParam('name', '');
        if (empty($name)) {
            header('Location: /admin/Oss/listPage');
            return ;
        }
        exit();
        $name = 'wxshare1';
        $ossClient = new OssClient(ACCESS_KEY_ID, ACCESS_KEY_SECRET, self::REGION);
        try {
            $response = $ossClient->listObjects($name, array('prefix' => 'Public/'));
            $prefixList = $response->getPrefixList();
            foreach ($prefixList as $item) {
                var_dump($item->getPrefix());
                //$ossClient->deleteObject($name, $item->getPrefix());
            }
            $objectList = $response->getObjectList();
            foreach ($objectList as $item) {
                $item = $item->getKey();
                var_dump($item);
            }
            exit();
            //$ossClient->deleteBucket($name);
        } catch (OssException $e) {
            echo "<script>alert('" . $e->getMessage() . "');window.location.href='/admin/Oss/listPage'</script>";
            return ;
        }
        OssModel::del($name);
        header('Location: /admin/Oss/listPage');
    }
    private function fetchFormParams(&$params, &$error)
    {
        $params['amount'] = intval($this->postParam('amount', 0));
        if ($params['amount'] > 10) {
            $error = '数量不能超过10';
            return false;
        }
        $params['name'] = trim($this->postParam('name', ''));
        if (empty($params['name'])) {
            $error = '名称不能为空';
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
