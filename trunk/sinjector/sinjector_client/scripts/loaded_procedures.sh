if [ ! -e $1 ] || [ "$1" == "" ]; then
	echo "Usage: $0 <sinjector_results>"
	exit
fi
grep "\[GetProcAddress" $1|cut -d']' -f 3-|cut -b 2-|sort -u
