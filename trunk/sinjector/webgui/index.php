<?
@session_start();

include("core.php");

$md5=Common::getMD5("report");
if(isset($_GET["clear_cache"])) session_unset();
if(isset($_GET["submit_art"])) Art::submit(Common::GetPost("md5"),Common::GetIntegerPost("type"),Common::GetPost("data"));
if(isset($_GET["contact"])) $html=Website::page("contact.html",array("md5"=>$md5));
if(isset($_GET["samples"])) $html=Website::page("samples.php",array("md5"=>$md5));
if(isset($_GET["analyze"])) $html=Website::page("analyze.php",array("md5"=>$md5));
if(isset($_GET["submit"]))  $html=Website::page("submit.php",array("md5"=>$md5));
if(isset($_GET["report"]))  $html=Website::page("report.php",array("md5"=>$md5));
if(isset($_GET["image"]))   $html=Templates::Load("templates/website/image.php",array("md5"=>$md5));
if(isset($_GET["icon"]))    $html=Templates::Load("templates/pefile/icon.php",array("md5"=>$md5));
if(isset($_GET["bitmap"]))  $html=Templates::Load("templates/pefile/bitmap.php",array("md5"=>$md5));

if(!isset($html)) $html=Website::page("index.html",array("md5"=>$md5));

print $html;
?>


