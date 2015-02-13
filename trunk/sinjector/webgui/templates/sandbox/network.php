<?
// tr class= active, success, warning, danger
$id=Common::getInteger("id");
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
							Prot
						</th>
						<th>
							Src
						</th>
						<th>
							sPort
						</th>
						<th>
							Dst
						</th>
						<th>
							dPort
						</th>
						<th style=text-align:right>
							Len
						</th>
						<th>
							Host
						</th>
						<th>
							Data
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
$pname=array(80=>"http",443=>"https",53=>"domain","137"=>"netbios",138=>"netbios");
$bg_colors=array(80=>"5f5",443=>"5f5",137=>"ff0",138=>"ff0","53"=>"0ff");
foreach($vars["packets"] as $r){
	$i++;
	$fgcolor="";$bgcolor="";
	if($r["len"]=="0") $fgcolor="888";
	if(isset($bg_colors[$r["sPort"]])) $bgcolor=$bg_colors[$r["sPort"]];
	if(isset($bg_colors[$r["dPort"]])) $bgcolor=$bg_colors[$r["dPort"]];
?>

					<tr style='<?=(strlen($fgcolor)?"color:#$fgcolor;":"")?><?=(strlen($bgcolor)?"background:#$bgcolor;":"")?>'>
						<td>
							<a name=<?=$i?>></a><?=$i?>
						</td>
						<td>
							<?=$r["proto"]?>
						</td>
						<td>
							<?=$r["src"]?>
						</td>
						<td>
							<?if(isset($pname[$r["sPort"]])){?>
								<?=$pname[$r["sPort"]]?>
							<?}else{?>
								<?=$r["sPort"]?>
							<?}?>
						</td>
						<td>
							<?=$r["dst"]?>
						</td>
						<td>
							<?if(isset($pname[$r["dPort"]])){?>
								<?=$pname[$r["dPort"]]?>
							<?}else{?>
								<?=$r["dPort"]?>
							<?}?>
						</td>
						<td align=right>
							<a href="?report=<?=$vars["md5"]?>&op=results&action=sandbox/network&id=<?=$i?>#<?=$i?>"><?=number_format($r["len"])?></a>
						</td>
						<td>
							<?=$r["host"]?>
						</td>
						<td>
							<?
							$data=$r["data"];
							if(strlen($data)>31) $data=substr($data,0,16)."...".substr($data,strlen($data)-12);
							?>
							<?=$data?>
						</td>
					</tr>
					<?
					if($id!="" && $id==$i){
						$data=Tcpdump::readPacket($vars["md5"],$id);
						if(strlen($data)){
							print "</table>";
							print "<pre>";
							$data=htmlentities($data,ENT_DISALLOWED,"iso-8859-1");
							$data=preg_replace("/&#xFFFD;/"," ",$data);
							print $data;
							print "</pre>";
							?>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>
							#
						</th>
						<th>
							Prot
						</th>
						<th>
							Src
						</th>
						<th>
							sPort
						</th>
						<th>
							Dst
						</th>
						<th>
							dPort
						</th>
						<th style=text-align:right>
							Len
						</th>
						<th>
							Host
						</th>
						<th>
							Data
						</th>
					</tr>
				</thead>
				<tbody>
						<?
						}
					}
					?>
<?}?>
				</tbody>
			</table>
		</div>
	</div>

