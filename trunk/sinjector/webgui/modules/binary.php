<?
class Binary{
	function Binary($filename){
		$this->filename=$filename;
		$this->binary=file_get_contents($filename);
	}
	function readBytesAtOffset($offset,$bytes){
		if(substr($offset,0,3)=="-0x") return Common::Error("bad","bad");
		if(substr($bytes,0,3)=="-0x") return Common::Error("bad","bad");
		if(substr($offset,0,2)=="0x") $offset=hexdec($offset);
		if(substr($bytes,0,2)=="0x") $bytes=hexdec($bytes);
		#print "Offset: $offset size: $bytes filesize: ".strlen($this->binary);
		return substr($this->binary,$offset,$bytes);
	}
	static function readBinaryBytesAtOffset($binary,$offset,$bytes){
		if(substr($offset,0,3)=="-0x") return Common::Error("bad","bad");
		if(substr($bytes,0,3)=="-0x") return Common::Error("bad","bad");
		if(substr($offset,0,2)=="0x") $offset=hexdec($offset);
		if(substr($bytes,0,2)=="0x") $bytes=hexdec($bytes);
		$in=fopen($binary,"rb");
		fseek($in,$offset);
		return fread($in,$bytes);
	}
	function headerOf($restype,$width="\x20",$height="\x20"){
		if($restype=="RT_ICON") return "\x00\x00\x01\x00\x01\x00".$width.$height."\x20\x00\x00\x00\x00\x00\x68\x05\x00\x00\x16\x00\x00\x00";
		if($restype=="RT_BITMAP") return "\x42\x4D\xC4\x0B\x00\x00\x00\x00\x00\x00\x76\x00\x00\x00";
	}
	static function magic($filename){
		@ob_start();
		system("file $filename");
        $res=ob_get_contents();
        ob_end_clean();
		$res=preg_replace("/^.+?:/","",$res);
		return $res;
	}
	static function filesize($filename,$format=true){
		$size=@filesize($filename);
		if(!$format) return $size;
		return number_format($size)." bytes (".sprintf("0x%x",$size).")";
	}
	
	static function imports($filename){
		$res=Pefile::imports($filename,true);
		$items=array();
		foreach(preg_split("/\n/",$res) as $s){
			if(!strlen($s)) continue;
			$item=array();
			$item["line"]=$s;
			list($item["library"],$item["function"],$item["address"])=preg_split("/\s/",$s);
			array_push($items,$item);
		}
		@usort($items, array(self,"cmp_imports"));
		return $items;
	}
	static function cmp_imports($a, $b)
	{
		if($b["library"]==$a["library"]) return strcmp($a["function"],$b["function"]);
		return strcmp($a["library"],$b["library"]);
	}
	static function codeSize($filename){
		$size=0;
		$sections=self::sections($filename);
		foreach($sections as $s){
			if(strstr($s["flags"],"code")) {
				$size+=hexdec($s["srd"]["value"]);
				break;
			}
		}
		return $size;
	}
	static function codeSection($filename){
		$sections=self::sections($filename);
		$section=$sections[0];
		foreach($sections as $s){
			if(strstr($s["flags"],"code")) {
				$section=$s;
				break;
			}
		}
		return $section;
	}
	static function resourcesSize($filename){
		$size=0;
		$sections=self::sections($filename);
		foreach($sections as $s){
			if($s["dir"]=="RESOURCE") {
				$size=hexdec($s["srd"]["value"]);
				break;
			}
		}
		return $size;
	}
	static function resourcesCompression($filename){
		$comp=array();
		$sections=self::sections($filename);
		foreach($sections as $s){
			if($s["dir"]=="RESOURCE") {
				$comp=self::compression_rate($filename,$s["name"]);
				break;
			}
		}
		return $comp;
	}
	static function resources($filename){
		$cache=true;
		$ressize=self::resourcesSize($filename);
		if(!$cache || !isset($_SESSION["resources_".$filename])){
			$resources=Pefile::res_offsets($filename,true);
			$_SESSION["resources_".$filename]=$resources;
		}else{
			$resources=$_SESSION["resources_".$filename];
		}
		$filesize=self::filesize($filename,false);
		$items=array();
		foreach(preg_split("/\n/",$resources) as $s){
			if(!strlen($s)) continue;
			if(preg_match("/OffsetToData/",$s) || !preg_match("/RT_/",$s)) continue;
			$item=array();
			$item["line"]=$s;
			$timedatestamp=array();
			$type=array();
			$size=array();
			$fo=array();
			list($type["value"],$item["va"],$item["od"],$size["value"],$fo["value"],$item["lang"],$timedatestamp["value"])=preg_split("/\s/",$s);
			$item["lang"]=preg_replace("/^LANG_/","",$item["lang"]);
			//intelligence for type
			$type["intelligence"]=array("class"=>"");
			if($type["value"]=="RT_RCDATA") $type["intelligence"]["class"]="warning";
			$item["type"]=$type;
			//intelligence for timedatestamp
			$timedatestamp["intelligence"]=array("class"=>"");
			if($timedatestamp["value"]=="0") $timedatestamp["intelligence"]["class"]="danger";
			$item["timedatestamp"]=$timedatestamp;
			//intelligence for size
			$size["intelligence"]=array("class"=>"");
			if($size["value"]/$filesize>=0.75) $size["intelligence"]["class"]="warning";
			if($size["value"]+$fo["value"]>$filesize) $size["intelligence"]["class"]="danger";
			$item["size"]=$size;
			//intelligence for fileoffset
			$fo["intelligence"]=array("class"=>"");
			if($fo["value"][0]=="-") $fo["intelligence"]["class"]="danger";
			$item["fo"]=$fo;
			//percentage section usage
			if($ressize) $item["per_resource"]=100*$size["value"]/$ressize;
				else $item["per_resource"]=0;
			array_push($items,$item);
		}
		return $items;
	}
	static function sections($filename){
		$filesize=self::filesize($filename,false);
		$cache=false;
		if(!$cache || !isset($_SESSION["sections_".$filename])){
			$sections=Pefile::sec_offsets($filename,true);
			$_SESSION["sections_".$filename]=$sections;
		}else{
			$sections=$_SESSION["sections_".$filename];
		}
		$items=array();
		foreach(preg_split("/\n/",$sections) as $s){
			if(!strlen($s)) continue;
			if(preg_match("/PointerToRawData/",$s)) continue;
			$item=array();
			$item["line"]=$s;
			$entropy=array();
			$prd=array();
			$srd=array();
			list($item["name"],$prd["value"],$srd["value"],$item["va"],$item["flags"],$entropy["value"],$item["dir"])=preg_split("/\s/",$s);
			$item["dir"]=preg_replace("/^IMAGE_DIRECTORY_ENTRY_/","",$item["dir"]);
			//intelligence for entropy
			$entropy["intelligence"]=array("class"=>"");
			if($entropy["value"]>=7) $entropy["intelligence"]["class"]="warning";
			if(!intval($entropy["value"])) $entropy["intelligence"]["class"]="active";
			$item["entropy"]=$entropy;
			//intelligence for pointertorawdata
			$prd["intelligence"]=array("class"=>"");
			if(hexdec($prd["value"])>$filesize) $prd["intelligence"]["class"]="danger";
			$item["prd"]=$prd;
			//intelligence for sizerawdata
			$srd["intelligence"]=array("class"=>"");
			//if(hexdec($srd["value"])+hexdec($prd["value"])>$filesize) $srd["intelligence"]["class"]="danger";
			if($filesize && hexdec($srd["value"])/$filesize>0.75) $srd["intelligence"]["class"]="warning";
			if(hexdec($srd["value"])>$filesize) $srd["intelligence"]["class"]="danger";
			$item["srd"]=$srd;
			if($filesize) $item["per_file"]=100*hexdec($srd["value"])/$filesize;
				else $item["per_file"]=0;
			//////////////////////////
			$item["complex"]=$item["per_file"]*$entropy["value"]/8;
			array_push($items,$item);
		}
		return $items;
	}
	
