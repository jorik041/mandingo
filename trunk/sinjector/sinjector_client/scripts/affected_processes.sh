if [ ! -e $1 ] || [ "$1" == "" ]; then
	echo "Usage: $0 <sinjector_log.txt>"
	exit
fi
cut -d '=' -f2|cut -d' ' -f 1|sort -u > /tmp/afp.log
cat $1 |grep Write|cut -d'=' -f 2|cut -d' ' -f 1|sort -u >> /tmp/afp.log
cat /tmp/afp.log|sort -u
rm /tmp/afp.log
