<?
$hooks=Sinjector::hooks();
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
							Function
						</th>
						<th>
							Address
						</th>
						<th>
							Library
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
foreach($vars["imports"] as $r){
	$i++;
 	$msg="";
	if(in_array($r["function"],$hooks)) $msg=" <font size=1>Hooked</font>";
?>

					<tr>
						<td>
							<?=$i?>
						</td>
						<td>
							<?if($r["function"]!="None"){?>
								<a href="https://social.msdn.microsoft.com/Search/en-US/windows/desktop?query=<?=$r["function"]?>" target=_blank><?=$r["function"]?></a><?=$msg?>
							<?}else{?>
								None
							<?}?>
						</td>
						<td>
							<?=$r["address"]?>
						</td>
						<td>
							<?=$r["library"]?>
						</td>
					</tr>
<?}?>
				</tbody>
			</table>
		</div>
	</div>

