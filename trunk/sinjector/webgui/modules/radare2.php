<?
class Radare2{
	static function call($app,$fun,$binary){
		if(!strlen($app)) return "Choose an option";
		if($app=="radare2/radare2") return self::r2($fun,$binary);
		if($app=="radare2/rabin2") return self::rabin2($fun,$binary);
		if($app=="radare2/rahash2") return self::rahash2($fun,$binary);
		return self::output("?","error - application not found...");
	}
	static function pdf($binary,$address){
		if(!preg_match("/^0x[a-f0-9]+$/",$address)) return "";
		$cmd="echo \"aa;af;afl;e scr.interactive=false;echo BEGIN;pdf @ $address\"|".Config::$radare2_path."/radare2 $binary 2>&1";
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=preg_replace("/\033\[0K\s*/","\n",$res);
		if(($start=strrpos($res,"BEGIN"))!==false) $res=substr($res,$start+6);
		$res=preg_replace("/\\033\[2K.*/s","",$res);
		$res=self::colorize($res);
		return $res;
	}
	static function colorize($asm){
		if(!strlen(trim($asm))) return "";
		$asm=preg_replace("/\\033\[34m/","</font><font color=#33a>",$asm);
		$asm=preg_replace("/\\033\[36m/","</font><font color=cyan>",$asm);
		$asm=preg_replace("/\\033\[0m/","</font><font color=#aaa>",$asm);
		$asm=preg_replace("/\\033\[31m/","</font><font color=red>",$asm);
		$asm=preg_replace("/\\033\[1;31m/","</font><font color=#f55>",$asm);
		$asm=preg_replace("/\\033\[32m/","</font><font color=#0a0>",$asm);
		$asm=preg_replace("/\\033\[1;32m/","</font><font color=#0d0>",$asm);
		$asm=preg_replace("/\\033\[35m/","</font><font color=pink>",$asm);
		$asm=preg_replace("/\\033\[1;35m/","</font><font color=#a50>",$asm);
		$asm=preg_replace("/\\033\[33m/","</font><font color=orange>",$asm);
		$asm=preg_replace("/\\033\[37m/","</font><font color=#aaa>",$asm);
		$asm.="</font><font color=black>";
		return $asm;
	}
	static function r2($cmds,$binary){
		if(!in_array($cmds,array("aa;af;afl"))) return self::output("!","Invalid option...");
		if($cmds=="aa;af;afl") return self::r2_functions($binary);
		return self::output("?","error - invalid options");
	}
	static function r2_functions($binary){
		#calculate code_start and code_end
		$code_section=Binary::codeSection($binary);
		$imagebase=Pefile::imagebase($binary);
		$code_start=hexdec($code_section["va"])+hexdec($imagebase);
		$code_end  =hexdec($code_section["va"])+hexdec($imagebase)+hexdec($code_section["srd"]["value"]);

		$cmd="echo \"aa;af;e scr.interactive=false;echo BEGIN;afj\"|".Config::$radare2_path."/radare2 $binary";
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$out="";
		$res=preg_replace("/.*BEGIN/s","",$res);
		$res=preg_replace("/\\033\[2K.*/s","",$res);
		$items=array();
		$lines=json_decode($res);
		if(count($lines)){
			foreach($lines as $line){
				$item=array();
				$item["address"]="0x".dechex($line->offset);
				$item["size"]=$line->size;
				$item["cc"]=$line->cc;
				$item["name"]=$line->name;
				$item["type"]=$line->type;
				$item["callrefs"]=count($line->callrefs);
				$item["datarefs"]=count($line->datarefs);
				if(hexdec($item["address"])>=$code_start && hexdec($item["address"])<=$code_end) $item["incodesec"]=1;
					else $item["incodesec"]=0;
				array_push($items,$item);
			}
		}
		@usort($items, array(self,"cmp_functions"));
		return $items;
	}
	static function cmp_functions($a, $b)
	{
		if($a["type"]==$b["type"]) return $a["address"]>$b["address"]?1:-1;
		return strcmp($a["type"],$b["type"]);
	}
	static function r2_functions_old($binary){
		$cmd="echo \"aa;af;afl\"|".Config::$radare2_path."/radare2 $binary 2>&1";
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$out="";
		$res=preg_replace("/\033\[0K\s*/","\n",$res);
		$items=array();
		foreach(preg_split("/\n/",$res) as $line){
			if(preg_match("/^0x[^\s]+/",$line) && !preg_match("/>/",$line)){
				$item=array();
				list($item["address"],$item["size"],$item["bbs"],$item["function"])=preg_split("/\s+/",$line);
				array_push($items,$item);
			}
		}
		@usort($items, array(self,"cmp_functions_old"));
		return $items;
	}
	static function cmp_functions_old($a, $b)
	{
		return $a["address"]>$b["address"]?1:-1;
	}
	static function rahash2($fun,$binary){
		if(!in_array($fun,array("-a all"))) return self::output("!","Invalid option...");
		$cmd=Config::$radare2_path."/rahash2 $fun $binary 2>&1";
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=preg_replace("/^.+?0x[^\s]+\s*/m","",$res);		
		return self::output($cmd,$res);
	}
	static function rabin2($fun,$binary){
		if(!in_array($fun,array("-l","-I","-e","-i","-R","-s","-S","-z","-zz"))) return self::output("!","Invalid option...");
		$cmd=Config::$radare2_path."/rabin2 $fun $binary 2>&1";
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();		
		return self::output($cmd,$res);
	}
	static function output($cmd,$res){
		$cmd=preg_replace("/(uploads\/|\.bin|2>&1)/","",$cmd);
		$res=preg_replace("/(uploads\/|\.bin)/","",$res);
		if(!strlen($res)) $res="(empty)";
		return "$ ".$cmd."<br><pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
}
?>
