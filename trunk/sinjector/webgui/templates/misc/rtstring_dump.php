<?
$md5=Common::getMD5("report");
$id=Common::getInteger("rtstring");
$binary="uploads/$md5.bin";
$resources=Binary::resources($binary);
$c=0;$start=0;$size=0;
foreach($resources as $r){
	if($r["type"]["value"]!="RT_STRING") continue;
	$c++;
	if($c==$id){
		$start=$r["fo"]["value"];
		$size=$r["size"]["value"];
	}
}
if(!$start && !$size) {
	print Common::Error("Error","That rt_string resource was not found...");
	return;
}
if($start[0]=="-") {
	print Common::Error("Error","The rt_stirng information is invalid: Negative offset position.");
	return;
}
if($size[0]=="-") {
	print Common::Error("Error","The rt_string information is invalid: Negative size.");
	return;
}
$b=new Binary($binary);
$bytes=$b->readBytesAtOffset($start,$size);
print "<pre>";
$html=htmlentities($bytes,ENT_DISALLOWED,"iso-8859-1");
$html=preg_replace("/&#xFFFD;/"," ",$html);
print $html;
print "</pre>";
?>
