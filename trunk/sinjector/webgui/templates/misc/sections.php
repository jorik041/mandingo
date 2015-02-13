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
							Name
						</th>
						<th>
							Entry
						</th>
						<th>
							ptrRawD
						</th>
						<th>
							SizeRawD
						</th>
						<th style="text-align:right">
							fsize%
						</th>
						<th>
							VAddr
						</th>
						<th>
							Flags
						</th>
						<th>
							Entropy
						</th>
						<th>
							FSE
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
$md5=$vars["md5"];
$binary=$vars["binary"];
foreach($vars["sections"] as $r){
	$i++;
?>

					<tr>
						<td>
							<?=$i?>
						</td>
						<td>
							<?if(strlen($r["prd"]["intelligence"]["class"])){?>
								<?=$r["name"]?>
							<?}else{?>
								<a href="?report=<?=$md5?>&op=results&action=dump_sec&id=<?=$i?>" title="Show RAW Dump of this section"><?=$r["name"]?></a>
							<?}?>
						</td>
						<td>
							<?if($r["dir"]=="RESOURCE"){?>
								<a href="?report=<?=$md5?>&op=results&action=dump_res" title="Display resources"><?=$r["dir"]?></a>
							<?}elseif($r["dir"]=="IMPORT" || $r["dir"]=="IAT"){?>
								<a href="?report=<?=$md5?>&op=results&action=imports" title="Display imports"><?=$r["dir"]?></a>
							<?}else{?>
								<?=$r["dir"]?>
							<?}?>
						</td>
						<td class="<?=$r["prd"]["intelligence"]["class"]?>">
							<?=$r["prd"]["value"]?>
						</td>
						<td class="<?=$r["srd"]["intelligence"]["class"]?>">
							<?=$r["srd"]["value"]?>
						</td>
						<td nowrap class="<?=$r["srd"]["intelligence"]["class"]?>" align=right>
							<?=sprintf("%.2f",$r["per_file"])?> %
						</td>
						<td>
							<?=$r["va"]?>
						</td>
						<td>
							<?=$r["flags"]?>
						</td>
						<td class="<?=$r["entropy"]["intelligence"]["class"]?>">
							<?=sprintf("%.4f",$r["entropy"]["value"])?>
						</td>
						<td>
							<?=sprintf("%.2f",$r["complex"])?>
						</td>
					</tr>
<?}?>
				</tbody>
			</table>
		</div>
	</div>

