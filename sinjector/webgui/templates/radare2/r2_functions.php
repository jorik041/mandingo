<?
// tr class= active, success, warning, danger
?>
<script>
function pdf(address){
	$.get( "?report=<?=$vars["md5"]?>&op=tools&app=radare2/radare2&fun=pdf&address="+address, function( data ) {
	  $( "#result_"+address ).html( data );
	});
}
</script>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>
							#
						</th>
						<th>
							Address
						</th>
						<th>
							Size
						</th>
						<th title="Cyclomatic Complexity">
							CC
						</th>
						<th>
							Name
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
foreach($vars["functions"] as $r){
	$i++;
?>

					<tr>
						<td>
							<?=$i?>
						</td>
						<td>
							<a href="javascript:pdf('<?=$r["address"]?>')" type=button><?=$r["address"]?></a>
						</td>
						<td>
							<?=$r["size"]?>
						</td>
						<td>
							<?=$r["cc"]?>
						</td>
						<td>
							<?=$r["name"]?>
							<div id=result_<?=$r["address"]?>></div>
						</td>
					</tr>
<?}?>
				</tbody>
			</table>
		</div>
	</div>