	static function version($filename){
		$sections=Pefile::fileinfo($filename,true);
		$items=array();
		foreach(preg_split("/\n/",$sections) as $s){
			if(!strlen($s)) continue;
			$item=array();
			$item["line"]=$s;
			list($item["name"],$item["value"])=preg_split("/:/",$s);
			$item["value"]=trim($item["value"]);
			array_push($items,$item);
		}
		return $items;
	}
	static function compression_rate($binary,$sec_name=""){
		$res=self::read_compression_rate($binary);
		$items=array();
		foreach(preg_split("/\n/",$res) as $line){
			if(preg_match("/^([^\s]+) ([^\s]+) \"([^\"]+)\" \"([^\"]*)\"/",$line,$m)){
				$item=array();
				$item["x"]=$m[1];
				$item["y"]=$m[2];
				$item["label"]=$m[3];
				$item["marker"]=$m[4];
				if(strlen($sec_name) && !strstr($item["label"],$sec_name)) continue;
				array_push($items,$item);
			}
		}
		return $items;
	}
	static function read_compression_rate($binary){
		$script="scripts/compression_rate.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		return $res;
	}
	static function strings($binary){
		$script="scripts/strings.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		return $res;
	}
	static function strings_unicode($binary){
		$script="scripts/strings_unicode.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		return $res;
	}
	static function hexDump($bytes){
		$lines="";
		$len=strlen($bytes);
		$nbytes=32;
		for($i=0;$i<$len;$i+=$nbytes){
			$line="";
			$textline="";
			for($j=0;$j<$nbytes;$j++){
				if($i+$j>=$len) break;
				$byte=ord($bytes[$i+$j]);
				$textline.=chr($byte);
				if($byte<16) $line.="0";
				$line.=sprintf("%x",$byte);
				$line.=" ";
			}
			if($j<$nbytes) {
				while($j++<$nbytes) {
					$line.="   ";
					$textline.=" ";
				}
			}
			$textline=preg_replace("/[\r\n\t]/"," ",$textline);
			$textline=htmlentities($textline,ENT_DISALLOWED,"iso-8859-1");
			$textline=preg_replace("/&#xFFFD;/"," ",$textline);
			$lines.=$line."|".$textline."|<br>";
		}
		return $lines;
	}
}
?>
