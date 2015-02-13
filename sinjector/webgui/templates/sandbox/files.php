<?
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
							Action
						</th>
						<th>
							Filename
						</th>
						<th>
							Pids
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
foreach($vars["files"] as $r){
	$i++;
?>

					<tr>
						<td>
							<?=$i?>
						</td>
						<?if($r["action"]=="created"){?>
							<td class=success>
						<?}else{?>
							<td class=active>
						<?}?>
							<?=$r["action"]?>
						</td>
						<td class="<?=$r["intelligence"]["class"]?>">
							<?=$r["filename"]?>
						</td>
						<td>
							<?foreach($r["pids"] as $p){?>
								<a href="?report=<?=$vars["md5"]?>&op=results&action=sandbox/processes&pid=<?=$p?>#res"><?=$p?></a>
							<?}?>
						</td>
					</tr>
<?}?>
				</tbody>
			</table>
		</div>
	</div>

