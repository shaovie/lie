<?php
/**
 * @Author shaowei
 * @Date   2015-12-03
 */

namespace src\admin\controller;

use \src\common\BaseController;
use \src\common\Util;
use \src\common\Log;
use \src\common\Check;
use \src\common\Session;
use \src\common\Nosql;
use \src\admin\model\DomainPoolModel;

class DomainCheckController extends BaseController
{
    public function callbackapi()
    {
        $domain = $this->getParam('domain', '');
        $ret = (int)$this->postParam('ret', 0);
        if (empty($domain))
            return ;
        if ($ret == 2) {
            DomainPoolModel::update($domain, array('state' => 'killed', 'mtime' => CURRENT_TIME));
            Log::rinfo('domain ' . $domain . ' killed');
        } else {
            Log::rinfo('domain ' . $domain . ' not killed');
        }
        return ;
    }
}
