<?
class Mono{
	static function call($action,$binary){
		if($action=="pedump") return self::monodis_pedump($binary);
		if($action=="disassemble") return self::monodis_dissasemble($binary);
		if($action=="strings") return self::monodis_strings($binary);
		if($action=="methods") return self::monodis_methods($binary);
		if($action=="moduleref") return self::monodis_moduleref($binary);
		if($action=="presources") return self::monodis_presources($binary);
		if($action=="typedef") return self::monodis_typedef($binary);
		if($action=="fields") return self::monodis_fields($binary);
		if($action=="implmap") return self::monodis_implmap($binary);
		if($action=="param") return self::monodis_param($binary);
		if($action=="assemblyref") return self::monodis_assemblyref($binary);
		return "Unknown option...";
	}
	static function monodis_pedump($binary){
		$cmd="pedump ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function monodis_param($binary){
		$cmd="monodis --param ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function monodis_assemblyref($binary){
		$cmd="monodis --assemblyref ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function monodis_typedef($binary){
		$cmd="monodis --typedef ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function monodis_strings($binary){
		$cmd="monodis --strings ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function loadResource($binary,$id){
		#dump resources to "/tmp" 
		$cwd=getcwd();
		$cmd="cd /tmp && monodis --mresources $cwd/".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$data="";
		foreach(self::presources($binary) as $resource){
			if($resource["id"]==$id){
				$data=file_get_contents("/tmp/".$resource["name"]);
				break;
			}
		}
		return $data;
	}
	static function presources($binary){
		$res=self::monodis_presources($binary);
		$items=array();
		foreach(preg_split("/\n/",$res) as $line){
			$i=array();
			$i["line"]=$line;
			if(preg_match("/(\d+)\:\s([^\s]+)\s\(size (\d+)/",$line,$m)){
				list($i["id"],$i["name"],$i["size"])=array($m[1],$m[2],$m[3]);
				array_push($items,$i);
			}
		}
		return $items;
	}
	static function monodis_presources($binary,$raw=false){
		$cmd="monodis --presources ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		if(count(preg_split("/\n/",$res))<2) return "";
		if($raw) return $res;
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function monodis_moduleref($binary){
		$cmd="monodis --moduleref ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		if(count(preg_split("/\n/",$res))<3) return "";
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function monodis_implmap($binary){
		$cmd="monodis --implmap ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		if(count(preg_split("/\n/",$res))<3) return "";
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function monodis_fields($binary){
		$cmd="monodis --fields ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function monodis_methods($binary){
		$cmd="monodis --method ".$binary;
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		return "<pre>".htmlentities($res,ENT_QUOTES,"iso-8859-1")."</pre>";
	}
	static function monodis_dissasemble($binary){
		@ob_start();
		system("monodis $binary");
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		$res=preg_replace("/^.+?:/","",$res);
		$res=self::parse($res);
		return "<pre>".$res."</pre>";
	}
	static function parse($ilcode){
		//Commands: http://en.wikipedia.org/wiki/List_of_CIL_instructions
		//ldloc inserta el contenido del número de variable local que se encuentra en el índice pasado en la pila de evaluación
		$ilcode=preg_replace("/(ldloc\.)([^\n]+)/","<span style=color:blue>$1</span><span style=color:orange>$2</span>",$ilcode);
		//ldstr referencia de objeto a una cadena se inserta en la pila
		$ilcode=preg_replace("/(ldstr)\s+\"(.+)\"/","<span style=color:blue>$1</span> \"<span style=color:#612759>$2</span>\"",$ilcode);
		//ldc.i4 push value into stack
		$ilcode=preg_replace("/(ldc\.i4\.?)([^\n]+)?/","<span style=color:blue>$1</span><span style=color:red>$2</span>",$ilcode);
		//stloc pop value from stack into local variable .number (0-3)
		$ilcode=preg_replace("/(stloc\.)(\d+)/","<span style=color:blue>$1</span><span style=color:orange>$2</span>",$ilcode);
		//ldsfld push the value field into the stack
		$ilcode=preg_replace("/(ldsfld)([^\n]+)/","<span style=color:blue>$1</span><span style=color:red>$2</span>",$ilcode);
		//ldarg load argument n onto stack
		$ilcode=preg_replace("/(ldarg)([^\n]+)/","<span style=color:blue>$1</span><span style=color:#814779>$2</span>",$ilcode);
		//localvariables
		$ilcode=preg_replace("/(V_\d)([,\)])/","<span style=color:orange>$1</span>$2",$ilcode);
		//labels
		$ilcode=preg_replace("/(IL_[0-9a-f]+:)/i","<span style=color:#aaa>$1</span>",$ilcode);
		//others..
		$ilcode=preg_replace("/(\:\:)/","<span style=color:blue>$1</span>",$ilcode);
		$ilcode=preg_replace("/(class\s|\.locals|\.method|call(virt)?( instance)?|br\.s|bne\.un\.s)/","<span style=color:blue>$1</span>",$ilcode);
		$ilcode=preg_replace("/(xor|shr(\.un)?|shl)/","<span style=color:orange>$1</span>",$ilcode);
		$ilcode=preg_replace("/(\/\/[^\n]+)/","<span style=color:green>$1</span>",$ilcode);
		//replace < >
		$ilcode=preg_replace("/([<>])/",htmlentities("$1"),$ilcode);
		//remove error
		$ilcode=preg_replace("/Missing method .ctor in assembly[^\n]+/","",$ilcode);
		return $ilcode;
	}
}
?>
