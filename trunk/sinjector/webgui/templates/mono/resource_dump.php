<?
$bytes=Mono::loadResource($vars["binary"],$vars["id"]);
print "<pre>";
$html=htmlentities($bytes,ENT_DISALLOWED,"iso-8859-1");
$html=preg_replace("/&#xFFFD;/"," ",$html);
print $html;
print "</pre>";

?>
