<?
if(!isset($_SESSION["crate_".$vars["binary"]])){
	$crate=Binary::compression_rate($vars["binary"]);
	$_SESSION["crate_".$vars["binary"]]=$crate;
}else{
	$crate=$_SESSION["crate_".$vars["binary"]];
}
$filesize=Binary::filesize($vars["binary"],false);
$nbytes=intval($filesize/count($crate));
$x=Common::getInteger("x");
if(strlen($x)){
$label=htmlentities(Common::getString("label"));
//if($nbytes>1024*2) $nbytes=1024*2;

$found=false;
$found_next=false;
$found_prev=false;
$x_prev=0;
$label_prev="";
$y="?";
foreach($crate as $point){
	if($found){
		$x_next=$point["x"];
		$label_next="0x".dechex($point["x"])." ".$point["label"];
		$found_next=true;
		break;
	}
	if($point["x"]==$x) {
		$found=true;
		$y=$point["y"];
		if(isset($last_point)){
			$found_prev=true;
			$x_prev=$last_point["x"];
			$label_prev="0x".dechex($last_point["x"])." ".$last_point["label"];
		}
	}
	$last_point=$point;
}
?>
<div class="modal fade" id="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:95%">
		<div class="modal-content">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="myModalLabel">
					offset: <?=$label?> (dec: <?=$x?>) <?=$nbytes?> bytes - compressed info: <?=$y?>
				</h4>
			</div>
			<div class="modal-body">
<?
	$bytes=Binary::readBinaryBytesAtOffset($vars["binary"],$x,$nbytes);
	$mode=Common::getString("mode");
	if($mode!="hex" && $mode!="ascii") $mode="ascii";
	print "<pre>";
	if($mode=="hex"){
		$html="<div style='font-size:10pt'>".Binary::hexDump($bytes)."</div>";
	}else{
		$bytes=preg_replace("/[\r\n]/"," ",$bytes);
		$html=htmlentities($bytes,ENT_DISALLOWED,"iso-8859-1");
		$html=preg_replace("/&#xFFFD;/"," ",$html);
	}
	print $html;
	print "</pre>";
?>
			</div>
			<div class="modal-footer">
			<?if($mode=="hex"){?>
				 <button type="button" class="btn btn-default" onClick="document.location='?report=<?=$vars["md5"]?>&op=results&action=graph&label?<?=$label?>&x=<?=$x?>'">Ascii</button>
			<?}else{?>
				 <button type="button" class="btn btn-default" onClick="document.location='?report=<?=$vars["md5"]?>&op=results&action=graph&label?<?=$label?>&x=<?=$x?>&mode=hex'">Hex</button>
			<?}?>
				<?if($found_prev){?>
				 <button type="button" class="btn btn-default" onClick="document.location='?report=<?=$vars["md5"]?>&op=results&action=graph&label=<?=urlencode($label_prev)?>&x=<?=$x_prev?>&mode=<?=$mode?>'">&lt; Prev</button>
				<?}?>
				<?if($found_next){?>
				 <button type="button" class="btn btn-default" onClick="document.location='?report=<?=$vars["md5"]?>&op=results&action=graph&label=<?=urlencode($label_next)?>&x=<?=$x_next?>&mode=<?=$mode?>'">Next &gt;</button>
				<?}?>
				 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
		
	</div>
</div>
<script>
$("#modal").modal();
</script>
<?}?>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/jquery.canvasjs.min.js"></script>
<?
$limit=$crate[count($crate)-1]["x"];
?>
<script type="text/javascript">
$(function () {
	var dataPoints = [];
	<?foreach($crate as $point){?>
		dataPoints.push({x:<?=$point["x"]?>,y:<?=$point["y"]?>,label:"<?="0x".dechex($point["x"])." ".$point["label"]?>"<?if(strlen($point["marker"])){?>,markerColor:"red",indexLabel:"<?=$point["marker"]?>",markerSize:10,markerType: "triangle"<?}?>});
	<?}?>
	var options = {
		theme: "theme1",
		zoomEnabled: true,
		toolTip:{
			  shared:true
		},
		axisX: {
			labelAngle: 0
		},
		axisY: {
			includeZero: false,
			gridColor: "Silver",
			tickColor: "silver"		
		},
		legend:{
			verticalAlign: "center",
			horizontalAlign: "right"
		},
		data:[{
			type: "line",
			//showInLegend: true,
			lineThickness: 2,
			markerType: "square",
			name:"info",
			dataPoints:dataPoints,
			color: "#5050a0",
			click: function(e){
				//open('modalContent.html');
				url="?report=<?=$vars["md5"]?>&op=results&action=graph"
				url+="&label="+e.dataPoint.label;
				url+="&x="+e.dataPoint.x;
				document.location=url;
				//alert(  e.dataSeries.type+ ", dataPoint { x:" + e.dataPoint.x + ", y: "+ e.dataPoint.y + " "+e.dataPoint.indexLabel+" }" );
			   }
		}]
	};

	$("#chartContainer").CanvasJSChart(options);

});
</script>
<div id="chartContainer" style="height: 100%; width: 100%;"></div>
