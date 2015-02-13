<?
$asm=trim($vars["asm"]);
if(strlen($asm)){
	print Common::Info('','<pre style="background:black;font-size:9pt">'.$asm.'</pre>');
}else{
	print Common::Error("Invalid address","Unable to get the dissasembly :-|");
}
?>
