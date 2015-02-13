if [ ! -e $1 ] || [ "$1" == "" ]; then
	echo "Usage: $0 <sinjector_results>"
	exit
fi
grep "\[LoadLibrary" $1/newlog.txt|cut -d']' -f 3|cut -b2-|tr a-z A-Z|sort -u
