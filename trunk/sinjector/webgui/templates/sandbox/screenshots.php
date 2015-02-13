<?
$images=$vars["images"];
$md5=$vars["md5"];
if(count($images)){
	foreach($images as $img){
	?>
		 <a id="modal-<?=$img?>" href="#modal-container-<?=$img?>" role="button" class="btn" data-toggle="modal">
		<?=$img?><br><img src="?report=<?=$md5?>&image=<?=$img?>" class="img-thumbnail" width=140 height=140></a>
		
		<div class="modal fade" id="modal-container-<?=$img?>" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style="width:880px">
				<div class="modal-content">
					<div class="modal-header">
						 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" id="myModalLabel">
							<?=$img?>
						</h4>
					</div>
					<div class="modal-body">
						<a href="?report=<?=$md5?>&image=<?=$img?>" target=_blank title="click to full display"><img src="?report=<?=$md5?>&image=<?=$img?>" class="img-thumbnail"></a>
					</div>
					<div class="modal-footer">
						 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
				
			</div>
			
		</div>
		
<?
	}
}else{
	print "<pre>No screenshots found...</pre>";
}
?>
