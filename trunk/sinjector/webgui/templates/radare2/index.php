<?
$md5=Common::getMD5("report");
$app=Common::getString("app");
$fun=Common::getString("fun");
if($app=="" && $fun=="") {
	$app="radare2/rabin2";	
	$fun="-I";
}
$binary="uploads/$md5.bin";
if(preg_match("/Mono/",Binary::magic($binary))){
	$moduleref=Mono::monodis_moduleref($binary);
	$implmap=Mono::monodis_implmap($binary);
	$presources=Mono::presources($binary);
	$strings=Mono::monodis_strings($binary);
}
$app=Common::getString("app");
if(preg_match("/^radare2\/radare2/",$app)) {
	if($fun=="pdf") {
		$function=Common::GetString("function");
		$address=Common::GetString("address");
		ob_clean();
		print Templates::Load("templates/radare2/r2_pdf.php",array("asm"=>Radare2::pdf($binary,$address),"md5"=>$md5));
		die;
	}
}
?>
	<div class="row clearfix">
		<div class="col-md-12 column">
			<div class="row clearfix">
				<div class="col-md-2 column">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">
								misc
							</h3>
						</div>
						<div class="panel-body">
							<?if(preg_match("/Mono/",Binary::magic($binary))){?>
								<b>monodis</b>
								<br>
								<?if($app=="monodis" && $fun=="pedump"){?>
									- pedump
								<?}else{?>
									- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=pedump">pedump</a>
								<?}?>
								<br>
								<?if($app=="monodis" && $fun=="assemblyref"){?>
									- assemblyref
								<?}else{?>
									- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=assemblyref">assemblyref</a>
								<?}?>
								<br>
								<?if($app=="monodis" && $fun=="disassemble"){?>
									- disassemble
								<?}else{?>
									- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=disassemble">dissasemble</a>
								<?}?>
								<br>
								<?if($app=="monodis" && $fun=="methods"){?>
									- methods
								<?}else{?>
									- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=methods">methods</a>
								<?}?>
								<br>
								<?if($app=="monodis" && $fun=="fields"){?>
									- fields
								<?}else{?>
									- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=fields">fields</a>
								<?}?>
								<br>
								<?if($app=="monodis" && $fun=="param"){?>
									- param
								<?}else{?>
									- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=param">param</a>
								<?}?>
								<?if(strlen($moduleref)){?>
									<br>
									<?if($app=="monodis" && $fun=="moduleref"){?>
										- moduleref
									<?}else{?>
										- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=moduleref">moduleref</a>
									<?}?>
								<?}?>
								<?if(strlen($implmap)){?>
									<br>
									<?if($app=="monodis" && $fun=="implmap"){?>
										- implmap
									<?}else{?>
										- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=implmap">implmap</a>
									<?}?>
								<?}?>
								<?if(count($presources)){?>
									<br>
									<?if($app=="monodis" && $fun=="presources"){?>
										- presources
									<?}else{?>
										- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=presources">presources</a>
									<?}?>
								<?}?>
								<?if(count($strings)){?>
									<br>
									<?if($app=="monodis" && $fun=="strings"){?>
										- strings
									<?}else{?>
										- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=strings">strings</a>
									<?}?>
								<?}?>
								<br>
								<?if($app=="monodis" && $fun=="typedef"){?>
									- typedef
								<?}else{?>
									- <a href="?report=<?=$md5?>&op=tools&app=monodis&fun=typedef">typedef</a>
								<?}?>
							<br>
							<?}?>
							<b>pefile</b> / 
							<?if($app=="pefile" && $fun=="dumpinfo"){?>
								dump_info()
							<?}else{?>
								<a href="?report=<?=$md5?>&op=tools&app=pefile&fun=dumpinfo">dump_info()</a>
							<?}?>
						</div>
					</div>
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">
								radare2
							</h3>
						</div>
						<div class="panel-body">
							<b>rabin2</b>
							<br>
							<?if($app=="radare2/rabin2" && $fun=="-I"){?>
								- binary info (-I)
							<?}else{?>
								- <a href="?report=<?=$md5?>&op=tools&app=radare2/rabin2&fun=-I">binary info (-I)</a>
							<?}?>
							<br>
							<?if($app=="radare2/rabin2" && $fun=="-e"){?>
								- entry point (-e)
							<?}else{?>
								- <a href="?report=<?=$md5?>&op=tools&app=radare2/rabin2&fun=-e">entry point (-e)</a>
							<?}?>
							<br>
							<?if($app=="radare2/rabin2" && $fun=="-i"){?>
								- imports (-i)
							<?}else{?>
								- <a href="?report=<?=$md5?>&op=tools&app=radare2/rabin2&fun=-i">imports (-i)</a>
							<?}?>
							<br>
							<?if($app=="radare2/rabin2" && $fun=="-l"){?>
								- linked libraries (-l)
							<?}else{?>
								- <a href="?report=<?=$md5?>&op=tools&app=radare2/rabin2&fun=-l">linked libraries (-l)</a>
							<?}?>
							<br>
							<?if($app=="radare2/rabin2" && $fun=="-R"){?>
								- relocations (-R)
							<?}else{?>
								- <a href="?report=<?=$md5?>&op=tools&app=radare2/rabin2&fun=-R">relocations (-R)</a>
							<?}?>
							<br>
							<?if($app=="radare2/rabin2" && $fun=="-s"){?>
								- symbols (-s)
							<?}else{?>
								- <a href="?report=<?=$md5?>&op=tools&app=radare2/rabin2&fun=-s">symbols (-s)</a>
							<?}?>
							<br>
							<?if($app=="radare2/rabin2" && $fun=="-S"){?>
								- sections (-S)
							<?}else{?>
								- <a href="?report=<?=$md5?>&op=tools&app=radare2/rabin2&fun=-S">sections (-S)</a>
							<?}?>
							<br>
							<?if($app=="radare2/rabin2" && $fun=="-z"){?>
								- strings data sec (-z)
							<?}else{?>
								- <a href="?report=<?=$md5?>&op=tools&app=radare2/rabin2&fun=-z">strings data sec (-z)</a>
							<?}?>
							<br>
							<?if($app=="radare2/rabin2" && $fun=="-zz"){?>
								- strings raw bin (-zz)
							<?}else{?>
								- <a href="?report=<?=$md5?>&op=tools&app=radare2/rabin2&fun=-zz">strings raw bin (-zz)</a>
							<?}?>
							<br>
							<b>radare2</b> / 
							<?if($app=="radare2/radare2" && $fun=="aa;af;afl"){?>
								aa;af;afl
							<?}else{?>
								<a href="?report=<?=$md5?>&op=tools&app=radare2/radare2&fun=aa;af;afl">aa;af;afl</a>
							<?}?>
							<br>
							<b>rahash2</b> /
							<?if($app=="radare2/rahash2" && $fun=="-a all"){?>
								-a all
							<?}else{?>
								<a href="?report=<?=$md5?>&op=tools&app=radare2/rahash2&fun=-a all">-a all</a>
							<?}?>
						</div>
					</div>
				</div>
				<div class="col-md-10 column">
					<p>
						<?
						if(file_exists($binary)) {
							if(preg_match("/^radare2\/(rabin2|rahash2)/",$app)) print Radare2::call($app,$fun,$binary);
							if(preg_match("/^radare2\/radare2/",$app)) {
								$functions=Radare2::r2_functions($binary);
								$codesize=Binary::codeSize($binary);
								$entrypoint=Pefile::entrypoint($binary);
								print Templates::Load("templates/art/drawing3.php",array("functions"=>$functions,"codesize"=>$codesize,"entrypoint"=>$entrypoint,"md5"=>$md5
));
								print Templates::Load("templates/radare2/r2_functions.php",array("functions"=>$functions,"md5"=>$md5));
							}
							if(preg_match("/^pefile/",$app)) print Pefile::call($fun,$binary);
							if(preg_match("/^monodis/",$app)) {
								if($fun=="presources") print Templates::Load("templates/mono/presources.php",array("presources"=>$presources,"binary"=>$binary,"md5"=>$md5));
								else print Mono::call($fun,$binary);
							}
						}
							else print "Binary not found.. removed?";
						?>
					</p>
				</div>
			</div>
		</div>
	</div>

