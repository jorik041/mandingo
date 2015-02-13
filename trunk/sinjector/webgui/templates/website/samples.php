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
							Sample
						</th>
						<th>
							Type
						</th>
						<th style="text-align:center">
							Art
						</th>
						<th>
							Analysis
						</th>
						<th>
							Status
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
foreach(Sinjector::Results() as $r){
	$i++;
?>

					<tr>
						<td>
							<?=$i?>
						</td>
						<td>
							<a href="?report=<?=$r["md5"]?>"><?=$r["md5"]?></a>
						</td>
						<td style="font-size:9pt">
							<?=$r["type"]?>
						</td>
						<td style="padding:1px;text-align:center" nowrap>
							<?if(isset($r["art"]["sections"])){?>
								<a href="?report=<?=$r["md5"]?>&op=results&action=dump_sec"><img src="<?=$r["art"]["sections"]?>" height=32></a>
							<?}?>
							<?if(isset($r["art"]["resources"])){?>
								<a href="?report=<?=$r["md5"]?>&op=results&action=dump_res"><img src="<?=$r["art"]["resources"]?>" height=32></a>
							<?}?>
							<?if(isset($r["art"]["r2_functions"])){?>
								<a href="?report=<?=$r["md5"]?>&op=tools&app=radare2/radare2&fun=aa;af;afl"><img src="<?=$r["art"]["r2_functions"]?>" height=32></a>
							<?}?>
						</td>
						<td nowrap>
							<?=$r["date"]?>
						</td>
						<td>
							<?=$r["status"]?>
						</td>
					</tr>
<?}?>
				</tbody>
			</table>
		</div>
	</div>

