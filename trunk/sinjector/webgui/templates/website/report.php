<?
$md5=htmlentities(Common::getMD5("report"));
$op=Common::getString("op");
if($op=="") $op="results";
$images=Sinjector::fetch($md5,"screenshots");
?>
<br>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<ul class="nav nav-pills">
				<li class="<?=($op=="results")?"active":""?>">
					<a href="?report=<?=$md5?>&op=results" title="Static binary information">Results</a>
				</li>
				<li class="<?=($op=="log")?"active":""?>">
					<a href="?report=<?=$md5?>&op=log" title="Raw Log">Full Log</a>
				</li>
				<li class="<?=($op=="compact")?"active":""?>">
					<a href="?report=<?=$md5?>&op=compact" title="Log in compact mode (no dups)">Compact</a>
				</li>
				<li class="<?=($op=="very_compact")?"active":""?>">
					<a href="?report=<?=$md5?>&op=very_compact" title="Log in compact mode without registry calls or libraries info">Very compact</a>
				</li>
				<li class="<?=($op=="radare2")?"active":""?>">
					<a href="?report=<?=$md5?>&op=tools" title="binary tools">Tools</a>
				</li>
			</ul>
		</div>
	</div>
<p>

<?if($op=="tools"){
	if(Common::getString("fun")=="pdf") ob_clean();
	print Templates::Load("templates/radare2/index.php",array(""));
}elseif($op=="pefile"){
	print Templates::Load("templates/pefile/index.php",array(""));
}elseif($op=="results"){
	print Templates::Load("templates/sandbox/index.php",array(""));
}elseif($op=="screenshots"){
	if(count($images)){
		foreach($images as $img){
		?>
			<center>			
			 <a id="modal-<?=$img?>" href="#modal-container-<?=$img?>" role="button" class="btn" data-toggle="modal">
			<p><?=$img?></p><img src="?report=<?=$md5?>&image=<?=$img?>" class="img-thumbnail" width=140 height=140></a>
			
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
}else{?>
<pre>
<?=Sinjector::fetch($md5,$op);?>
</pre>
<?}?>
</p>

