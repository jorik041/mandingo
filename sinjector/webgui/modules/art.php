<?
class Art{
	static function submit($md5,$itype,$base64_string){
		$data = explode(',', $base64_string);
		file_put_contents("art/$md5"."-".$itype.".png",base64_decode($data[1]));
	}
	static function banner($vars){
		$js="ctx.fillStyle = '".$vars["color"]."';\n";
		$js.="ctx.textAlign = 'left';\n";
		$js.="ctx.font = '".$vars["size"]." Calibri';\n";
		$js.="ctx.lineWidth = 8;\n";
		$js.="ctx.strokeStyle = '".$vars["bordercolor"]."';\n";
		$js.="ctx.strokeText('".$vars["text"]."', ".$vars["x"].", ".($vars["y"]+$vars["size"]*1).");\n";
		$js.="ctx.fillText('".$vars["text"]."', ".$vars["x"].", ".($vars["y"]+$vars["size"]*1).");\n";
		return $js;
	}
}
