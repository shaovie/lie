<?php
/**
 * @Author shaowei
 * @Date   2016-05-10
 */

namespace src\admin\controller;


class HomeController extends AdminController
{
    public function index()
    {
        if ($_SERVER['HTTP_HOST'] != HT_HOST) {
            echo '拒绝访问';
            exit();
        }

        $data = array(
            'iframe' => '/admin/Dns/listPage',
            'account' => $this->account,
        );
        $this->display('framwork', $data);
    }
}
