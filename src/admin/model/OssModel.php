<?php
/**
 * @Author shaowei
 * @Date   2015-11-30
 */

namespace src\admin\model;

use \src\common\DB;
use \src\common\Util;
use \src\common\Cache;

class OssModel
{
    public static function newOne(
        $name,
        $extranetDomain
    ) {
        if (empty($name)) {
            return false;
        }
        $data = array(
            'name' => $name,
            'extranet_domain' => $extranetDomain,
            'state' => 'unprocessing',
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('d_oss', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return (int)$ret;
    }
    public static function findName($name)
    {
        if (empty($name))
            return array();
        $ret = DB::getDB('r')->fetchOne(
            'd_oss',
            '*',
            array('name'), array($name),
            false
        );
        return $ret === false ? array() : $ret;
    }

    public static function fetchSome($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'd_oss',
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
            'd_oss',
            $cond, $vals,
            $rel
        );
        return $ret === false ? 0 : $ret;
    }

    public static function searchName($key, $page, $pageSize)
    {
        if (empty($key))
            return array();
        $page = $page > 0 ? $page - 1 : $page;
        $key = str_replace('_', '\_', $key);
        $key = str_replace('%', '\%', $key);

        $sql = 'select * from d_oss where name like '
            . "'%" . trim($key) . "%'"
            . ' order by id desc limit '
            . $pageSize * $page . ', ' . $pageSize;
        $ret = DB::getDB('r')->rawQuery($sql);
        return $ret === false ? array() : $ret;
    }
    public static function searchNameCount($key)
    {
        if (empty($key))
            return 0;
        $key = str_replace('_', '\_', $key);
        $key = str_replace('%', '\%', $key);

        $sql = 'select count(*) as c from d_oss where name like '
            . "'%" . trim($key) . "%'";
        $ret = DB::getDB('r')->rawQuery($sql);
        return $ret === false ? 0 : (int)$ret[0]['c'];
    }

    public static function update($domain, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'd_oss',
            $data,
            array('name'), array($domain),
            false,
            1
        );
        self::onUpdateData($domain);
        return $ret !== false;
    }
    public static function del($name)
    {
        if (empty($name))
            return false;
        $ret = DB::getDB('w')->delete(
            'd_oss',
            array('name'), array($name),
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

