basepath=$(cd `dirname $0`; pwd)

nohup sh ${basepath}/ali_asyn_dns.sh $1 >/dev/null 2>&1 &
nohup sh ${basepath}/gen_domain.sh $1 >/dev/null 2>&1 &
