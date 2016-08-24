<?php
/**
 * @Author shaowei
 * @Date   2015-09-17
 * 
 * 注意：优先使用const定义常量
 *       const是语言结构 define是函数，const编译比define快很多
 */

//= defines
define('EDITION',               isset($_SERVER['EDITION']) ? $_SERVER['EDITION'] : 'online');
define('ROOT_PATH',             realpath(__DIR__ . '/../'));
define('SRC_PATH',              ROOT_PATH . '/src');
define('LOG_DIR',               ROOT_PATH . '/logs');
define('LIBS_DIR',              ROOT_PATH . '/libs');
define('CONFIG_PATH',           ROOT_PATH . '/config');
define('PUBLIC_PATH',           ROOT_PATH . '/public');
define('JS_CSS_CONFIG_PATH',    EDITION == 'test' ? ROOT_PATH . '/config/js_css_test' : ROOT_PATH . '/config/js_css');

define('CURRENT_TIME',          $_SERVER['REQUEST_TIME']); // 不敏感的时间可以取这个值
define('APP_HOST',              $_SERVER['APP_HOST']);
define('HT_HOST',               $_SERVER['HT_HOST']);

//= for cookie
define('COOKIE_PREFIX',         $_SERVER['COOKIE_PREFIX']);

define('APP_URL_BASE',          'http://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : APP_HOST));

//= for mysql
// dsn=mysql:host=127.0.0.1;port=3306;dbname=user;charset=utf8
define('DB_R_DSN',              $_SERVER['DB_R_DSN']);
define('DB_R_USER',             $_SERVER['DB_R_USER']);
define('DB_R_PASSWD',           $_SERVER['DB_R_PASSWD']);

define('DB_W_DSN',              $_SERVER['DB_W_DSN']);
define('DB_W_USER',             $_SERVER['DB_W_USER']);
define('DB_W_PASSWD',           $_SERVER['DB_W_PASSWD']);

//= for redis
define('CACHE_PREFIX',          $_SERVER['CACHE_PREFIX']);
define('REDIS_CACHE_HOST',      $_SERVER['REDIS_CACHE_HOST']);
define('REDIS_CACHE_PORT',      $_SERVER['REDIS_CACHE_PORT']);
define('NOSQL_PREFIX',          $_SERVER['NOSQL_PREFIX']);
define('REDIS_NOSQL_HOST',      $_SERVER['REDIS_NOSQL_HOST']);
define('REDIS_NOSQL_PORT',      $_SERVER['REDIS_NOSQL_PORT']);

define('ACCESS_KEY_ID',         $_SERVER['ACCESS_KEY_ID']);
define('ACCESS_KEY_SECRET',     $_SERVER['ACCESS_KEY_SECRET']);

//= error code
const ERR_PARAMS_ERROR          = 101; // 参数错误
const ERR_OPT_FREQ_LIMIT        = 102; // 操作频率受限
const ERR_PASSWD_ERROR          = 103; // 密码错误
const ERR_SYSTEM_ERROR          = 104; // 系统错误
const ERR_SYSTEM_BUSY           = 105; // 系统繁忙
const ERR_OPT_FAIL              = 106; // 操作失败
const ERR_NOT_LOGIN             = 107; // 未登录或未注册

const ASSETS_VERSION            = 3;

