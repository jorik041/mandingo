<?
$md5=Common::getMD5("report");
$icon=Common::getInteger("icon");
$binary="uploads/$md5.bin";
@ob_clean();
if(!file_exists($binary)) {
	$path="img/computers.jpg";
	header("Content-Type: image/jpg");
	print file_get_contents($path);
}else{
	$cache_icon="cache/$md5"."_icon_".$icon.".bmp";
	if(!file_exists($cache_icon)){
		$count=0;
		$offset=0;$size=0;
		$b=new Binary($binary);
		$html=Pefile::call("res_offsets",$binary);
		preg_match_all("/RT_ICON (.+)/",$html,$i);
		if($icon>count($i[1])) die;
		foreach($i[1] as $icn){
			$count++;		
			if($count==$icon) {
				list($va,$offset_d,$size,$offset)=preg_split("/\s/",$icn);
				break;
			}
		}
		list($width,$height)=getIconWH($b,$html,$icon);
	//print "width: ".ord($width)." height: ".ord($height);
		$out=$b->headerOf("RT_ICON",$width,$height);
		$out.=$b->readBytesAtOffset($offset,$size);
	}else{
		$out=file_get_contents($cache_icon);
	}
	header("Content-Type: image/x-icon");
	print $out;
	file_put_contents($cache_icon,$out);
}
exit();

function getIconWH($b,$html,$icon){
	$w="\x20";
	$h="\x20";
	preg_match("/RT_GROUP_ICON (.+)/",$html,$i);
	list($va,$offset_d,$size,$offset)=preg_split("/\s/",$i[1]);
	$bytes=$b->readBytesAtOffset($offset,$size);
	$offset_w=6+($icon-1)*14;
	$offset_h=7+($icon-1)*14;
	return array(substr($bytes,$offset_w,1),	substr($bytes,$offset_h,1));
	/*
	print "<br>reserved: ";
	for($i=0;$i<2;$i++) printf("%02x ",ord(substr($bytes,$i,1)));
	print "<br>resourcetype: ";
	for($i=2;$i<4;$i++) printf("%02x ",ord(substr($bytes,$i,1)));
	print "<br>imagecount: ";
	for($i=4;$i<6;$i++) printf("%02x ",ord(substr($bytes,$i,1)));
	for($n=0;$n<$count;$n++){
		print "<br>width: ";
		for($i=6;$i<7;$i++) printf("%02x ",ord(substr($bytes,$i+$n*14,1)));
		print "<br>height: ";
		for($i=7;$i<8;$i++) printf("%02x ",ord(substr($bytes,$i+$n*14,1)));
		print "<br>colors:";
		for($i=8;$i<9;$i++) printf("%02x ",ord(substr($bytes,$i+$n*14,1)));
		print "<br>reserved:";
		for($i=9;$i<10;$i++) printf("%02x ",ord(substr($bytes,$i+$n*14,1)));
		print "<br>planes:";
		for($i=10;$i<12;$i++) printf("%02x ",ord(substr($bytes,$i+$n*14,1)));
		print "<br>bitsperpixel:";
		for($i=12;$i<14;$i++) printf("%02x ",ord(substr($bytes,$i+$n*14,1)));
		print "<br>imagesize:";
		for($i=14;$i<18;$i++) printf("%02x ",ord(substr($bytes,$i+$n*14,1)));
		print "<br>resourceid:";
		for($i=18;$i<22;$i++) printf("%02x ",ord(substr($bytes,$i+$n*14,1)));
		print "<hr>";
	}
	print "<br>";
	for($i=22;$i<$size;$i++) printf("%02x ",ord(substr($bytes,$i,1)));*/
	return array($w,$h);
}
?>
