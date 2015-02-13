<?
$md5=Common::getMD5("report");
$bitmap=Common::getInteger("bitmap");
$binary="uploads/$md5.bin";
@ob_clean();
if(!file_exists($binary)) {
	$path="img/computers.jpg";
	header("Content-Type: image/jpg");
	print file_get_contents($path);
}else{
	$cache_bitmap="cache/$md5"."_bitmap_".$bitmap.".bmp";
	if(!file_exists($cache_bitmap)){
		$count=0;
		$offset=0;$size=0;
		$b=new Binary($binary);
		$html=Pefile::call("res_offsets",$binary);
		preg_match_all("/RT_BITMAP (.+)/",$html,$i);
		if($bitmap>count($i[1])) die;
		foreach($i[1] as $bmp){
			$count++;		
			if($count==$bitmap) {
				list($va,$offset_d,$size,$offset)=preg_split("/\s/",$bmp);
				break;
			}
		}
		$header=$b->headerOf("RT_BITMAP");
		$data=$b->readBytesAtOffset($offset,$size);
		$out="";
		if($data[0]!="B" && $data[1]!="M") 
			$out=$header.$data;
		else 
			$out=$data;
	}else{
		$out=file_get_contents($cache_bitmap);
	}
	header("Content-Type: image/bmp");
	print $out;
	file_put_contents($cache_bitmap,$out);
}
exit();
?>
