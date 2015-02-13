<?
// tr class= active, success, warning, danger
$handle=Common::getInteger("handle");
$procedures=array();
if(strlen($handle)) $procedures=Sinjector::procedures($vars["md5"],$handle);
$hooks=Sinjector::hooks();
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
							Name
						</th>
						<th>
							Procs
						</th>
						<th>
							Handle
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
foreach($vars["libraries"] as $r){
	$proc_count=count(Sinjector::procedures($vars["md5"],$r["handle"]));
	//if(!$proc_count && intval($r["handle"])) continue; //skip libraries with no loaded procs
	$i++;
	#TODO: add this to sinjector class
	$class="";
	if(!intval($r["handle"])) $class=" class=warning title='This library was not found on the guest system'";
	if(!$proc_count && intval($r["handle"])) $class=" class=active title='Library found but no procedures were dynamically loaded'";
?>
					<tr<?=$class?>>
						<td>
<a name="<?=$i?>"></a>
							<?=$i?>
						</td>
						<?
						?>
						<td>
							<?=$r["name"]?>
							<?if($handle==intval($r["handle"])&& count($procedures)){
								print "<br>";
								for($n=0;$n<count($procedures);$n++){
									print "<a href='https://social.msdn.microsoft.com/Search/en-US/windows/desktop?query=".$procedures[$n]."' target=_new title='Show MSDN info'>";
									print $procedures[$n];
									print "</a>";
								 	$msg="";
									if(in_array($procedures[$n],$hooks)) $msg=" <font size=1>Hooked</font>";
									print $msg;
									if($n<count($procedures)-1) print ", ";
								}
							}?>
						</td>
						<td>
							<?
								//$proc_count=count(Sinjector::procedures($vars["md5"],$r["handle"]));
								print $proc_count;
							?>
						</td>
						<td>
							<?if($proc_count){?>
								<a href="?report=<?=$vars["md5"]?>&op=results&action=sandbox/libraries&handle=<?=$r["handle"]?>#<?=$i?>" title="Loaded procedures"><?=$r["handle"]?></a>
							<?}else{?>
								<?=$r["handle"]?>
							<?}?>
						</td>
					</tr>
<?}?>
				</tbody>
			</table>
		</div>
	</div>

