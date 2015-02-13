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
							Type
						</th>
						<th width=64>
							
						</th>
						<th>
							VirtAddr
						</th>
						<th>
							OffToData
						</th>
						<th>
							Bytes
						</th>
						<th>
							FileOffset
						</th>
						<th>
							Language
						</th>
						<th>
							Date
						</th>
						<th>
							%rec
						</th>
					</tr>
				</thead>
				<tbody>
<?
$i=0;
$md5=Common::getMD5("report");
$binary="uploads/$md5.bin";
$icon_count=0;
$bitmap_count=0;
$rcdata_count=0;
$string_count=0;
$manifest=false;
$version=false;
$va=0;
foreach($vars["resources"] as $r){
	$i++;
	$va=$r["va"];
?>

					<tr>
						<td>
							<?=$i?>
						</td>
						<td class="<?=$r["type"]["intelligence"]["class"]?>"><?if($r["type"]["value"]=="RT_MANIFEST"){
								$manifest=true;
								?>
								<a id="modal-MANIFEST" href="#modal-container-MANIFEST" role="button" data-toggle="modal" title="Show manifest">RT_MANIFEST</a>
							<?}elseif($r["type"]["value"]=="RT_VERSION"){
								$version=true;
								?>
								<a id="modal-VERSION" href="#modal-container-VERSION" role="button" data-toggle="modal" title="Show version">RT_VERSION</a>
							<?}elseif($r["type"]["value"]=="RT_RCDATA"){
								$rcdata_count++;
								if($r["fo"]["value"][0]=="-"){
								?>
									RT_RCDATA
								<?}else{?>
									<a href="?report=<?=$md5?>&op=results&action=dump_res&rcdata=<?=$rcdata_count?>">RT_RCDATA</a>
								<?}?>
							<?}elseif($r["type"]["value"]=="RT_BITMAP"){
								$bitmap_count++;
								?>
								<a href="?report=<?=$md5?>&op=results&action=dump_res&rtbitmap=<?=$bitmap_count?>"><?=$r["type"]["value"]?></a>
							<?}elseif($r["type"]["value"]=="RT_STRING"){
								$string_count++;
								?>
								<a href="?report=<?=$md5?>&op=results&action=dump_res&rtstring=<?=$string_count?>"><?=$r["type"]["value"]?></a>
							<?}else{?>
								<?=$r["type"]["value"]?>
							<?}?>
						</td>
						<td align=center><?
							if($r["type"]["value"]=="RT_ICON" && $r["fo"]!="?"){
								$icon_count++;
								$cache_icon="cache/$md5"."_icon_".$icon_count.".bmp";
							?>
							<?if(!file_exists($cache_icon)){?>
								<a href="?report=<?=$md5?>&icon=<?=$icon_count?>" target=_blank title="Open icon in a new window">
								<img src="?report=<?=$md5?>&icon=<?=$icon_count?>" height=32>
								</a>	
							<?}else{?>
								<a href="<?=$cache_icon?>" target=_blank title="Open icon in a new window">
								<img src="<?=$cache_icon?>" height=32>
								</a>
							<?}?>
							<?}
							if($r["type"]["value"]=="RT_BITMAP" && $r["fo"]!="?"){
								$cache_bitmap="cache/$md5"."_bitmap_".$bitmap_count.".bmp";
							?>
							<?if(!file_exists($cache_bitmap) || isset($_GET["clear_cache"])){?>
								<a href="?report=<?=$md5?>&bitmap=<?=$bitmap_count?>" target=_blank title="Open bitmap in a new window">
								<img src="?report=<?=$md5?>&bitmap=<?=$bitmap_count?>" height=32>
								</a>	
							<?}else{?>
								<a href="<?=$cache_bitmap?>" target=_blank title="Open bitmap in a new window">
								<img src="<?=$cache_bitmap?>" class="img-thumbnail">
								</a>	
							<?}?>
							<?}
						?></td>
						<td>
							<?=$r["va"]?>
						</td>
						<td>
							<?=$r["od"]?>
						</td>
						<td class="<?=$r["size"]["intelligence"]["class"]?>">
							<?=number_format($r["size"]["value"])?>
						</td>
						<td class="<?=$r["fo"]["intelligence"]["class"]?>">
							<?=$r["fo"]["value"]?>
						</td>
						<td>
							<?=$r["lang"]?>
						</td>
						<td nowrap class="<?=$r["timedatestamp"]["intelligence"]["class"]?>">
							<?=date("y-m-d H:i",$r["timedatestamp"]["value"])?>
						</td>
						<td>
							<?=sprintf("%.2f",$r["per_resource"])?>
						</td>
					</tr>
<?}?>
				</tbody>
			</table>
		</div>
	</div>

<?if($manifest){?>
	<div class="modal fade" id="modal-container-MANIFEST" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:70%">
			<div class="modal-content">
				<div class="modal-header">
					 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myModalLabel">
						RT_MANIFEST
					</h4>
				</div>
				<div class="modal-body">
					<pre><?print htmlentities(Pefile::manifest($binary,true));?></pre>
				</div>
				<div class="modal-footer">
					 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		
		</div>
	
	</div>
<?}?>
<?if($version){?>
	<div class="modal fade" id="modal-container-VERSION" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:70%">
			<div class="modal-content">
				<div class="modal-header">
					 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myModalLabel">
						RT_VERSION
					</h4>
				</div>
				<div class="modal-body">
					<pre><?print htmlentities(Pefile::fileinfo($binary,true));?></pre>
				</div>
				<div class="modal-footer">
					 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		
		</div>
	
	</div>
<?}?>
<?
if(isset($_GET["rcdata"])) {
	print Templates::Load("templates/misc/rcdata_dump.php",array(""));
}
if(isset($_GET["rtbitmap"])) {
	print Templates::Load("templates/misc/rtbitmap_dump.php",array(""));
}
if(isset($_GET["rtstring"])) {
	print Templates::Load("templates/misc/rtstring_dump.php",array(""));
}
?>
<?
//check if we found the right section for dumping the resources
$sectionFound=false;
foreach(Binary::sections($binary) as $s){
	if($va==$s["va"]) $sectionFound=true;
}
if(!$sectionFound && $va) print Common::Error("Error","The section with VirtualAddress=$va was not found");
?>
