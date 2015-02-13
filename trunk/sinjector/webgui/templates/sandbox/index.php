<?
$md5=Common::getMD5("report");
$action=Common::getString("action");
$id=Common::getInteger("id");
$binary="uploads/$md5.bin";
$html_res_offsets=Pefile::call("res_offsets",$binary);
$resources=Binary::resources($binary);
$sections=Binary::sections($binary);
$images=Sinjector::fetch($md5,"screenshots");
$processes=Sinjector::processes($md5);
$libraries=Sinjector::libraries($md5);
$files=Sinjector::files($md5);
$presources=Mono::presources($binary);
$packets=Tcpdump::packets($md5);
$imports=Binary::imports($binary);
#TODO: cache registry
if($action=="sandbox/registry" || $action=="sandbox/registry_queries"){
	$registry=Sinjector::registry($md5);
	$registry_queries=Sinjector::registry_queries($md5);
}
//if($action=="") $action="art";
?>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<div class="row clearfix">
				<div class="col-md-2 column">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">
								binary
							</h3>
						</div>
						<div class="panel-body">
							<?if($action=="details" || $action==""){?>
								<b>details</b>
							<?}else{?>
								<a href="?report=<?=$md5?>&op=results&action=details">details</a>
							<?}?>
							<br>
							<?if($action=="dump_sec"){?>
								<b>sections (<?=count($sections)?>)</b>
							<?}else{?>
								<a href="?report=<?=$md5?>&op=results&action=dump_sec">sections (<?=count($sections)?>)</a>
							<?}?>
							<?if(count($resources)){?>
								<br>
								<?if($action=="dump_res"){?>
									<b>resources (<?=count($resources)?>)</b>
								<?}else{?>
									<a href="?report=<?=$md5?>&op=results&action=dump_res">resources (<?=count($resources)?>)</a>
								<?}?>
							<?}?>
							<?if(count($presources)){?>
								<?if(!count($resources)){?>
									<br>
									resources
								<?}?>
								(<a href="?report=<?=$md5?>&op=tools&app=monodis&fun=presources">.net</a>)
							<?}?>
							<br>
							<?if($action=="imports"){?>
								<b>imports (<?=count($imports)?>)</b>
							<?}else{?>
								<a href="?report=<?=$md5?>&op=results&action=imports">imports (<?=count($imports)?>)</a>
							<?}?>
							<br>
							<?if($action=="strings"){?>
								<b>strings</b>
							<?}else{?>
								<a href="?report=<?=$md5?>&op=results&action=strings">strings</a>
							<?}?>
							<br>
							<?if($action=="graph"){?>
								<b>graph</b>
							<?}else{?>
								<a href="?report=<?=$md5?>&op=results&action=graph">graph</a>
							<?}?>
							<br>
							<?if($action=="art"){?>
								<b>art</b>
							<?}else{?>
								<a href="?report=<?=$md5?>&op=results&action=art">art</a>
							<?}?>
							<br>
						</div>
					</div>
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">
								execution
							</h3>
						</div>
						<div class="panel-body">
							<?if($action=="sandbox/processes"){?>
								<b>processes (<?=count($processes)?>)</b>
							<?}else{?>
								<a href="?report=<?=$md5?>&op=results&action=sandbox/processes">processes (<?=count($processes)?>)</a>
							<?}?>
							<br>
							<?if(count($libraries)){?>
								<?if($action=="sandbox/libraries"){?>
									<b>libraries (<?=count($libraries)?>)</b>
								<?}else{?>
									<a href="?report=<?=$md5?>&op=results&action=sandbox/libraries">libraries (<?=count($libraries)?>)</a>
								<?}?>
								<br>
							<?}?>
							<?if(count($files)){?>
								<?if($action=="sandbox/files"){?>
									<b>files (<?=count($files)?>)</b>
								<?}else{?>
									<a href="?report=<?=$md5?>&op=results&action=sandbox/files">files (<?=count($files)?>)</a>
								<?}?>
								<br>
							<?}?>
							<?if(count($packets)){?>
								<?if($action=="sandbox/network"){?>
									<b>network (<?=count($packets)?>)</b>
								<?}else{?>
									<a href="?report=<?=$md5?>&op=results&action=sandbox/network">network (<?=count($packets)?>)</a>
								<?}?>
								<br>
							<?}?>
							<?if($action=="sandbox/registry" || $action=="sandbox/registry_queries"){?>
								registry<br>
								<?if($action=="sandbox/registry"){?>
									<b>- writes (<?=count($registry)?>)</b>
								<?}else{?>
									- <a href="?report=<?=$md5?>&op=results&action=sandbox/registry">writes (<?=count($registry)?>)</a>
								<?}?>
								<br>
								<?if($action=="sandbox/registry_queries"){?>
									<b>- queries (<?=count($registry_queries)?>)</b>
								<?}else{?>
									- <a href="?report=<?=$md5?>&op=results&action=sandbox/registry_queries">queries (<?=count($registry_queries)?>)</a>
								<?}?>
								<br>
							<?}else{?>
									<a href="?report=<?=$md5?>&op=results&action=sandbox/registry">registry</a>
									<br>
							<?}?>
							<?if($action=="sandbox/screenshots"){?>
								<b>screenshots (<?=count($images)?>)</b>
							<?}else{?>
								<a href="?report=<?=$md5?>&op=results&action=sandbox/screenshots">screenshots (<?=count($images)?>)</a>
							<?}?>
						</div>
					</div>
				</div>
				<div class="col-md-10 column">
					<p>
						<?
						if($action=="sandbox/processes") 	print Templates::Load("templates/sandbox/processes.php",array("processes"=>$processes,"md5"=>$md5));
						if($action=="sandbox/libraries") 	print Templates::Load("templates/sandbox/libraries.php",array("libraries"=>$libraries,"md5"=>$md5));
						if($action=="sandbox/files") 		print Templates::Load("templates/sandbox/files.php",array("files"=>$files,"md5"=>$md5));
						if($action=="sandbox/screenshots") 	print Templates::Load("templates/sandbox/screenshots.php",array("images"=>$images,"md5"=>$md5));
						if($action=="sandbox/registry") 		print Templates::Load("templates/sandbox/registry.php",array("registry"=>$registry,"md5"=>$md5,"mode"=>"set"));
						if($action=="sandbox/registry_queries") print Templates::Load("templates/sandbox/registry.php",array("registry"=>$registry_queries,"md5"=>$md5,"mode"=>"query"));
						if($action=="sandbox/network") 		print Templates::Load("templates/sandbox/network.php",array("packets"=>$packets,"md5"=>$md5));
						if(file_exists($binary)) {
								if($action=="details" || $action=="") {
									print Templates::Load("templates/misc/info.php",array("md5"=>$md5,"binary"=>$binary));
									print Templates::Load("templates/misc/info_version.php",array("md5"=>$md5,"binary"=>$binary));
								}
								if($action=="dump_sec") {
									print Templates::Load("templates/misc/sections.php",array("md5"=>$md5,"binary"=>$binary,"sections"=>$sections));
									if($id>0) print Templates::Load("templates/misc/section_dump.php",array(""));
									print Templates::Load("templates/art/draw_sections.php",array("version"=>Binary::version($binary),"magic"=>Binary::magic($binary),"resources"=>$resources,"sections"=>$sections,"md5"=>$md5));
								}
								if($action=="dump_res") {
									print Templates::Load("templates/misc/resources.php",array("resources"=>$resources));
									print Templates::Load("templates/art/draw_resources.php",array("resources"=>$resources,"filesize"=>Binary::filesize($binary,false),"md5"=>$md5,"res_size"=>Binary::resourcesSize($binary),"res_comp"=>Binary::resourcesCompression($binary)));
								}
								if($action=="imports") {
									print Templates::Load("templates/misc/imports.php",array("imports"=>$imports));
								}
								if($action=="strings") {
									print Templates::Load("templates/misc/strings.php",array("binary"=>$binary));
								}
								if($action=="graph") {
									print Templates::Load("templates/misc/graph.php",array("binary"=>$binary,"md5"=>$md5));
								}
								if($action=="art") {
									$codesize=Binary::codeSize($binary);
									$entrypoint=Pefile::entrypoint($binary);
									print Templates::Load("templates/art/drawing3.php",array("functions"=>Radare2::r2_functions($binary),"md5"=>$md5,"codesize"=>$codesize,"entrypoint"=>$entrypoint));
								}
						}else 
							print "Binary not found.. removed?";					
						?>
					</p>
				</div>
			</div>
		</div>
	</div>

