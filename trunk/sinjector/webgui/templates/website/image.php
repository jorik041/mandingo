<?
$md5=Common::getMD5("report");
$image=Common::getString("image");
$image=preg_replace("/[\.\/]/","",$image);
$path=Config::$sinjector_path."/results/".$md5."/screenshot-".$image.".png";
@ob_clean();
if(!file_exists($path)) {
	$path="img/computers.jpg";
	header("Content-Type: image/jpg");
}else{
	header("Content-Type: image/png");
}
print file_get_contents($path);
exit();
?>
