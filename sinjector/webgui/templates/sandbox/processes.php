<?
// tr class= active, success, warning, danger
$pid=Common::GetInteger("pid");
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
						<th>
							<center>Action</center>
						</th>
						<th>
							<center>Status</center>
						</th>
						<th>
							Name
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
$lastpid="";
$lastaction="";
$lastname="";
$processDumped=false;
foreach($vars["processes"] as $r){
	$i++;
?>

				<?if($r["action"]=="rthread" || $r["action"]=="written"){?>
					<?if($r["msg"]=="running" || $r["msg"]=="other"){?>
						<tr class=danger>
					<?}elseif($r["msg"]!="unknown"){?>
						<tr class=warning>
					<?}else{?>
						<tr>
					<?}?>
				<?}else{?>
					<tr>
				<?}?>
						<td>
							<?=$i?>
						</td>
						<td>
							<?if($r["pid"]==$lastpid){?>
							<?}else{
								$lastaction="";
								$lastname="";
								?>
								<?if($pid==$r["pid"]){?>
									<b><?=$r["pid"]?></b>
								<?}else{?>
									<a href="?report=<?=$vars["md5"]?>&op=results&action=sandbox/processes&pid=<?=$r["pid"]?>#res"><?=$r["pid"]?></a>
								<?}?>
							<?}
							$lastpid=$r["pid"];
							?>
						</td>
						<td align=center>
							<?if($r["action"]==$lastaction){?>
							<?}else{?>
								<?=$r["action"]?>
							<?}
							$lastaction=$r["action"];
							?>
						</td>
						<td align=center>
							<?=$r["msg"]?>
						</td>
						<td>
							<?if($r["name"]==$lastname){?>
							<?}else{?>
								<?=$r["name"]?>
							<?}
							$lastname=$r["name"];
							?>
						</td>
					</tr>
					<?
					if(strlen($pid) && $pid==$r["pid"] && !$processDumped) {
						print "</table>";
						print Templates::Load("templates/sandbox/process_pid.php",array_merge($vars,array("pid"=>$pid)));
						$processDumped=true;
						if($i<count($vars["processes"])){
?>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>
							#
						</th>
						<th>
							Pid
						</th>
						<th>
							<center>Action</center>
						</th>
						<th>
							<center>Status</center>
						</th>
						<th>
							Name
						</th>
					</tr>
				</thead>
				<tbody>
<?
					}}
					?>
<?}?>
				</tbody>
			</table>
		</div>
	</div>
