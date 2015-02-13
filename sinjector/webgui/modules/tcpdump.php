<?
class Tcpdump{
	static function readPacket($md5,$id){
		$pcapfile=Config::$sinjector_path."/results/$md5/network.pcap";
		if(!file_exists($pcapfile)) return "Error, capture not found...";
		return self::dump_packets($md5,$id);
	}
	static function packets($md5){
		$pcapfile=Config::$sinjector_path."/results/$md5/network.pcap";
		if(!file_exists($pcapfile)) return array();
		$items=array();
		foreach(preg_split("/\n/",self::dump_packets($md5)) as $line){
			if(preg_match("/^([^\s]+) ([^\s]+) ([^\s]+) ([^\s]+) ([^\s]+) ([^\s]+) \"([^\"]*)\" \"([^\"]*)\"/",$line,$m)){
				$item=array();
				$item["line"]=$line;
				$item["proto"]=$m[1];
				$item["src"]=$m[2];
				$item["sPort"]=$m[3];
				$item["dst"]=$m[4];
				$item["dPort"]=$m[5];
				$item["len"]=$m[6];
				$item["host"]=$m[7];
				$item["data"]=$m[8];
				array_push($items,$item);
			}
		}
		return $items;
	}
	static function dump_packets($md5,$packet=""){
		$script="scripts/pcap_reader.py";
		$cmd="cd ".Config::$sinjector_path." && ".Config::$python." -i $script ";
		$cmd.="results/$md5";
		if(strlen($packet)) $cmd.=" $packet";
		@ob_start();
		system($cmd);
        $res=ob_get_contents();
        ob_end_clean();
		$res=trim($res);
		return $res;
	}
}
