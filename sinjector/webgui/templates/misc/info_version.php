<?
$version=Binary::version($vars["binary"]);
if(!count($version)) return;
?>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<table class="table table-bordered">
				<thead>
					<?foreach($version as $v){?>
					<tr>
						<td width=20% class="active"><?=$v["name"]?></td>
						<td><?=$v["value"]?></td>
					</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	</div>
