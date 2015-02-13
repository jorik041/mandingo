<?
$handle="0x".dechex(hexdec(Common::getString("handle")));
$pid=Common::getInteger("pid");
// tr class= active, success, warning, danger
?>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>
							#
						</th>
						<th>
							Pid
						</th>
						<th title="Mode (Create or Set)">
							M
						</th>
						<th>
							Type
						</th>
						<th title="Handle">
							Hndl
						</th>
						<th>
							Key
						</th>
						<th>
							Value
						</th>
						<?if($vars["mode"]!="query"){?>
							<th>
								Ret
							</th>
						<?}?>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
foreach($vars["registry"] as $r){
	$i++;
?>

					<tr>
						<td>
							<a name="<?=$i?>"></a><?=$i?>
						</td>
						<td>
							<a href="?report=<?=$vars["md5"]?>&op=results&action=sandbox/processes&pid=<?=$r["pid"]?>#res"><?=$r["pid"]?></a>
						</td>
						<td>
							<?=$r["class"]?>
						</td>
						<td>
							<?=strtolower($r["type"])?>
						</td>
						<td>
						<?if(strlen($r["handle"])){?>
							<?if($r["handle"]==$handle){?> 
								<a href="#<?=$i?>" onClick='$("#modal").modal()'><?=$handle?></a>
							<?}else{
								if($vars["mode"]=="set"){
								?>
								<a href="?report=<?=$vars["md5"]?>&op=results&action=sandbox/registry&pid=<?=$r["pid"]?>&handle=<?=$r["handle"]?>#<?=$i?>"><?=$r["handle"]?></a>
								<?}else{?>
								<a href="?report=<?=$vars["md5"]?>&op=results&action=sandbox/registry_queries&pid=<?=$r["pid"]?>&handle=<?=$r["handle"]?>#<?=$i?>"><?=$r["handle"]?></a>
								<?}?>
							<?}?>
						<?}else{?>
							<?=$r["handle"]?>
						<?}?>
						</td>
						<?
						#TODO: add this warnings to registry parser (intelligence), not here
						$class="";
						if(strstr(strtolower($r["key"]),"software\\microsoft\\windows\\currentversion\\run")) $class=" class=danger";	
						if(strstr(strtolower($r["key"]),"software\\microsoft\\windows\\currentversion\\uninstall")) $class=" class=warning";	
						if(strstr(strtolower($r["key"]),"software\\microsoft\systemcertificates")) $class=" class=danger";	
						if(strstr(strtolower($r["key"]),"enablefirewall") && $r["value"]=="0") $class=" class=danger";	
						if(strstr(strtolower($r["key"]),"userinit") && strpos($r["value"],".exe") && $vars["mode"]!="query") $class=" class=danger";	
						?>
						<td<?=$class?>>
							<?
							$key=$r["key"];
							if(strlen($key)>50) $key=substr($key,0,24)."...".substr($key,strlen($key)-23);
							?>
							<?=$key?>
						</td>
						<?
						#TODO: add this warnings to registry parser, not here
						$class="";
						if(strpos($r["value"],".exe") && $vars["mode"]!="query"){
							$class=" class=warning";	
							if(strpos(strtolower($r["value"]),"application data") || strpos(strtolower($r["value"]),"documents and settings")) $class=" class=danger";
							if(strstr(strtolower($r["key"]),"userinit")) $class=" class=danger";	
						}
						?>
						<td<?=$class?>>
							<?
							$value=$r["value"];
							if(strlen($value)>32) $value=substr($value,0,16)."...".substr($value,strlen($value)-24,24);
							$plain="";							
							if($r["type"]=="BINARY") {
								$plain="<br><font color=#aaa>\"";
								for($n=0;$n<strlen($r["value"]);$n+=2){
									$val=hexdec(substr($r["value"],$n,2));
									if(ctype_print(chr($val))) $plain.=chr($val);
								}
								$plain.="\"</font>";
							}
							?>
							<?=$value?><?=$plain?>
						</td>
						<?if($vars["mode"]!="query"){?>
							<td>
								<?=$r["ret"]?>
							</td>
						<?}?>
					</tr>
<?}?>
				</tbody>
			</table>
		</div>
	</div>
<?
if(strlen($handle) && strlen($pid)){
?>
<div class="modal fade" id="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:85%">
		<div class="modal-content">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="myModalLabel">
					Search for handle <?=$handle?>
				</h4>
			</div>
			<div class="modal-body">
<?
	print "<pre>";
	if($vars["mode"]=="set"){
		print Sinjector::dump_registry($vars["md5"],$handle,$pid);
	}else{
		print Sinjector::dump_registry_queries($vars["md5"],$handle,$pid);
	}
	print "</pre>";

?>
			</div>
			<div class="modal-footer">
				 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
		
	</div>
</div>
<script>
$("#modal").modal();
</script>
<?
}
?>
