<?
error_reporting(E_ALL);
ini_set("display_errors", 1);
$modules=Array("config","common","templates","website","sinjector","radare2","pefile","binary","mono","tcpdump","art");
foreach($modules as $m){
        include("modules/$m.php");
}
@ob_clean();
