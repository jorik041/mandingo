<?
class Pefile{
	static function call($action,$binary){
		if(!strlen($action)) return "Choose an option";
		if($action=="dumpinfo") return self::dumpinfo($binary);
		if($action=="fileinfo") return self::fileinfo($binary);
		if($action=="res_offsets") return self::res_offsets($binary);
		if($action=="sec_offsets") return self::sec_offsets($binary);
		if($action=="imports") return self::imports($binary);
		if($action=="manifest") return self::manifest($binary);
		if($action=="packerid") return self::packerid($binary);
		return self::output("what?","Unknown option...");
	}
	static function timedatestamp($binary){
		$script="scripts/pe_timedatestamp.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		return intval($res);
	}
	static function packerid($binary,$raw=false){
		if(!isset($_SESSION["packerid_".$binary])){
			$script="scripts/pe_packerid.py";
			$cmd=$script." ";
			$cmd.=$binary;
			$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd." -D ".Config::$sinjector_path."/scripts/UserDB.TXT";
			@ob_start();
			system($longcmd);
		    $res=ob_get_contents();
		    ob_end_clean();
			$_SESSION["packerid_".$binary]=$res;
		}else{
			$res=$_SESSION["packerid_".$binary];
		}
		if(preg_match("/### ERROR ###/",$res)) $res="Unknown (ERROR)";
		if($raw) return $res;
		return self::output($cmd,$res);
	}
	static function compiler($binary){
		$script="scripts/pe_compiler.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
	    $res=ob_get_contents();
	    ob_end_clean();
		return $res;
	}
	static function imagebase($binary){
		$script="scripts/pe_imagebase.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
	    $res=ob_get_contents();
	    ob_end_clean();
		return $res;
	}
	static function entrypoint($binary){
		$script="scripts/pe_entrypoint.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
	    $res=trim(ob_get_contents());
	    ob_end_clean();
		return $res;
	}
	static function manifest($binary,$raw=false){
		$script="scripts/pe_manifest.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		if($raw) return $res;
		return self::output($cmd,$res);
	}
	static function sec_offsets($binary,$raw=false){
		$script="scripts/pe_sections_offsets.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		if($raw) return $res;
		return self::output($cmd,$res);
	}
	static function imports($binary,$raw=false){
		$script="scripts/pe_imports.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		if($raw) return $res;
		return self::output($cmd,$res);
	}
	static function res_offsets($binary){
		$script="scripts/pe_resource_offsets.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		return self::output($cmd,$res);
	}
	static function dumpinfo($binary){
		$script="scripts/pe_dumpinfo.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		return self::output($cmd,$res);
	}
	static function fileinfo($binary,$raw=false){
		$script="scripts/pe_fileinfo.py";
		$cmd=$script." ";
		$cmd.=$binary;
		$longcmd=Config::$python." -i ".Config::$sinjector_path."/".$cmd;
		@ob_start();
		system($longcmd);
        $res=ob_get_contents();
        ob_end_clean();
		if($raw) return $res;
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
