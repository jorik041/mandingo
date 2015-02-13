<?
$md5=Common::getMD5("report");
$action=Common::getString("action");
$binary="uploads/$md5.bin";
$html_res_offsets=Pefile::call("res_offsets",$binary);
$html_packerid=Pefile::call("packerid",$binary);
?>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<div class="row clearfix">
				<div class="col-md-2 column">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">
								pefile
							</h3>
						</div>
						<div class="panel-body">
							<a href="?report=<?=$md5?>&op=pefile&action=res_offsets">resources</a>
							<br>
							<a href="?report=<?=$md5?>&op=pefile&action=sec_offsets">sections</a>
							<br>
							<a href="?report=<?=$md5?>&op=pefile&action=imports">imports</a>
							<br>
							<?if(preg_match("/RT_MANIFEST/",$html_res_offsets)){?>
								<a href="?report=<?=$md5?>&op=pefile&action=manifest">manifest</a>
								<br>
							<?}?>
							<?if(!preg_match("/None/",$html_packerid)){?>
								<a href="?report=<?=$md5?>&op=pefile&action=packerid">packerid</a>
								<br>
							<?}?>
							<a href="?report=<?=$md5?>&op=pefile&action=dumpinfo">dump_info()</a>
							<br>
						</div>
					</div>
				</div>
				<div class="col-md-10 column">
					<p>
						<?
						if(file_exists($binary)) {
							if($action=="res_offsets"){
								print $html_res_offsets;
								preg_match_all("/RT_ICON (.+)/",$html_res_offsets,$i);
								$count=0;
								foreach($i[1] as $icon){
									$count++;
									list($offset_d,$size,$offset)=preg_split("/\s/",$icon);
									print "<img src=\"?report=$md5&icon=$count\" class=\"img-thumbnail\" title=\"RT_ICON@".$offset."\">";
								}
								preg_match_all("/RT_BITMAP (.+)/",$html_res_offsets,$i);
								$count=0;
								foreach($i[1] as $bitmap){
									$count++;
									list($offset_d,$size,$offset)=preg_split("/\s/",$bitmap);
									print "<img src=\"?report=$md5&bitmap=$count\" class=\"img-thumbnail\" title=\"RT_BITMAP@".$offset."\">";
								}
								
							}else{
								$html=Pefile::call($action,$binary);
								print $html;
							}
						}else 
							print "Binary not found.. removed?";
						?>
					</p>
				</div>
			</div>
		</div>
	</div>
