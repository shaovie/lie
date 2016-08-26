<?php
/**
 * @Author shaowei
 * @Date   2015-11-30
 */

namespace src\admin\model;

use \src\common\DB;
use \src\common\Util;
use \src\common\Cache;

class DomainPoolModel
{
    public static function newOne(
        $domain,
        $type,
        $state
    ) {
        if (empty($domain) || empty($type)) {
            return false;
        }
        $data = array(
            'domain' => $domain,
            'domain_type' => $type,
            'state' => $state,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('d_domain_pool', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return (int)$ret;
    }

    public static function fetchSome($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'd_domain_pool',
            '*',
            $conds, $vals,
            $rel,
            array('id'), array('desc'),
            array($page * $pageSize, $pageSize)
        );

        return $ret === false ? array() : $ret;
    }

    public static function fetchCount($cond, $vals, $rel)
    {
        $ret = DB::getDB('r')->fetchCount(
            'd_domain_pool',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }

    public static function searchDomain($key, $page, $pageSize)
    {
        if (empty($key))
            return array();
        $page = $page > 0 ? $page - 1 : $page;
        if ($key == 'A' || $key == 'B') {
            $sql = "select * from d_domain_pool where domain_type='" . $key
                . "' order by id desc limit "
                . $pageSize * $page . ', ' . $pageSize;
        } else {
            $key = str_replace('_', '\_', $key);
            $key = str_replace('%', '\%', $key);

            $sql = 'select * from d_domain_pool where domain like '
                . "'%" . trim($key) . "%'"
                . ' order by id desc limit '
                . $pageSize * $page . ', ' . $pageSize;
        }
        $ret = DB::getDB('r')->rawQuery($sql);
        return $ret === false ? array() : $ret;
    }
    public static function searchDomainCount($key)
    {
        if (empty($key))
            return 0;

        $page = $page > 0 ? $page - 1 : $page;
        if ($key == 'A' || $key == 'B') {
            $sql = "select count(*) as c from d_domain_pool where domain_type='" . $key . "'";
        } else {
            $key = str_replace('_', '\_', $key);
            $key = str_replace('%', '\%', $key);

            $sql = 'select count(*) as c from d_domain_pool where domain like '
                . "'%" . trim($key) . "%'";
        }
        $ret = DB::getDB('r')->rawQuery($sql);
        return $ret === false ? 0 : (int)$ret[0]['c'];
    }

    public static function update($domain, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'd_domain_pool',
            $data,
            array('domain'), array($domain),
            array(),
            1
        );
        self::onUpdateData($domain);
        return $ret !== false;
    }
    public static function del($id)
    {
        if ((int)$id <= 0)
            return false;
        $ret = DB::getDB('w')->delete(
            'd_domain_pool',
            array('id'), array($id),
            false,
            1
        );
        return $ret === false ? false : true;
    }
    public static function getStateDesc($st)
    {
        return $st;
    }

    private static function onUpdateData($domain)
    {
    }
}

