#!/bin/sh

ROOT_PATH=/opt
SERVER=$1
CMD="${ROOT_PATH}/php/bin/php -c ${ROOT_PATH}/php/etc/php.ini /data/htdocs/${SERVER}/public/cli.php ${ROOT_PATH}/nginx/conf/params/${SERVER}.params"
API=AliDns

echo $$ > /data/run/ali_asyn_dns.pid

while true
do
    $CMD "/job/$API/doit"
    sleep 2
done
