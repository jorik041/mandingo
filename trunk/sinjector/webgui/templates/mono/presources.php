<?
$id=Common::getInteger("id");
// tr class= active, success, warning, danger
?>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>
							ID
						</th>
						<th>
							Name
						</th>
						<th>
							Size (bytes)
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
foreach($vars["presources"] as $r){
	$i++;
?>

					<tr>
						<td>
							<?=$r["id"]?>
						</td>
						<td>
							<a href="?report=<?=$vars["md5"]?>&op=tools&app=monodis&fun=presources&id=<?=$r["id"]?>"><?=$r["name"]?></a>
						</td>
						<td>
							<?=number_format($r["size"])?>
						</td>
					</tr>
<?}?>
				</tbody>
			</table>
		</div>
	</div>
<?
if(strlen($id)) print Templates::Load("templates/mono/resource_dump.php",array_merge($vars,array("id"=>$id)));
?>
