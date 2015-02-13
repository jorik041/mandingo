<?
$md5=Common::getMD5("report");
$id=Common::getInteger("id");
$binary="uploads/$md5.bin";
$sections=Binary::sections($binary);
if($id>count($sections)) {
	print Website::Error("That section was not found...");
	return;
}
$b=new Binary($binary);
$bytes=$b->readBytesAtOffset($sections[$id-1]["prd"]["value"],$sections[$id-1]["srd"]["value"]);
print "<pre>";
$html=htmlentities($bytes,ENT_DISALLOWED,"iso-8859-1");
$html=preg_replace("/&#xFFFD;/"," ",$html);
print $html;
print "</pre>";
?>
