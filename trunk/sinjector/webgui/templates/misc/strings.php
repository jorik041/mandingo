<?
$unicode_strings=trim(Binary::strings_unicode($vars["binary"]));
if(strlen($unicode_strings)){
?>
<h4>Unicode UTF-16le Strings</h4>
<pre>
<?=htmlentities($unicode_strings,ENT_QUOTES,"iso-8859-1");?>
</pre>
<?}?>
<h4>ASCII Strings</h4>
<pre>
<?=htmlentities(Binary::strings($vars["binary"]),ENT_QUOTES,"iso-8859-1");?>
</pre>
