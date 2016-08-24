<?php
/**
 * @Author shaowei
 * @Date   2015-11-30
 */

namespace src\admin\model;

use \src\common\DB;
use \src\common\Util;
use \src\common\Cache;

class DnsModel
{
    public static function newOne(
        $recordId,
        $domain,
        $rr,
        $type,
        $value,
        $state
    ) {
        if (empty($domain) || empty($rr)) {
            return false;
        }
        $data = array(
            'record_id' => $recordId,
            'domain' => $domain,
            'rr' => $rr,
            'record_type' => $type,
            'value' => $value,
            'state' => $state,
            'ctime' => CURRENT_TIME,
        );
        $ret = DB::getDB('w')->insertOne('d_dns', $data);
        if ($ret === false || (int)$ret <= 0) {
            return false;
        }
        return (int)$ret;
    }
    public static function findDnsRecordById($id)
    {
        if (empty($id))
            return array();
        $ret = DB::getDB('r')->fetchOne(
            'd_dns',
            '*',
            array('id'), array($id),
            array()
        );
        return $ret === false ? array() : $ret;
    }

    public static function fetchSome($conds, $vals, $rel, $page, $pageSize)
    {
        $page = $page > 0 ? $page - 1 : $page;

        $ret = DB::getDB('r')->fetchSome(
            'd_dns',
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
            'd_dns',
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
        $key = str_replace('_', '\_', $key);
        $key = str_replace('%', '\%', $key);

        $sql = 'select * from d_dns where domain like '
            . "'%" . trim($key) . "%'"
            . ' order by id desc limit '
            . $pageSize * $page . ', ' . $pageSize;
        $ret = DB::getDB('r')->rawQuery($sql);
        return $ret === false ? array() : $ret;
    }
    public static function searchDomainCount($key)
    {
        if (empty($key))
            return 0;
        $key = str_replace('_', '\_', $key);
        $key = str_replace('%', '\%', $key);

        $sql = 'select count(*) as c from d_dns where domain like '
            . "'%" . trim($key) . "%'";
        $ret = DB::getDB('r')->rawQuery($sql);
        return $ret === false ? 0 : (int)$ret[0]['c'];
    }

    public static function update($domain, $rr, $data)
    {
        if (empty($data)) {
            return true;
        }
        $ret = DB::getDB('w')->update(
            'd_dns',
            $data,
            array('domain', 'rr'), array($domain, $rr),
            array('and'),
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
            'd_dns',
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

