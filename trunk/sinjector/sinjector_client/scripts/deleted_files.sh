if [ ! -e $1 ] || [ "$1" == "" ]; then
	echo "Usage: $0 <sinjector_log.txt>"
	exit
fi
cat $1 |grep DeleteFile|cut -d ']' -f 3-|cut -b2-|sort|uniq
