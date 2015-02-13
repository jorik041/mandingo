<?
class Sinjector{
	static function hooks(){
		$items=array();
		foreach(preg_split("/\n/",file_get_contents("modules/hooks.txt")) as $line){
			array_push($items,$line);
		}
		return $items;
	}
	static function Analyze($filename){
		$cmd="cd ".Config::$sinjector_path." && ".Config::$python." -i \"".Config::$sinjector_client."\" ";
		$cmd.=Config::$sinjector_vm_addr." ";
		$cmd.="\"".$filename."\" 2>&1";
		return $cmd;
	}
	static function Results(){
		$basedir=Config::$sinjector_path."/results";
		if(!is_dir($basedir)) die("ERROR - \"$basedir\" not found...");
		$files = @scandir($basedir);
		$res=array();
		foreach($files as $f){
			if($f[0]==".") continue;
			$item=array();
			$item["md5"]=$f;
			$item["type"]=Binary::magic("uploads/".$f.".bin");
			$item["type"]=preg_replace("/executable for MS Windows/","exec MSWin",$item["type"]);
			$item["art"]=array();
			if(file_exists("art/$f"."-0.png")) $item["art"]["sections"]="art/$f"."-0.png";
			if(file_exists("art/$f"."-1.png")) $item["art"]["resources"]="art/$f"."-1.png";
			if(file_exists("art/$f"."-2.png")) $item["art"]["r2_functions"]="art/$f"."-2.png";
			$item["date"]=date ("M d H:i:s", filemtime($basedir."/".$f));
			$log=self::fullLog($f);
			if(count(preg_split("/\n/",$log))<=2){
				$item["status"]="Failed";
			}else{
				$item["status"]="Done";
			}
			array_push($res,$item);
		}

		@usort($res, array(self,"cmp_samples"));

		return $res;
	}
	static function cmp_samples($a, $b)
	{
		return strcmp($b["date"], $a["date"]);
	}
	static function fetch($md5,$option,$pid=""){
		$html="";
		if($option=="log") 			$html=self::fullLog($md5);
		if($option=="compact") 		$html=self::compactLog($md5,true,$pid);
		if($option=="very_compact")	$html=self::compactLog($md5,false,$pid,false);
		if($option=="processes")		$html=self::dump_processes($md5);
		if($option=="libraries")		$html=self::dump_libraries($md5);
		if($option=="files")			$html=self::dump_files($md5);
		if($option=="screenshots")	return self::screenshots($md5);
		$html=htmlentities($html,ENT_QUOTES, "iso-8859-1");
		if(!strlen($html)) return "Nothing found...";
		if($option=="compact" || $option=="very_compact") $html=self::colorize($html);
		return $html;
	}
	static function files($md5){
		$items=array();
		foreach(preg_split("/\n/",self::files_created($md5)) as $line){
			if(!strlen($line)) continue;
			if(preg_match("/([^\|]+)\|(.*)/",$line,$m)){
				$i=array();
				$i["filename"]=$m[1];
				$i["pids"]=preg_split("/,/",$m[2]);
				$i["action"]="created";
				$i["intelligence"]=array("class"=>"");
				if(preg_match("/\.dll$/i",$i["filename"])) $i["intelligence"]["class"]="warning";
				if(preg_match("/\.exe$/i",$i["filename"])) $i["intelligence"]["class"]="warning";
				if(preg_match("/\.job$/i",$i["filename"])) $i["intelligence"]["class"]="warning";
				array_push($items,$i);
			}
		}
		foreach(preg_split("/\n/",self::files_deleted($md5)) as $line){
			if(!strlen($line)) continue;
			if(preg_match("/([^\|]+)\|(.*)/",$line,$m)){
				$i=array();
				$i["filename"]=$m[1];
				$i["pids"]=preg_split("/,/",$m[2]);
				$i["action"]="deleted";
				$i["intelligence"]=array("class"=>"");
				array_push($items,$i);
			}
		}
		return $items;
	}
	static function libraries($md5){
		$out=self::dump_libraries($md5);
		$items=array();
		foreach(preg_split("/\n/",$out) as $line){
			if(!strlen($line)) continue;
			if(preg_match("/handle=(\d+) \"([^\"]+)/",$line,$m)){
				$i=array();
				$i["handle"]=$m[1];
				$i["name"]=$m[2];
				array_push($items,$i);
			}
		}
		return $items;
	}
	static function procedures($md5,$handle){
		$out=self::dump_procedures($md5);
		$items=array();
		foreach(preg_split("/\n/",$out) as $line){
			if(!strlen($line)) continue;
			if(preg_match("/handle=(\d+) \"([^\"]+)/",$line,$m)){
				if(intval($m[1])==intval($handle)){
					array_push($items,$m[2]);
				}
			}
		}
		return $items;
	}
	static function processes($md5){
		$out=self::dump_processes($md5);
		$items=array();
		foreach(preg_split("/\n/",$out) as $line){
			$i=array();
			if(preg_match("/^([^\s]+)\s+([^\s]+)\s+([^\s]+)\s+(.+)/",$line,$m)){
				list($i["pid"],$i["action"],	$i["msg"],$i["name"])=array($m[1],$m[2],$m[3],$m[4]);
				if(is_numeric($i["pid"])) array_push($items,$i);
			}
		}
		@usort($items, array(self,"cmp_processes"));
		return $items;
	}
	static function cmp_processes($a, $b)
	{
		if($b["pid"]==$a["pid"]) {
			if($a["action"]=="created") return -1;
			if($b["action"]=="created") return 1;
		}
		return intval($a["pid"])>intval($b["pid"]);
	}
	static function screenshots($md5){
		$basedir=Config::$sinjector_path."/results/$md5/";
		if(!is_dir($basedir)) die("ERROR - \"$md5\" not found...");
		$files = @scandir($basedir);
		$res=array();
		foreach($files as $f){
			if(preg_match("/^screenshot-(.+)\.png$/",$f,$m)) array_push($res,$m[1]);
		}
		return $res;
	}
	static function dump_files($md5){
		$res_c=self::files_created($md5);
		$res_d=self::files_deleted($md5);
		if(strlen($res_c)) $res_c="Created files\n-------------\n".$res_c;
		if(strlen($res_d)) $res_d="\nDeleted files\n-------------\n".$res_d;
		return $res_c."\n".$res_d;
	}
	static function files_deleted($md5){
		$logfile=Config::$sinjector_path."/results/$md5/".Config::$logfilename;
		if(!file_exists($logfile)) return "Results not found.. bad analysis?";
		#get deleted files
		$script="scripts/deleted_files.py";
		$cmd="cd ".Config::$sinjector_path." && ".Config::$python." -i $script ";
		$cmd.="results/$md5/".Config::$logfilename;
		@ob_start();
		system($cmd);
        $res_d=ob_get_contents();
        ob_end_clean();
		$res_d=trim($res_d);
		return $res_d;
	}
	static function files_created($md5){
		$logfile=Config::$sinjector_path."/results/$md5/".Config::$logfilename;
		if(!file_exists($logfile)) return "Results not found.. bad analysis?";
		#get created files
		$script="scripts/created_files.py";
		$cmd="cd ".Config::$sinjector_path." && ".Config::$python." -i $script ";
		$cmd.="results/$md5/".Config::$logfilename;
		@ob_start();
		system($cmd);
        $res_c=ob_get_contents();
        ob_end_clean();
		$res_c=trim($res_c);
		return $res_c;
	}
	static function dump_libraries($md5){
		$logfile=Config::$sinjector_path."/results/$md5/".Config::$logfilename;
		if(!file_exists($logfile)) return "Results not found.. bad analysis?";
		$script="scripts/loaded_libraries.py";
		$cmd="cd ".Config::$sinjector_path." && ".Config::$python." -i $script ";
		$cmd.="results/$md5/".Config::$logfilename;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return $res;
	}
	static function dump_procedures($md5){
		$logfile=Config::$sinjector_path."/results/$md5/".Config::$logfilename;
		if(!file_exists($logfile)) return "Results not found.. bad analysis?";
		$script="scripts/loaded_procedures.sh";
		$cmd="cd ".Config::$sinjector_path." && $script ";
		$cmd.="results/$md5/".Config::$logfilename;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return $res;
	}
	static function dump_processes($md5){
		$logfile=Config::$sinjector_path."/results/$md5/".Config::$logfilename;
		if(!file_exists($logfile)) return "Results not found.. bad analysis?";
		$script="scripts/affected_processes.py";
		$cmd="cd ".Config::$sinjector_path." && ".Config::$python." -i $script ";
		$cmd.="results/$md5/".Config::$logfilename;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		return $res;
	}
	static function registry($md5,$handle="",$pid=""){
		$reg=self::dump_registry($md5,$handle,$pid);
		$items=array();
		foreach(preg_split("/\n/",$reg) as $line){
			//[7460 ] [RegSetValueExW] REG_SZ "handle(0xfc)\BaseClass" "Drive"
			if(preg_match("/^\[([^\]]+).+\] ([^\"]+)\"handle\(([^\)]+)\)\\\\([^\"]+)\" \"(.+)\"/",$line,$m)){
				$item=array();
				$item["line"]=$line;
				$item["class"]="S";
				$item["pid"]=trim($m[1]);
				$item["type"]=trim($m[2]);
				$item["handle"]=trim($m[3]);
				$item["key"]=trim($m[4]);
				$item["value"]=trim($m[5]);
				$item["ret"]="";
				$item["type"]=preg_replace("/REG_DWORD_LITTLE_ENDIAN/","",$item["type"]);
				$item["type"]=preg_replace("/REG_/","",$item["type"]);
				array_push($items,$item);
			}
			//[15244 ] [RegCreateKeyExW] handle=0x174 ALL_ACCESS "handle(0x17c)\Avwixi"
			if(preg_match("/^\[([^\]]+).+\] handle=(0x[^\s]+) ([^\"]+)\"handle\(([^\)]+)\)\\\\([^\"]+)\"/",$line,$m)){
				$item=array();
				$item["line"]=$line;
				$item["class"]="C";
				$item["pid"]=trim($m[1]);
				$item["ret"]=trim($m[2]);
				$item["type"]="";
				$item["handle"]=trim($m[4]);
				$item["key"]=trim($m[5]);
				$item["value"]="";

				array_push($items,$item);
			}
			//[1928  ] [RegCreateKeyExW] handle=0x6d4 ALL_ACCESS "HKCU\Software\Microsoft\Windows\Currentversion\Run"
			if(preg_match("/^\[([^\]]+).+\] handle=(0x[^\s]+) ([^\"]+)\"([^\"]+)\"/",$line,$m)){
				$item=array();
				$item["line"]=$line;
				$item["class"]="C";
				$item["pid"]=trim($m[1]);
				$item["ret"]=trim($m[2]);
				$item["type"]="";
				$item["handle"]="";
				$item["key"]=trim($m[4]);
				$item["value"]="";

				array_push($items,$item);
			}
		}
		return $items;
	}
	static function dump_registry($md5,$handle,$pid){
		$logfile=Config::$sinjector_path."/results/$md5/".Config::$logfilename;
		if(!file_exists($logfile)) return "Results not found.. bad analysis?";
		$script="scripts/reg_written.py";
		$cmd="cd ".Config::$sinjector_path." && ".Config::$python." -i $script ";
		$cmd.="results/$md5/".Config::$logfilename;
		if(strlen($pid)) $cmd.=" $pid";
		if(strlen($handle)) $cmd.=" $handle";
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return $res;
	}
	static function registry_queries($md5,$handle="",$pid=""){
		$reg=self::dump_registry_queries($md5,$handle,$pid);
		$items=array();
		foreach(preg_split("/\n/",$reg) as $line){
			//[14100 ] [RegQueryValueExA] REG_SZ "handle(0x28)\Name" "(null)"
			if(preg_match("/^\[([^\]]+).+\] ([^\"]+)\"handle\(([^\)]+)\)\\\\([^\"]+)\" \"(.+)\"/",$line,$m)){
				$item=array();
				$item["line"]=$line;
				$item["class"]="Q";
				$item["pid"]=trim($m[1]);
				$item["type"]=trim($m[2]);
				$item["handle"]=trim($m[3]);
				$item["key"]=trim($m[4]);
				$item["value"]=trim($m[5]);
				$item["ret"]="";
				$item["type"]=preg_replace("/REG_DWORD_LITTLE_ENDIAN/","",$item["type"]);
				$item["type"]=preg_replace("/REG_/","",$item["type"]);
				array_push($items,$item);
			}
			//[15244 ] [RegCreateKeyExW] handle=0x174 ALL_ACCESS "handle(0x17c)\Avwixi"
			if(preg_match("/^\[([^\]]+).+\] handle=(0x[^\s]+) ([^\"]+)\"handle\(([^\)]+)\)\\\\([^\"]+)\"/",$line,$m)){
				$item=array();
				$item["line"]=$line;
				$item["class"]="C";
				$item["pid"]=trim($m[1]);
				$item["ret"]=trim($m[2]);
				$item["type"]="";
				$item["handle"]=trim($m[4]);
				$item["key"]=trim($m[5]);
				$item["value"]="";

				array_push($items,$item);
			}
			//[1928  ] [RegCreateKeyExW] handle=0x6d4 ALL_ACCESS "HKCU\Software\Microsoft\Windows\Currentversion\Run"
			if(preg_match("/^\[([^\]]+).+\] handle=(0x[^\s]+) ([^\"]+)\"([^\"]+)\"/",$line,$m)){
				$item=array();
				$item["line"]=$line;
				$item["class"]="C";
				$item["pid"]=trim($m[1]);
				$item["ret"]=trim($m[2]);
				$item["type"]="";
				$item["handle"]="";
				$item["key"]=trim($m[4]);
				$item["value"]="";

				array_push($items,$item);
			}
		}
		return $items;
	}
	static function dump_registry_queries($md5,$handle,$pid){
		$logfile=Config::$sinjector_path."/results/$md5/".Config::$logfilename;
		if(!file_exists($logfile)) return "Results not found.. bad analysis?";
		$script="scripts/reg_read.py";
		$cmd="cd ".Config::$sinjector_path." && ".Config::$python." -i $script ";
		$cmd.="results/$md5/".Config::$logfilename;
		if(strlen($pid)) $cmd.=" $pid";
		if(strlen($handle)) $cmd.=" $handle";
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return $res;
	}
	static function compactLog($md5,$showreg=true,$pid="",$showproc=true){
		$logfile=Config::$sinjector_path."/results/$md5/".Config::$logfilename;
		if(!file_exists($logfile)) return "Results not found.. bad analysis?";
		$script="scripts/parse_log.py";
		$cmd="cd ".Config::$sinjector_path." && ".Config::$python." -i $script ";
		$cmd.="results/$md5/".Config::$logfilename;
		if(strlen($pid)) $cmd.=" -pid ".intval($pid);
		if(!$showreg) $cmd.=" -reg";
		if(!$showproc) $cmd.=" -proc";
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return $res;
	}
	static function colorize($text){
		$text=preg_replace("/\033\[2m([^\033]+).+/","<span style=color:#bbb>$1</span>",$text);
		$text=preg_replace("/\033\[7m([^\033]+).+/","<span style=background:lime>$1</span>",$text);
		$text=preg_replace("/\033\[33m([^\033]+).+/","<span style=color:orange>$1</span>",$text);
		$text=preg_replace("/\033\[36;1m([^\033]+).+/","<span style=color:blue>$1</span>",$text);
		$text=preg_replace("/\033\[31m([^\033]+).+/","<span style=color:red>$1</span>",$text);
		$text=preg_replace("/\033\[35m([^\033]+).+/","<span style=color:#91b>$1</span>",$text);
		$text=preg_replace("/\033\[32m([^\033]+).+/","<span style=color:#0a0>$1</span>",$text);
		return $text;
	}
	static function fullLog($md5){
		$logfile=Config::$sinjector_path."/results/$md5/".Config::$logfilename;
		if(!file_exists($logfile)) return "Results not found.. bad analysis?";
		return file_get_contents($logfile);
	}
}
