<?
$packerid=Pefile::packerid($vars["binary"],true);
$compiler=Pefile::compiler($vars["binary"]);
$timedatestamp=Pefile::timedatestamp($vars["binary"]);
?>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<table class="table table-bordered">
				<thead>
					<tr>
						<td width=20% class="active">MD5</td>
						<td><?=$vars["md5"]?></td>
					</tr>
					<tr>
						<td class="active">File Size</td>
						<td><?=Binary::filesize($vars["binary"])?></td>
					</tr>
					<tr>
						<td class="active">File Type</td>
						<td><?=Binary::magic($vars["binary"])?></td>
					</tr>
					<tr>
						<td class="active">Internal Date</td>
						<td><?=date("Y-m-d H:i",$timedatestamp)?></td>
					</tr>
					<tr>
						<td class="active">Packer ID</td>
						<td><?=$packerid?></td>
					</tr>
					<tr>
						<td class="active">Compiler</td>
						<td><?=preg_replace("/\n/","<br>",$compiler)?></td>
					</tr>					
				</tbody>
			</table>
		</div>
	</div>
