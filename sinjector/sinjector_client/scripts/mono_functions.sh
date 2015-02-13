if [ ! -e $1 ] || [ "$1" == "" ]; then
	echo "Usage: $0 <sinjector_results>"
	exit
fi

monodis $1 |grep "method line" -A 4|egrep "^\s+default"|sed -e "s/.*default //"|sed -e "s/  cil managed //"
