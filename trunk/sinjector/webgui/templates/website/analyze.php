<?
ob_clean();
print Templates::Load("templates/website/header.php",array(""));
?>
<!--
<html>
<head>
<style>
body{
	font-family:courier-new;
	color:lime;
	background:black;
}
</style>
</head>
-->
<font size=+2>
<?
$md5=Common::getMD5("analyze");
if(!file_exists("uploads/$md5.bin")){
	print "ERROR - The sample '".htmlentities($md5)."' was not found";
	return;
}
?>
Analyzing sample <a href="?report=<?=$md5?>"><?=$md5?></a> (it will run during 1 minute, please wait...)
</font>
<?
$sample=getcwd()."/uploads/$md5.bin";
$cmd=Sinjector::analyze($sample);
while (@ ob_end_flush()); // end all output buffers if any

$proc = popen($cmd, 'r');
echo '<pre>';
while (!feof($proc))
{
    $res=fread($proc, 128);
	$res=preg_replace("/\033\[1m/","<b>",$res);
	$res=preg_replace("/\033\[0m/","</b>",$res);
	echo $res;
    @ flush();
}
echo '</pre>';
exit();
?>

