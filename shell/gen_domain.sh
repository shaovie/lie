#!/bin/sh

ROOT_PATH=/opt
SERVER=$1
CMD="${ROOT_PATH}/php/bin/php -c ${ROOT_PATH}/php/etc/php.ini /data/htdocs/${SERVER}/public/cli.php ${ROOT_PATH}/nginx/conf/params/${SERVER}.params"
API=AliDns

echo $$ > /data/run/gen_domain.pid

while true
do
    $CMD "/job/$API/genDomain"
    sleep 3
done
